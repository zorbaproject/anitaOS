#!/bin/bash

swapoff -a
rm -f /swapfile
sed -i "s/.*swapfile.*//g" /etc/fstab
/usr/bin/respin clean
/usr/bin/respin dist cdfs
cp /home/respin/anitaOS/respin/splash.png /home/respin/respin/ISOTMP/isolinux/splash.png
#mkdir -p /home/respin/respin/dummysys/home/custom/
#cp /usr/share/applications/ubiquity.desktop /home/respin/respin/dummysys/home/custom/Install.desktop
myversion=$(cat /etc/os-release | grep VERSION_ID | sed 's/VERSION\_ID\=//g' | sed 's/\"//g' | sed 's/\./-/g' )
/usr/bin/respin dist iso Anita_$myversion.iso
