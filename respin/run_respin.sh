#!/bin/bash

/usr/bin/respin dist cdfs
cp /home/respin/anitaOS/respin/splash.png /home/respin/respin/ISOTMP/isolinux/splash.png
myversion=$(cat /etc/os-release | grep VERSION_ID | sed 's/VERSION\_ID\=//g' | sed 's/\"//g' | sed 's/\./-/g' )
/usr/bin/respin dist iso Anita_$myversion.iso
