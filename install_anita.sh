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



else
echo "Hai scelto DESKTOP"
#exit 0

#Può tornarci utile https://userbase.kde.org/Tutorials/Modify_KDE_Software_Defaults
#https://techbase.kde.org/Development/Tutorials/Shell_Scripting_with_KDE_Dialogs
#https://mostlylinux.wordpress.com/bashscripting/kdialog/

# TODO: controlliamo se l'attuale sistema sia Kubuntu o Debian, e se kubuntu desktop sia installato. Se è Debian, installiamo i pacchetti ed i repo necessari per avere comunque il minimo indispensabile.
sudo apt-get install kubuntu-desktop


#menù di dolphin per creare PDF
sudo apt-get install img2pdf imagemagick ghostscript pdftk skanlite kamoso

if [ ! -f '/usr/share/kservices5/ServiceMenus/image2pdf.desktop' ]; then
#wget https://dl.opendesktop.org/api/files/download/id/1460731752/118537-image2pdf.desktop
#sudo mv 118537-image2pdf.desktop /usr/share/kservices5/ServiceMenus/image2pdf.desktop
sudo mv $scriptfolder/kde/118537-image2pdf.desktop /usr/share/kservices5/ServiceMenus/image2pdf.desktop
fi

#abilitare le anteprime in dolphin
sudo apt-get install kffmpegthumbnailer kde-thumbnailer-openoffice
kbuildsycoca5

#abilitare la barra del filtro in dolphin
dolphinrc=$HOME"/.kde/share/config/dolphinrc"
filterbar="[General]\nFilterBar=true"
if [ ! -f $dolphinrc ]; then
echo -e $filterbar >> $dolphinrc
fi

if grep -q "FilterBar=false" "$dolphinrc"; then
sed -i 's/FilterBar=false/FilterBar=true/' "$dolphinrc"
fi

if grep -q "FilterBar=true" "$dolphinrc"; then
echo "FilterBar OK"
else

if grep -q "\[General\]" "$dolphinrc"; then
sed -i "s/\[General\]/${filterbar}/" "$dolphinrc"
else
echo -e $filterbar >> $dolphinrc
fi

fi

# TODO: abilitare la barra dei menù in Dolphin

# TODO: sostituiamo il pulsante del menù K con "Start", stile Windows XP

#abilitare i kipi-plugin, soprattutto per l'assistente di stampa
sudo add-apt-repository ppa:philip5/extra
sudo apt update
sudo apt-get install kipi-plugins5 gwenview

# TODO: controllare che su tutti i programmi Copia=Ctrl+C, Incolla=Ctrl+V, Taglia=Ctrl+X, Cancella=Canc, Aggiorna=F5, Rinomina=F2




#Abilitare i MagicSysRQ
sysrq="/etc/sysctl.d/10-magic-sysrq.conf"
if grep -q "^kernel.sysrq" "$sysrq"; then
sed -i "s/^kernel.sysrq/kernel.sysrq = 1 #/" "$sysrq"
else
echo -e "kernel.sysrq = 1" >> "$sysrq"
fi

#verificare la necessità di un server di stampa e scansione
kdialog --title "Stampanti e scanner" --yesno "Vuoi usare una stampante o scanner con il tuo computer?"
if [ "$?" = 0 ]; then
   sudo apt-get install cups hplip hplip-gui sane-utils;
elif [ "$?" = 1 ]; then
   echo "non installo cups e sane";
else
   echo "Errore in kdialog";
fi;



#installare rete samba
sudo apt-get install samba smbclient
# TODO: chiedi la password per impostarla con smbpasswd

#programmi di uso comune
sudo apt-get install vlc speedcrunch rar unrar filelight aptitude ttf-mscorefonts-installer inkscape

#Scribus occupa un po' di spazio. Lo vogliamo installare?
kdialog --title "Impaginazione" --yesno "Vuoi utilizzare il tuo computer per impaginare documenti (brochure, libri, articoli, pagelle, biglietti da visita, ...)?"
if [ "$?" = 0 ]; then
   sudo apt-get install scribus scribus-data scribus-template
elif [ "$?" = 1 ]; then
   echo "non installo scribus";
else
   echo "Errore in kdialog";
fi;

#installare browser ed email reader
sudo add-apt-repository ppa:saiarcot895/chromium-dev
sudo apt-get update
sudo apt-get install chromium-browser firefox thunderbird

#Riconoscimento della Carta Nazionale dei Servizi
#http://www.regione.fvg.it/rafvg/cms/RAFVG/GEN/carta-regionale-servizi/FOGLIA5/
#https://www.regione.fvg.it/rafvg/export/sites/default/RAFVG/GEN/carta-regionale-servizi/FOGLIA7/FOGLIA3/allegati/manuale_installazione_carta_ubuntu.pdf
#https://wiki.ubuntu-it.org/Hardware/Periferiche/CartaNazionaleServizi?action=show&redirect=Hardware%2FPeriferiche%2FCartaNazionaleServizi%2FCNS
#https://it.wikipedia.org/wiki/Carta_nazionale_dei_servizi#Alcuni_servizi_a_cui_si_pu.C3.B2_accedere_con_la_Carta_Nazionale_dei_Servizi
sudo apt-get install pcscd libccid opensc libacr38u libnss3-tools

cd
modutil -dbdir sql:.pki/nssdb/ -add "OpenSC" -libfile /usr/lib/$(uname -i)-*/opensc-pkcs11.so
modutil -dbdir sql:.pki/nssdb/ -list

fi
