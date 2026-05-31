<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 */

/* =============================================
   FYLCAD — Conector de Servicios Externos
   Archivo: app/Core/ServiceConnector.php
   Guía Práctica N°7 — Actividades 1, 2 y 4
   Refactorizado en Guía 11 — Extract Method

   REFACTORING APLICADO (Guía 11):
   - Extract Method: configurarOpciones() extrae la
     configuración cURL duplicada entre GET y POST,
     eliminando la repetición interna de 30 líneas.
============================================= */

require_once __DIR__ . '/Database.php';

interface IServiceConnector {
    public static function verificarServicio(): bool;
    public static function diagnostico(): array;
}

class ServiceConnector implements IServiceConnector {

    private const URL_BASE_GET  = 'https://api.open-elevation.com/api/v1/lookup';
    private const URL_BASE_POST = 'https://api.open-elevation.com/api/v1/lookup';
    private const TIMEOUT_SEG   = 10;
    private const CONNECT_SEG   = 5;
    private const MAX_PUNTOS    = 100;

    private static array $ultimaRespuesta = [
        'codigo'       => 0,
        'headers'      => [],
        'content_type' => '',
        'cuerpo'       => '',
        'error'        => '',
        'tiempo'       => 0.0,
        'metodo'       => '',
        'url'          => '',
    ];

    // ═══════════════════════════════════════════════════════════
    // MÉTODO PRINCIPAL — GET
    // ═══════════════════════════════════════════════════════════
    public static function consultarElevaciones(array $puntos): ?array {

        if (empty($puntos)) {
            self::$ultimaRespuesta['error'] = 'No se proporcionaron puntos para consultar.';
            return null;
        }

        $puntos      = array_slice($puntos, 0, self::MAX_PUNTOS);
        $ubicaciones = implode('|', array_map(
            fn($p) => "{$p['lat']},{$p['lon']}",
            $puntos
        ));
        $url       = self::URL_BASE_GET . '?locations=' . urlencode($ubicaciones);
        $respuesta = self::ejecutarGet($url);

        if ($respuesta === null) {
            return null;
        }

        return self::mapearRespuestaJson($respuesta, $puntos);
    }

    // ═══════════════════════════════════════════════════════════
    // MÉTODO POST
    // ═══════════════════════════════════════════════════════════
    public static function consultarElevacionesPost(array $puntos): ?array {

        if (empty($puntos)) {
            self::$ultimaRespuesta['error'] = 'No se proporcionaron puntos.';
            return null;
        }

        $puntos = array_slice($puntos, 0, self::MAX_PUNTOS);
        $body   = json_encode([
            'locations' => array_map(
                fn($p) => ['latitude' => $p['lat'], 'longitude' => $p['lon']],
                $puntos
            )
        ]);

        $respuesta = self::ejecutarPost(self::URL_BASE_POST, $body);

        if ($respuesta === null) {
            return null;
        }

        return self::mapearRespuestaJson($respuesta, $puntos);
    }

    // ═══════════════════════════════════════════════════════════
    // MÉTODO: Consultar un solo punto
    // ═══════════════════════════════════════════════════════════
    public static function consultarElevacionPunto(float $lat, float $lon): ?float {

        $resultado = self::consultarElevaciones([['lat' => $lat, 'lon' => $lon]]);

        if ($resultado === null || empty($resultado)) {
            return null;
        }

        return (float) $resultado[0]['elevacion'];
    }

