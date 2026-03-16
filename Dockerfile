# Dockerfile para proyecto PHP
FROM php:8.1-apache

# Copia el código fuente al contenedor
COPY . /var/www/html/

# Habilita mod_rewrite (útil para muchos proyectos PHP)
RUN a2enmod rewrite

# Permisos para el directorio
RUN chown -R www-data:www-data /var/www/html

# Instala extensiones comunes de PHP (puedes agregar más si lo necesitas)

# Instala extensiones comunes de PHP, nmap, python3, python3-venv, curl
RUN apt-get update \
	&& apt-get install -y nmap python3 python3-venv curl \
	&& docker-php-ext-install mysqli \
	# Crea el entorno virtual en /var/www/html/venv
	&& python3 -m venv /var/www/html/venv \
	# Instala pip en el venv usando get-pip.py
	&& curl -sS https://bootstrap.pypa.io/get-pip.py -o /tmp/get-pip.py \
	&& /var/www/html/venv/bin/python /tmp/get-pip.py \
	&& rm /tmp/get-pip.py \
	# Actualiza pip en el venv
	&& /var/www/html/venv/bin/pip install --upgrade pip

# Exponer el puerto 80
EXPOSE 80

# El contenedor se inicia con Apache
CMD ["apache2-foreground"]
