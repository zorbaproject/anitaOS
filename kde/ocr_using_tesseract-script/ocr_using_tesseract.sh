#!/bin/bash
# Servicemenu ocr program
# see readme.txt for copyright and licence


## SOME INITIAL VALUES
GUILANG=$1
let "nbfiles = $# -1"
time=`date +%H%M%S`

######################################################################################################
#### Functions and variables with translatable strings: TRANSLATORS, CHOOSE STRINGS FOR YOUR LANGUAGE!
#
## in addition, please translate these strings, that will go to the *.desktop file:
#
# "OCR using Tesseract"
#
# "OCR image text"


case "$GUILANG" in
    en)
	LANGUAGE=`kdialog --title "OCR using Tesseract" --menu "Select the language of the files:" eng "English"  lit "Lithuanian" deu "German"`
    ;;
    lt)
	LANGUAGE=`kdialog --title "Paveikslėlio teksto nuskaitymas" --menu "Pasirinkite kalbą, kuria užrašytas tekstas paveikslėliuose:" eng "Anglų"  lit "Lietuvių" deu "Vokiečių"`
    ;;
    de)
	LANGUAGE=`kdialog --title "OCR mit Tesseract" --menu "Wählen Sie die Sprache der Datei:" eng "Englisch"  lit "Litauisch" deu "Deutsch"`	
    ;;
esac

function kdialog_initial {

case "$GUILANG" in
    en)
	dbusRef=`kdialog --title "OCR using Tesseract" --progressbar "Tesseract OCR - initialising ..." "$nbfiles"`
    ;;
    lt)
	dbusRef=`kdialog --title "Paveikslėlio teksto nuskaitymas" --progressbar "Tesseract OCR – inicializuojama ..." "$nbfiles"`
    ;;
    de)
	dbusRef=`kdialog --title "OCR mit Tesseract" --progressbar "Tesseract OCR - Initialisierung ..." "$nbfiles"`
    ;;
esac

}

function kdialog_progress {
case "$GUILANG" in
    en)
	qdbus $dbusRef setLabelText "Tesseract-OCR - OCR-ing file: `basename "$INPUTFILE" .tif`"
    ;;
    lt)
	qdbus $dbusRef setLabelText "Tesseract-OCR - nuskaitomas failas: `basename "$INPUTFILE" .tif`"
    ;;
    de)
	qdbus $dbusRef setLabelText "Tesseract-OCR - Bearbeiten der Datei: `basename "$INPUTFILE" .tif`"
    ;;
esac
}

function kdialog_notsupported 
  {
  case "$GUILANG" in
      en)
	  kdialog --passivepopup "OCR of file $i cannot be done: filetype is not supported."
      ;;
      lt)
	  kdialog --passivepopup "Failo $i nuskaitymas neįmanomas: failo tipas nėra palaikomas."
      ;;
      de)
	  kdialog --passivepopup "Texterkennung für Datei $i fehlgeschlagen: Dateityp wird nicht unterstützt."
      ;;
  esac
}

#### End of functions and variables with translatable strings – nothing to translate beyond this point
######################################################################################################

kdialog_initial
qdbus $dbusRef showCancelButton "true"

counter=0
for i in "$@";do
	if [ -f "$i" ];then

	      #test -f if cancel button has been pushed
	      if [[ "$(qdbus $dbusRef wasCancelled)" == "true" ]] ; then
		    qdbus $dbusRef close
		    exit 1
	      fi

	      # some initial values initialized
	      let "counter +=1"
 
	      # main cycle
	      case "${i##*.}" in
		  tif)
		      INPUTFILE="$i"
		      OUTPUTFILE="${INPUTFILE%.*}"
		      kdialog_progress
		      test -f $OUTPUTFILE.txt && OUTPUTFILE=$OUTPUTFILE.$time
		      tesseract "$INPUTFILE" "$OUTPUTFILE" -l "$LANGUAGE"		  
		  ;;
		  tiff)
		    INPUTFILE="${i%.*}.tif"
		    cp "$i" "$time.$INPUTFILE"
		    OUTPUTFILE="${INPUTFILE%.*}"
		    kdialog_progress
		    test -f $OUTPUTFILE.txt && OUTPUTFILE=$OUTPUTFILE.$time
		    tesseract "$time.$INPUTFILE" "$OUTPUTFILE" -l "$LANGUAGE"
		    rm "$time.$INPUTFILE"
		  ;;
		  png|PNG|gif|GIF|jpg|JPG|jpeg|JPEG)
		    INPUTFILE="${i%.*}.tif"
		    convert "$i" "$time.$INPUTFILE"
		    OUTPUTFILE="${INPUTFILE%.*}"
		    kdialog_progress
		    test -f $OUTPUTFILE.txt && OUTPUTFILE=$OUTPUTFILE.$time
		    tesseract "$time.$INPUTFILE" "$OUTPUTFILE" -l "$LANGUAGE"
		    rm "$time.$INPUTFILE"
		  ;;
		  pdf|PDF)
		    INPUTFILE="${i%.*}.tif" 
		    OUTPUTFILE="${INPUTFILE%.*}"
		    gs -dNOPAUSE -q -sDEVICE=tiffg4 -dBATCH -sOutputFile="$INPUTFILE.$time.tif" "$i"
		    kdialog_progress
		    test -f $OUTPUTFILE.txt && OUTPUTFILE=$OUTPUTFILE.$time
		    tesseract "$INPUTFILE.$time.tif" "$OUTPUTFILE" -l "$LANGUAGE"
		    rm "$INPUTFILE.$time.tif"
		  ;;
		  *)
		     kdialog_notsupported
		  ;;
	      esac
	fi
	qdbus $dbusRef org.freedesktop.DBus.Properties.Set org.kde.kdialog.ProgressDialog value $counter

done

qdbus $dbusRef close

