#!/bin/bash
#Con questo script si trasforma Kubuntu in AnitaOS

echo "Rispondi alle domande, tra poco installeremo tutto il necessario."
thisscript=$(readlink -f "$0")
scriptfolder=$(dirname "$thisscript")
echo $scriptfolder

sudo apt-get install git build-essential


echo -n "Questa è una installazione Desktop o Server? (Nel dubbio premere Invio)"
read answer
server=$(echo "$answer" | grep -i "^s")

if [ ! $server == "" ] ; then

echo "Hai scelto SERVER"

./server_install.sh

else
echo "Hai scelto DESKTOP"
#exit 0

./desktop_install.sh

fi
