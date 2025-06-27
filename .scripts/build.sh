#!/bin/bash

basedir=$(dirname "$(realpath "$0")")
cd "$basedir"/.. || exit 1

php vendor-local/box.phar compile