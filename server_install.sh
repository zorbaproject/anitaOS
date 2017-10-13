#!/bin/bash
#Con questo script si trasforma Kubuntu in AnitaOS

echo "Hai scelto SERVER"

serverslist=$(/usr/bin/dialog --stdout  --title " Tipo di server" --clear --checklist "Che tipo di server vuoi realizzare? \n(spostati con freccia su e giù, scegli con Spazio, conferma con Invio)" 20 30 10 "LTSP" "LTSP" off "stampa" "Server di stampa" off "scanner" "Server per scanner" off "owncloud" "Memorizzazione file" off "lool" "Libre Office On Line" off "LAMP" "Database" off "apache" "Web" off "iotpos" "POS - registratore di cassa" off)


if [ ! $? -eq 0 ]; then
        echo "cancel selected"
        exit 0
fi

sudo apt-get update

# TODO: secondo livello definito da inputbox, primo livello autorilevato
dominio='nextcloud\\.lan'

for opzioneserver in $serverslist
do
   echo "Hai scelto: $opzioneserver"
   
   if [ $opzioneserver == "LTSP" ] ; then
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "stampa" ] ; then
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "scanner" ] ; then
   sudo apt-get install sane-utils
   fi
   
   if [ $opzioneserver == "owncloud" ] ; then
   sh -c "echo 'deb http://download.owncloud.org/download/repositories/stable/Debian_8.0/ /' > /etc/apt/sources.list.d/owncloud.list"
   sudo apt-get update && sudo apt-get install owncloud
   fi
   
   if [ $opzioneserver == "lool" ] ; then
   sh -c "echo 'deb http://download.owncloud.org/download/repositories/stable/Debian_8.0/ /' > /etc/apt/sources.list.d/owncloud.list"
   sudo apt-get update && sudo apt-get install owncloud
   sudo apt-get install curl apt-transport-https ca-certificates software-properties-common
   sudo curl -fsSL https://yum.dockerproject.org/gpg | apt-key add -
   echo "deb https://apt.dockerproject.org/repo/ debian-$(lsb_release -cs) main" >> /etc/apt/sources.list
   sudo apt-get update
   sudo apt-get -y install docker-engine
   #sudo docker pull libreoffice/online:master
   #docker run -t -d -p 127.0.0.1:9980:9980 -e "domain=localhost" --cap-add MKNOD libreoffice/online:master
   # Collabora probabilmente funziona meglio di LibreOffice On Line, per ora usiamo questo
   sudo docker pull collabora/code
   sudo docker run -t -d -p 127.0.0.1:9980:9980 -e 'domain=office\\.$dominio' --restart always --cap-add MKNOD collabora/code
   sudo apt-get install apache2
   sudo a2enmod proxy
   sudo a2enmod proxy_wstunnel
   sudo a2enmod proxy_http
   sudo a2enmod ssl
#aggiungiamo un VirtualHost
   sudo nano /etc/apache2/sites-available/collabora.conf
#Il contenuto del file dovrà essere simile al punto 2 della pagina https://nextcloud.com/collaboraonline/, avendo cura di modificare il nome di dominio inserendo quello che si è scelto. Il sito può poi essere abilitato con 
   sudo a2ensite collabora.conf
   sudo service apache2 restart
#Sempre seguendo la pagina di NextCloud, è possibile integrare Collabora nell’interfaccia di NextCloud e OwnCloud.
   fi
   
   if [ $opzioneserver == "LAMP" ] ; then
   sudo apt-get -y install apache2 mysql-server php5-mysql php5 libapache2-mod-php5 php5-mcrypt
   sudo mysql_install_db
   sudo mysql_secure_installation
   sudo systemctl restart apache2
   sudo apt-get install phpmyadmin php-mbstring php-gettext
   sudo phpenmod mcrypt
   sudo phpenmod mbstring
   sudo systemctl restart apache2
   echo '<?php phpinfo(); ?>' | sudo tee /var/www/html/info.php
   fi
   
   if [ $opzioneserver == "apache" ] ; then
   sudo apt-get -y install apache2 php5 libapache2-mod-php5 php5-mcrypt
   sudo systemctl restart apache2
   echo '<?php phpinfo(); ?>' | sudo tee /var/www/html/info.php
   fi

   if [ $opzioneserver == "iotpos" ] ; then
   git clone https://github.com/hiramvillarreal/iotpos
   fi
   
done



