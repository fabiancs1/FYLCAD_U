# FYLCAD — Plataforma de Topografía Digital

> **© 2026 Fabian Eduardo Rodriguez Hernandez / Emmely Lorena Gutierrrez Gutierrez — Todos los derechos reservados.**

## ⚠️ Aviso Legal

Este proyecto es propiedad intelectual exclusiva de **Fabian Eduardo Rodriguez Hernandez**.

Queda **estrictamente prohibido**:
- Copiar o reutilizar el código sin autorización escrita del autor
- Usar este proyecto con fines comerciales
- Distribuirlo o modificarlo sin permiso expreso

Este repositorio se comparte únicamente con fines académicos.
Ver archivo [LICENSE](./LICENSE) para más detalles.

---

## ¿Qué es FYLCAD?

Plataforma SaaS de topografía digital que permite:
- Procesar coordenadas topográficas desde archivos CSV
- Visualizar terrenos en 3D con triangulación TIN
- Generar curvas de nivel y análisis de corte/relleno
- Calcular cotizaciones de obra civil
- Gestionar proyectos topográficos

## Tecnologías

- **Backend:** PHP 8.2, MySQL, PDO
- **Frontend:** HTML5, CSS3, JavaScript (Canvas 2D)
- **Protocolos:** SOAP, WebSockets, XML/XSD/XSLT
- **Asistente:** Chatbot propio sin API externa

  Repositorio: https://github.com/fabiancs1/FYLCAD_U
Análisis de calidad: https://sonarcloud.io/project/overview?id=fabiancs1_FYLCAD

## Requisitos Previos

PHP 8.1+ con extensiones pdo, pdo_mysql, curl y sockets
MySQL 8.0+
Apache 2.4 con mod_rewrite habilitado
XAMPP o Laragon para entorno local

No requiere Composer ni npm.

## Instalación

1. Clonar el repositorio
git clone https://github.com/fabiancs1/FYLCAD_U.git}
2. Importar la base de datos
mysql -u root -p < fylcad_db.sql
O desde phpMyAdmin: crear base de datos fylcad_db → Importar → seleccionar fylcad_db.sql.
3. Configurar conexión en app/Core/Database.php:
$host   = 'localhost';
$dbname = 'fylcad_db';
$user   = 'root';
$pass   = '';
4. Colocar la carpeta dentro de htdocs/ y acceder a http://localhost/FYLCAD_U/
   

## Autor

**Fabian Eduardo Rodriguez Hernandez **  
Ingeniería de Sistemas — 2026  
