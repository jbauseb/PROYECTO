#!/bin/bash
# Se asume que este fichero tiene permisos de ejecución 
# chmod +x install_linux.sh


# Paso 1: Clonar el repositorio
git clone https://github.com/jbauseb/PROYECTO.git

# Paso 2: Mover el proyecto a la carpeta htdocs de XAMPP
cp -r PROYECTO /opt/lampp/htdocs/PROYECTO

# Paso 3: Dar permisos
chmod -R 755 /opt/lampp/htdocs/PROYECTO

# Paso 4: Instalar dependencias con Composer
cd /opt/lampp/htdocs/PROYECTO/Tarea03_4
composer install

# Paso 5: Crear la base de datos e insertar datos de muestra
# Se asume que está MySQL instalado y funcionando con XAMPP y que tenemos un usuario root sin password
mysql -u root -p < /opt/lampp/htdocs/PROYECTO/Tarea03_4/basedatos/BaseDatos.sql

# Paso 6: Iniciar XAMPP
sudo /opt/lampp/lampp start

# Paso 7: Abrir la aplicación en el navegador
xdg-open http://localhost/PROYECTO/Tarea03_4
