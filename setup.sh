# This scripts changes the volumes to be writable for the www-data user


#82 is the user-id of www-data inside the container
docker-compose run --rm -u root fpm sh -c "chown -R 82 .."
docker-compose run --rm fpm composer install
