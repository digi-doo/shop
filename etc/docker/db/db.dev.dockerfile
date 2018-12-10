# Root Dockerfile
FROM mysql:latest

# Author
MAINTAINER Jan Czernin <jan.czernin@autodevelo.cz>

# Copy database dump to entrypoint and execute it into defined MYSQL_DATABASE in docker-compose.yml
COPY sshop_dev.sql.gz /docker-entrypoint-initdb.d/