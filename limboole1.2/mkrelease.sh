#!/bin/sh
version=`cat VERSION`
base="limboole$version"
dir=/tmp/$base
rm -rf $dir
mkdir $dir
cp -a \
  BUILD \
  VERSION \
  README \
  NEWS \
  configure.sh \
  makefile.in \
  *.c \
  $dir
mkdir $dir/log
cp -a log/*.in log/*.out $dir/log
cd /tmp/
tar zcf $base.tar.gz $base/
ls -l /tmp/$base.tar.gz
