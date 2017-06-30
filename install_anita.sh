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

serverslist=$(/usr/bin/dialog --stdout  --title " Tipo di server" --clear --checklist "Che tipo di server vuoi realizzare? \n(spostati con freccia su e giù, scegli con Spazio, conferma con Invio)" 20 30 10 "LTSP" "LTSP" off "stampa" "Server di stampa" off "scanner" "Server per scanner" off "owncloud" "Memorizzazione file" off "lool" "Libre Office On Line" off "LAMP" "Database" off "apache" "Web" off)


if [ ! $? -eq 0 ]; then
        echo "cancel selected"
        exit 0
fi

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
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "owncloud" ] ; then
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "lool" ] ; then
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "LAMP" ] ; then
   sudo apt-get install cups hplip
   fi
   
   if [ $opzioneserver == "apache" ] ; then
   sudo apt-get install cups hplip
   fi
   
done



else
echo "Hai scelto DESKTOP"
#exit 0

#menù di dolphin per creare PDF
sudo apt-get install img2pdf imagemagick ghostscript pdftk skanlite kamoso
#wget https://dl.opendesktop.org/api/files/download/id/1460731752/118537-image2pdf.desktop
#sudo mv 118537-image2pdf.desktop /usr/share/kservices5/ServiceMenus/image2pdf.desktop
sudo mv $scriptfolder/kde/118537-image2pdf.desktop /usr/share/kservices5/ServiceMenus/image2pdf.desktop
kbuildsycoca5

#abilitare le anteprime in dolphin
sudo apt-get install kffmpegthumbnailer kde-thumbnailer-openoffice

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



#Può tornarci utile https://userbase.kde.org/Tutorials/Modify_KDE_Software_Defaults

#Abilitare i MagicSysRQ
sysrq="/etc/sysctl.d/10-magic-sysrq.conf"
if grep -q "^kernel.sysrq" "$sysrq"; then
sed -i "s/^kernel.sysrq/kernel.sysrq = 1 #/" "$sysrq"
else
echo -e "kernel.sysrq = 1" >> "$sysrq"
fi

#verificare la presenza di un server di stampa e scansione
sudo apt-get install cups hplip hplip-gui sane-utils

#https://techbase.kde.org/Development/Tutorials/Shell_Scripting_with_KDE_Dialogs


#installare rete samba
sudo apt-get install samba smbclient
# TODO: chiedi la password per impostarla con smbpasswd

#programmi di uso comune
sudo apt-get install vlc speedcrunch rar unrar filelight aptitude ttf-mscorefonts-installer

#installare browser ed email reader
sudo add-apt-repository ppa:saiarcot895/chromium-dev
sudo apt-get update
sudo apt-get install chromium-browser firefox thunderbird

#Riconoscimento della Carta Nazionale dei Servizi
#http://www.regione.fvg.it/rafvg/cms/RAFVG/GEN/carta-regionale-servizi/FOGLIA5/
#https://www.regione.fvg.it/rafvg/export/sites/default/RAFVG/GEN/carta-regionale-servizi/FOGLIA7/FOGLIA3/allegati/manuale_installazione_carta_ubuntu.pdf
#https://wiki.ubuntu-it.org/Hardware/Periferiche/CartaNazionaleServizi?action=show&redirect=Hardware%2FPeriferiche%2FCartaNazionaleServizi%2FCNS
#https://it.wikipedia.org/wiki/Carta_nazionale_dei_servizi#Alcuni_servizi_a_cui_si_pu.C3.B2_accedere_con_la_Carta_Nazionale_dei_Servizi
sudo apt-get install pcscd libccid opensc libacr38u
cd
modutil -dbdir sql:.pki/nssdb/ -add "OpenSC" -libfile /usr/lib/$(uname -i)-*/opensc-pkcs11.so
modutil -dbdir sql:.pki/nssdb/ -list

fi
