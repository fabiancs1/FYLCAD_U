# FYLCAD — Guía de despliegue en VPS

## Requisitos del servidor
- PHP 8.2+
- MySQL 8.0+ o MariaDB 10.4+
- Apache 2.4+ o Nginx
- Extensiones PHP: pdo, pdo_mysql, curl, json, mbstring

## Pasos para desplegar

### 1. Subir archivos
Sube todo el contenido de esta carpeta a `/var/www/html/fylcad` en la VPS.

### 2. Configurar entorno
Edita el archivo `config/env.php` con tus credenciales reales:
- DB_USER → tu usuario de MySQL
- DB_PASS → tu contraseña de MySQL
- APP_URL → tu dominio (ej: https://fylcad.com)

### 3. Importar base de datos
```bash
mysql -u tu_usuario -p fylcad_db < fylcad_db.sql
```

### 4. Permisos de carpetas
```bash
chmod 755 uploads/avatares
chmod 755 PNG/PDF/guardar
```

### 5. SSL (HTTPS)
```bash
apt install certbot python3-certbot-apache
certbot --apache -d tudominio.com
```

### 6. Verificar
- Abre tu dominio en el navegador
- Prueba login, registro y carga de proyectos
