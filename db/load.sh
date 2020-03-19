#!/bin/sh

BASE_DIR=$(dirname "$(readlink -f "$0")")
if [ "$1" != "test" ]; then
    psql -h localhost -U coolgames -d coolgames < $BASE_DIR/coolgames.sql
fi
psql -h localhost -U coolgames -d coolgames_test < $BASE_DIR/coolgames.sql
