# This scripts changes the volumes to be writable for the www-data user


#82 is the user-id of www-data inside the container
docker run -v ${DOCKER_PREFIX}_upload:/upload --rm alpine sh -c "chown -R 82 /upload"
