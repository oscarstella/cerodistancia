# CeroDistancia

Plataforma web para creación y gestión de tiendas online locales sin comisiones / contacto directo por WhatsApp.

## Requisitos
- Servidor web Apache con `mod_rewrite` habilitado (PHP 7.4+ u 8.x)
- Base de datos MySQL / MariaDB

## Instalación y Configuración

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/oscarstella/cerodistancia.git
   ```

2. Configurar la base de datos:
   - Crear una copia de `public_html/backend/conexion.example.php` y renombrarla a `conexion.php`:
     ```bash
     cp public_html/backend/conexion.example.php public_html/backend/conexion.php
     ```
   - Editar `conexion.php` con las credenciales de tu base de datos MySQL.

3. Configurar Mercado Pago:
   - Crear una copia de `public_html/backend/config_mp.example.php` y renombrarla a `config_mp.php`:
     ```bash
     cp public_html/backend/config_mp.example.php public_html/backend/config_mp.php
     ```
   - Editar `config_mp.php` e ingresar tu Access Token de Mercado Pago.

4. Importar la estructura de la base de datos e iniciar el servidor web apuntando a `public_html`.
