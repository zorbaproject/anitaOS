#!/bin/bash

#gmailusername="iltuoutente"
#gmailpassword="password"
echo "Inserisci il tuo username gmail.com"
read gmailusername
echo "Inserisci la tua password di gmail.com"
read gmailpassword


sudo apt-get update
sudo apt-get install kdebase-runtime libqt4-dev build-essential g++ cmake gettext libqt4-sql-mysql kdelibs5-dev
sudo apt-get install kde-l10n-es
sudo apt-get install mysql-client mysql-server 
cd ~
git clone https://github.com/hiramvillarreal/iotpos
cd iotpos 
mkdir build
cd build

cmake .. -DCMAKE_INSTALL_PREFIX=`kde4-config --prefix`
#cmake -DCMAKE_INSTALL_PREFIX=kde4-config --prefix -DQT_QMAKE_EXECUTABLE=which qmake-qt4 -DQT_INCLUDE_DIR=/usr/include/qt4 -DCMAKE_BUILD_TYPE=Release ..
make
sudo make install
cp iotpos/src/iotposrc ~/.kde/share/config/

cd ~/iotpos/database_resources
cat iotpos_mysql.sql | mysql -u root -p

mysql -u root -p iotposdb < iotposdb.sql


sudo apt-get install python-serial python-imaging python-unidecode
cd ~/py-thermal-printer-master
sudo python printer.py

sudo echo "dtoverlay=pi3-disable-bt" >> /boot/config.txt

sed -i 's/console=ttyAMA0,115200/ /' "/boot/cmdline.txt"
sed -i 's/console=serial0,115200/ /' "/boot/cmdline.txt"


sudo apt-get install ssmtp
sudo apt-get install mailutils

cat << EOF > /etc/ssmtp/ssmtp.conf
root=postmaster
mailhub=smtp.gmail.com:587
hostname=raspberrypi
AuthUser=YourGMailUserName@gmail.com
AuthPass=YourGMailPassword
UseSTARTTLS=YES
EOF

myhost=$(hostname)
sed -i 's/raspberrypi/${myhost}/' "/etc/ssmtp/ssmtp.conf"
sed -i 's/YourGMailUserName/${gmailusername}/' "/etc/ssmtp/ssmtp.conf"
sed -i 's/YourGMailPassword/${gmailpassword}/' "/etc/ssmtp/ssmtp.conf"

sudo chmod 774 /etc/ssmtp/ssmtp.conf

sed -i 's/your_mail/${gmailusername}/' "~/iotpos/scripts/corteMail.sh"

#sudo raspi-config 
#  Advanced options
#  Serial /  Enable/Disable shell and kernel messages on the serial connection
#   Would you like a login shell to be accessible over       │
#          │ serial?  NO

sudo systemctl disable serial-getty@ttyAMA0.service

sudo reboot
  
#The default and only user and password is (without quotation marks):
#username: "admin"
#password: "linux"
#run command iotpos


#WARNING!!!: Make sure a serial TTL printer is used, NEVER CONNECT A SERIAL RS232 TO PI'S GPIO!!! even using a TTL conection, never conect the side #printer Tx pin to Rx GPIO on the pi, the Tx wire is only used for rare cases where the printer returns data to the computer. It’s left unconnected #because the printer works at 5 Volts and the Raspberry Pi at 3.3 Volts… connecting directly to the Rx pin could permanently damage the Raspberry #Pi! (The other direction is safe as-is.). 

#  Serial Data Cable Rx TO    Physical pin 8 It's also commonly known as "Serial TX" ttyAMA0.
#  Serial Data Cable Tx  DO NOT CONNECT IT TO THE GPIO RX AS SOME ARE 5 V TTL WILL DAMAGE THE RASPBERRY PI!!!
#  Serial GND from serial printer to any gpio GND could use Physical pin 6,9, 14. 
  
#-----Push buttom and LED.

#Push bottom N.O. to Physical pin 16 switch to GND.
#LED+ Physical pin 12 LED- TO any GND available.
