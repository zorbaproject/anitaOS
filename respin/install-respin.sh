#/bin/bash
sudo dpkg -i respin_2.0.6-0-76~ubuntu18.04.1_amd64.deb
sudo apt-get install -f
cp respin /usr/bin/respin
cp respin-skelcopy /usr/bin/respin-skelcopy