    // ═══════════════════════════════════════════════════════════
    // PERSISTENCIA EXTERNA
    // ═══════════════════════════════════════════════════════════
    public static function persistirResultados(
        int $usuarioId,
        array $resultados,
        ?int $proyectoId = null
    ): array {

        $resultado = [
            'ok'                   => false,
            'actividad_id'         => null,
            'proyecto_actualizado' => false,
            'error'                => '',
        ];

        if (empty($resultados)) {
            $resultado['error'] = 'No hay resultados para persistir.';
            return $resultado;
        }

        try {
            $pdo         = Database::getInstance()->getConnection();
            $totalPuntos = count($resultados);
            $elevaciones = array_column($resultados, 'elevacion');
            $elevMin     = min($elevaciones);
            $elevMax     = max($elevaciones);
            $elevProm    = round(array_sum($elevaciones) / $totalPuntos, 2);

            $meta = json_encode([
                'fuente'           => 'open-elevation-api',
                'url'              => self::$ultimaRespuesta['url'],
                'metodo_http'      => self::$ultimaRespuesta['metodo'],
                'codigo_http'      => self::$ultimaRespuesta['codigo'],
                'content_type'     => self::$ultimaRespuesta['content_type'],
                'total_puntos'     => $totalPuntos,
                'elevacion_min'    => $elevMin,
                'elevacion_max'    => $elevMax,
                'elevacion_prom'   => $elevProm,
                'tiempo_respuesta' => self::$ultimaRespuesta['tiempo'],
                'timestamp'        => date('Y-m-d H:i:s'),
                'puntos'           => array_slice($resultados, 0, 10),
            ], JSON_UNESCAPED_UNICODE);

            $descripcion = "WS Open-Elevation: {$totalPuntos} puntos consultados — "
                         . "Z_min={$elevMin}m, Z_max={$elevMax}m, Z_prom={$elevProm}m";

            $stmt = $pdo->prepare("
                INSERT INTO actividad
                    (usuario_id, proyecto_id, tipo, descripcion, meta)
                VALUES
                    (:uid, :pid, 'archivo_exportado', :desc, :meta)
            ");
            $stmt->execute([
                ':uid'  => $usuarioId,
                ':pid'  => $proyectoId,
                ':desc' => $descripcion,
                ':meta' => $meta,
            ]);

            $resultado['actividad_id'] = (int) $pdo->lastInsertId();
            $resultado['ok']           = true;

            if ($proyectoId !== null) {
                $stmtProy = $pdo->prepare("
                    UPDATE proyectos
                    SET
                        cota_min       = :cota_min,
                        cota_max       = :cota_max,
                        desnivel       = :desnivel,
                        actualizado_en = NOW()
                    WHERE id = :id
                      AND usuario_id = :uid
                ");
                $stmtProy->execute([
                    ':cota_min' => $elevMin,
                    ':cota_max' => $elevMax,
                    ':desnivel' => round($elevMax - $elevMin, 2),
                    ':id'       => $proyectoId,
                    ':uid'      => $usuarioId,
                ]);
                $resultado['proyecto_actualizado'] = $stmtProy->rowCount() > 0;
            }

        } catch (PDOException $e) {
            $resultado['ok']    = false;
            $resultado['error'] = 'Error PDO al persistir: ' . $e->getMessage();
            self::$ultimaRespuesta['error'] = $resultado['error'];
        }

        return $resultado;
    }

    // ═══════════════════════════════════════════════════════════
    // VERIFICAR DISPONIBILIDAD
    // ═══════════════════════════════════════════════════════════
    public static function verificarServicio(): bool {
        $resultado = self::consultarElevacionPunto(7.8939, -72.5078);
        return $resultado !== null;
    }

    // ═══════════════════════════════════════════════════════════
    // DIAGNÓSTICO
    // ═══════════════════════════════════════════════════════════
    public static function diagnostico(): array {
        return self::$ultimaRespuesta;
    }

    // ═══════════════════════════════════════════════════════════
    // REFACTORING — Extract Method (Guía 11)
    //
    // ANTES: ejecutarGet() y ejecutarPost() tenían ~30 líneas
    // idénticas de configuración cURL (URL, timeouts, SSL,
    // headers base, redirecciones) duplicadas entre sí.
    //
    // DESPUÉS: este método centraliza todas las opciones
    // compartidas. Cada método solo agrega lo exclusivo:
    // POST agrega CURLOPT_POST, CURLOPT_POSTFIELDS y
    // Content-Length. GET no agrega nada extra.
    //
    // Técnica: Extract Method — Martin Fowler (2018)
    // Fuente: https://refactoring.guru/extract-method
    //
    // @param string $url          URL del endpoint
    // @param array  $headersExtra Headers adicionales específicos
    // @return array               Opciones cURL base configuradas
    // ═══════════════════════════════════════════════════════════
    private static function configurarOpciones(
        string $url,
        array $headersExtra = []
    ): array {

        $headersBase = [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: FYLCAD/1.0 (Topografia Digital — FESC 2026)',
        ];

        return [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => self::TIMEOUT_SEG,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_SEG,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => array_merge($headersBase, $headersExtra),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // GET con cURL — usa configurarOpciones() (Extract Method)
    // ═══════════════════════════════════════════════════════════
    private static function ejecutarGet(string $url): ?string {

        $inicio = microtime(true);
        self::$ultimaRespuesta['metodo'] = 'GET';
        self::$ultimaRespuesta['url']    = $url;

        $headersRecibidos = [];
        $ch               = curl_init();

        // REFACTORING: configuración centralizada en configurarOpciones()
        $opciones = self::configurarOpciones($url);
        $opciones[CURLOPT_HEADERFUNCTION] = function ($_curl, $header) use (&$headersRecibidos) {
            $len    = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) { return $len; }
            $headersRecibidos[strtolower(trim($header[0]))] = trim($header[1]);
            return $len;
        };

        curl_setopt_array($ch, $opciones);

        $cuerpo     = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl  = curl_error($ch);
        $tiempoMs   = round((microtime(true) - $inicio) * 1000, 2);

        curl_close($ch);

        self::$ultimaRespuesta = array_merge(self::$ultimaRespuesta, [
            'codigo'       => $codigoHttp,
            'headers'      => $headersRecibidos,
            'content_type' => $headersRecibidos['content-type'] ?? '',
            'cuerpo'       => is_string($cuerpo) ? substr($cuerpo, 0, 500) : '',
            'tiempo'       => $tiempoMs,
        ]);

        if ($cuerpo === false || !empty($errorCurl)) {
            self::$ultimaRespuesta['error'] =
                "Error cURL (código {$codigoHttp}): {$errorCurl}";
            return null;
        }

        switch ($codigoHttp) {
            case 200:
                self::$ultimaRespuesta['error'] = '';
                return $cuerpo;
            case 404:
                self::$ultimaRespuesta['error'] =
                    "Servicio no encontrado (HTTP 404).";
                return null;
            case 429:
                self::$ultimaRespuesta['error'] =
                    "Límite de peticiones excedido (HTTP 429).";
                return null;
            case 500:
                self::$ultimaRespuesta['error'] =
                    "Error interno del servidor externo (HTTP 500).";
                return null;
            case 503:
                self::$ultimaRespuesta['error'] =
                    "Servicio no disponible temporalmente (HTTP 503).";
                return null;
            default:
                self::$ultimaRespuesta['error'] =
                    "Respuesta inesperada del servicio (HTTP {$codigoHttp}).";
                return null;
        }
    }

    // ═══════════════════════════════════════════════════════════
    // POST con cURL — usa configurarOpciones() (Extract Method)
    // ═══════════════════════════════════════════════════════════
    private static function ejecutarPost(string $url, string $body): ?string {

        $inicio = microtime(true);
        self::$ultimaRespuesta['metodo'] = 'POST';
        self::$ultimaRespuesta['url']    = $url;

        $headersRecibidos = [];
        $ch               = curl_init();

        // REFACTORING: configuración centralizada en configurarOpciones()
        // Solo se agregan las opciones exclusivas de POST
        $opciones = self::configurarOpciones($url, [
            'Content-Length: ' . strlen($body),
        ]);
        $opciones[CURLOPT_POST]           = true;
        $opciones[CURLOPT_POSTFIELDS]     = $body;
        $opciones[CURLOPT_HEADERFUNCTION] = function ($_curl, $header) use (&$headersRecibidos) {
            $len    = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) { return $len; }
            $headersRecibidos[strtolower(trim($header[0]))] = trim($header[1]);
            return $len;
        };

        curl_setopt_array($ch, $opciones);

        $cuerpo     = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl  = curl_error($ch);
        $tiempoMs   = round((microtime(true) - $inicio) * 1000, 2);

        curl_close($ch);

        self::$ultimaRespuesta = array_merge(self::$ultimaRespuesta, [
            'codigo'       => $codigoHttp,
            'headers'      => $headersRecibidos,
            'content_type' => $headersRecibidos['content-type'] ?? '',
            'cuerpo'       => is_string($cuerpo) ? substr($cuerpo, 0, 500) : '',
            'tiempo'       => $tiempoMs,
        ]);

        if ($cuerpo === false || !empty($errorCurl)) {
            self::$ultimaRespuesta['error'] =
                "Error cURL POST (código {$codigoHttp}): {$errorCurl}";
            return null;
        }

        if ($codigoHttp !== 200) {
            self::$ultimaRespuesta['error'] =
                "Error HTTP {$codigoHttp} en respuesta POST del servicio.";
            return null;
        }

        self::$ultimaRespuesta['error'] = '';
        return $cuerpo;
    }

    // ═══════════════════════════════════════════════════════════
    // Mapear respuesta JSON → objetos FYLCAD
    // ═══════════════════════════════════════════════════════════
    private static function mapearRespuestaJson(
        string $jsonString,
        array $puntosOrig
    ): ?array {

        $datos = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::$ultimaRespuesta['error'] =
                'Respuesta JSON inválida: ' . json_last_error_msg();
            return null;
        }

        if (!isset($datos['results']) || !is_array($datos['results'])) {
            self::$ultimaRespuesta['error'] =
                'Estructura de respuesta inesperada: falta la clave "results".';
            return null;
        }

        $resultadoMapeado = [];
        foreach ($datos['results'] as $i => $punto) {
            $resultadoMapeado[] = [
                'lat'       => (float) ($punto['latitude']  ?? ($puntosOrig[$i]['lat'] ?? 0)),
                'lon'       => (float) ($punto['longitude'] ?? ($puntosOrig[$i]['lon'] ?? 0)),
                'elevacion' => (float) ($punto['elevation'] ?? 0),
                'nombre'    => $puntosOrig[$i]['nombre'] ?? "Punto {$i}",
                'fuente'    => 'open-elevation',
            ];
        }

        return $resultadoMapeado;
    }
}
