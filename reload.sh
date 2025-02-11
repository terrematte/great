#!/usr/bin/env bash
# ----------------------------------------------------------------------------------
# Script      : reload.sh
# Description : reload the php thigns to us.
# Version     : 1.0
# Author      : JJoaoll <joaoduos@gmail.com>
# Date        : 02/11/2025
# Licence     : GNU/GPL v3.0
# ----------------------------------------------------------------------------------
# Usage: ./reload.sh
# ----------------------------------------------------------------------------------

# Da raiz do projeto

# Gerar liblimmat.a
cd limmat
CC=gcc ./configure
make clean
make

# Volta a raiz do projeto
cd ..

# Gerar binario do limboole
cd limboole-0.2/
make clean
make

# Volta a raiz do projeto

cd ..

# Executa o projeto

php -S 127.0.0.1:8000
echo "
cabou!!"
