@REM Es requisito que el usuario tenga XAMPP, MySQL y Composer instalados y configurados previamente    .

@echo off
REM Paso 1: Clonar el repositorio
git clone https://github.com/jbauseb/PROYECTO.git

REM Paso 2: Mover el proyecto a la carpeta htdocs de XAMPP
xcopy /E /I PROYECTO "C:\xampp\htdocs\PROYECTO"

REM Paso 3: Instalar dependencias con Composer
cd C:\xampp\htdocs\PROYECTO\Tarea03_4
composer install

REM Paso 4: Crear la base de datos e insertar datos de muestra
REM Se asume que está MySQL instalado y configurado con phpMyAdmin en XAMPP, y que tenemos un usuario root sin password
mysql -u root -p < C:\xampp\htdocs\PROYECTO\Tarea03_4\basedatos\BaseDatos.sql

REM Paso 5: Iniciar XAMPP (si no está iniciado)
start "" "C:\xampp\xampp-control.exe"

REM Paso 6: Abrir la aplicación en el navegador
start http://localhost/PROYECTO/Tarea03_4
