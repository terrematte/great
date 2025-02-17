#!/bin/sh

picosat=unknown
lingeling=unknown
depqbf=unknown

debug=no

die () {
  echo "*** configure.sh: $*" 1>&2
  exit 1
}

msg () {
  echo "[configure.sh] $*"
}

while [ $# -gt 0 ]
do
  case $1 in
    -h)
    
cat <<EOF
usage: configure.sh [-h][-g]
-h             print this command line option summary
-g             compile with debugging information
--picosat      only compile with PicoSAT back-end
--lingeling    only compile with Lingeling back-end
--depqbf       only compile with DepQBF back-end
EOF
exit 0
;;
    -g) debug=yes;;
    --picosat) picosat=yes;;
    --lingeling) lingeling=yes;;
    --depqbf) depqbf=yes;;
    *) die "invalid option '$1' (try '-h')";;
  esac
  shift
done

CC=gcc
CFLAGS="$CFLAGS -Wall"

if [ $debug = yes ]
then
  CFLAGS="-g"
else
  CFLAGS="-O3 -DNDEBUG"
fi

CFLAGS="$CFLAGS -DVERSION=`cat VERSION`"

LIBS=""

found=no


if [ $lingeling = yes ]
then
  if [ -d ../lingeling -a \
       -f ../lingeling/lglib.h -a \
       -f ../lingeling/liblgl.a ]
  then
    DEPS="../lingeling/lglib.h ../lingeling/liblgl.a"
    LIBS="-L../lingeling -llgl -lm"
    CFLAGS="$CFLAGS -DLIMBOOLE_USE_LINGELING -I../lingeling"
    msg "using Lingeling in '../lingeling'"
    found=yes
  else
    msg "no Lingeling found (searched '../lingeling')"
  fi
fi

if [  $picosat = yes ]
then
  if [ -d ../picosat -a \
       -f ../picosat/picosat.h -a \
       -f ../picosat/libpicosat.a ]
  then
    DEPS="$DEPS ../picosat/picosat.h ../picosat/libpicosat.a"
    LIBS="$LIBS -L../picosat -lpicosat"
    CFLAGS="$CFLAGS -DLIMBOOLE_USE_PICOSAT -I../picosat"
    msg "using PicoSAT in '../picosat'"
    found=yes
  else
    msg "no PicoSAT found (searched '../picosat')"
  fi
fi


if [  $depqbf = yes ]
then 
  if [ -d ../depqbf -a \
       -f ../depqbf/qdpll.h -a \
       -f ../depqbf/libqdpll.a ]
  then
    DEPS="$DEPS ../depqbf/qdpll.h ../depqbf/libqdpll.a"
    LIBS="$LIBS -L../depqbf -lqdpll"
    CFLAGS="$CFLAGS -DLIMBOOLE_USE_DEPQBF -I../depqbf"
    msg "using Depqbf in '../depqbf'"
    found=yes
  else
    msg "no DepQBF found (searched '../depqbf')"
  fi
fi

[ $found = no ] && die "no SAT solver found"

msg "LIBS=$LIBS"
msg "DEPS=$DEPS"
msg "CC=$CC"
msg "CFLAGS=$CFLAGS"

rm -f makefile
sed -e "s,@CC@,$CC," \
    -e "s,@CFLAGS@,$CFLAGS," \
    -e "s,@LIBS@,$LIBS," \
    -e "s,@DEPS@,$DEPS," \
makefile.in > makefile
