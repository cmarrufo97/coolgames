#!/bin/sh

if [ "$1" = "travis" ]; then
    psql -U postgres -c "CREATE DATABASE coolgames_test;"
    psql -U postgres -c "CREATE USER coolgames PASSWORD 'coolgames' SUPERUSER;"
else
    sudo -u postgres dropdb --if-exists coolgames
    sudo -u postgres dropdb --if-exists coolgames_test
    sudo -u postgres dropuser --if-exists coolgames
    sudo -u postgres psql -c "CREATE USER coolgames PASSWORD 'coolgames' SUPERUSER;"
    sudo -u postgres createdb -O coolgames coolgames
    sudo -u postgres psql -d coolgames -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    sudo -u postgres createdb -O coolgames coolgames_test
    sudo -u postgres psql -d coolgames_test -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    LINE="localhost:5432:*:coolgames:coolgames"
    FILE=~/.pgpass
    if [ ! -f $FILE ]; then
        touch $FILE
        chmod 600 $FILE
    fi
    if ! grep -qsF "$LINE" $FILE; then
        echo "$LINE" >> $FILE
    fi
fi
