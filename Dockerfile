FROM php:7.2-fpm
COPY . /usr/app
WORKDIR /usr/app
CMD [ "php", "./src/main.php" ]