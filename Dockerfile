# Usa a imagem oficial do PHP com servidor web Apache
FROM php:8.2-apache

# Instala a extensão do MySQL necessária para conectar ao banco de dados
RUN docker-php-ext-install pdo pdo_mysql

# Habilita o sistema de rotas amigáveis 
RUN a2enmod rewrite

# Define que o sistema vai trabalhar dentro da pasta html
WORKDIR /var/www/html

# Copia o código do Preditiv.ia para dentro do servidor
COPY . /var/www/html/

# Configura o Apache para ler a pasta "public"
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instala o Composer para gerenciar as dependências
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expõe a porta 80 para a internet
EXPOSE 80