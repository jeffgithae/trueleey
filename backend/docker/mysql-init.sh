#!/bin/bash
# Runs once when the local MySQL volume is first created (docker-compose.local.yml).
# MYSQL_DATABASE creates the VP database; this adds the Beauty Express stand-in.
set -e

mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS beauty_express CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON beauty_express.* TO '$MYSQL_USER'@'%';
FLUSH PRIVILEGES;
SQL
