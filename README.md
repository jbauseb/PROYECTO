🚚 ALM System - Sistema de Gestión de Logística Aeronaútica
Este proyecto es un sistema web desarrollado para la gestión eficiente de solicitudes de materiales y rutas de transporte optimizadas. Utiliza arquitectura MVC, incluye pruebas unitarias y está preparado tanto para despliegue local como en servidores como InfinityFree.

🔧 Tecnologías utilizadas
HTML5, CSS3, PHP, JavaScript
Frameworks / Librerías:
Bootstrap 5.3
FontAwesome 5
Google Fonts
Arquitectura MVC (Modelo - Vista - Controlador)
Cliente-Servidor con sistema de rutas en PHP
cURL para llamadas a API REST
GTmetrix y PageSpeed Insights para optimización de rendimiento
🧠 Características principales
Gestión de solicitudes de materiales
Cálculo de rutas óptimas con la API REST de OSRM
Eliminación automática de solicitudes vacías (mediante trigger en la BD)
Generación de documentos PDF con mPDF
Interfaz adaptada a dispositivos
Compatible con hosting gratuito (InfinityFree)
🛠️ Problemas solucionados
file_get_contents no funciona en InfinityFree → se reemplazó por cURL
Se evitó redundancia en la base de datos mediante tabla intermedia
Se implementó un trigger SQL para borrar solicitudes sin materiales
📚 Funcionalidades pendientes y mejoras futuras
Sistema para determinar la urgencia de las solicitudes
Crear tabla de vehículos disponibles con atributos (capacidad, consumo, velocidad)
Asignación inteligente de materiales según tipo de vehículo y urgencia
📦 Instalación y configuración
✅ Requisitos previos
XAMPP 8.2.4+
Composer
Git
Extensiones PHP necesarias (php.ini):
extension=mysqli extension=pdo_mysql extension=gd extension=mbstring extension=curl extension=fileinfo extension=intl

Instalación y uso
1. Clonar el repositorio:
   git clone https://github.com/jbauseb/PROYECTO.git
2. Mover el proyecto a la carpeta htdocs de XAMPP.
3. Instalar dependencias:
   Dentro de la carpeta PROYECTO/Tarea03_4 ejecutar para instalar mPdf:
   composer install
4. La configuración del fichero PROYECTO/Tarea03_4/config.php está por defecto para despliegue local, pero dispone de las líneas de código para despliegue en InfinityFree con mi cuenta de usuario.
5. Crear la base de datos e insertar datos de muestra mediante el fichero PROYECTO/Tarea03_4/basedatos/BaseDatos.sql en phpMyAdmin.
6. Iniciar XAMPP e iniciar los servidores MySQL y Apache.
7. Abrir la aplicación a través de un navegador web a la URL http://localhost/PROYECTO/Tarea03_4
