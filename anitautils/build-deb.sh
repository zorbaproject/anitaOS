#!/bin/bash

get_abs_filename() {
  # $1 : relative filename
  filename=$1
  parentdir=$(dirname "${filename}")

  if [ -d "${filename}" ]; then
      echo "$(cd "${filename}" && pwd)"
  elif [ -d "${parentdir}" ]; then
    echo "$(cd "${parentdir}" && pwd)/$(basename "${filename}")"
  fi
}

if [[ ! -z $1 ]]; then
pkgdir="$1"
else
echo "build-deb.sh ../myprogram ./myprogram-oldversion"
exit 0
fi

oldpkgdir=""
if [[ ! -z $2 ]]; then
oldpkgdir=$(get_abs_filename "$2")
else
echo -n "Is there a previous version?"
read tmpoldpkgdir
oldpkgdir=$(get_abs_filename "$tmpoldpkgdir")
fi
echo $oldpkgdir

echo -n "Program name?"
read pkgname
if [[ -z $pkgname ]]; then
exit 0
fi

echo -n "Program version? (es: 0.1)"
read pkgver
if [[ -z $pkgver ]]; then
exit 0
fi

echo -n "Your email?"
read ymail
if [[ -z $ymail ]]; then
exit 0
fi

curdir=$(pwd)
rm -R $curdir/$pkgname-$pkgver
mkdir $curdir/$pkgname-$pkgver
cp -R $pkgdir/* $curdir/$pkgname-$pkgver/
cd $curdir/$pkgname-$pkgver

qmake

echo -n "Your name?"
read yname
if [[ -z $pkgname ]]; then
exit 0
fi
export DEBFULLNAME="$yname"

debuild clean

dh_make -e $ymail -s --createorig

nano "debian/changelog"

if [[ ! -z $oldpkgdir ]]; then
if [ -f "$oldpkgdir/debian/control" ]; then
cp "$oldpkgdir/debian/control" "debian/control"
fi
fi

nano "debian/control"

debuild -S -sa

debuild binary

