//Make the DIV element draggagle:
//dragElement(document.getElementById(("mydiv")));

function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
        /* if present, the header is where you move the DIV from:*/
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
        /* otherwise, move the DIV from anywhere inside the DIV:*/
        elmnt.onmousedown = dragMouseDown;
    }
    
    function dragMouseDown(e) {
        e = e || window.event;
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }
    
    function elementDrag(e) {
        e = e || window.event;
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
        if (document.getElementsByName(elmnt.id + "top")) {
            document.getElementsByName(elmnt.id + "top")[0].value = '' + elmnt.style.top;
            document.getElementsByName(elmnt.id + "left")[0].value = '' + elmnt.style.left;
        }
        //    alert ("The position of the blue element's top-left corner will change to (" + pos1 + ", " + pos2 + ").");
    }
    
    function closeDragElement() {
        /* stop moving when mouse button is released:*/
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

function getfields(element,db) {
    //alert(element.value);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Action to be performed when the document is read;
            var options = "";
            var obj = JSON.parse(xhttp.responseText);
            for (i = 0; i < obj.length; i++) { 
                options = options + '<option>'+obj[i][0]+'</option>';
            }
            document.getElementsByName(element.name + "field")[0].innerHTML = options;
        }
    };
    xhttp.open("GET", "gettableinfo.php?db="+db+"&table="+element.value, true);
    xhttp.send();
}
