function resizeFrame(height, top, book) {
    $('#view' + book).css('height', height);
    $('#view' + book).css('top', top);
}
var currentPage, previousPage; //markers for menu scrollcollapse.
//--------------------------------------------------------
//functions for the marktable to display columns from gradebox

// array for the gradechoice box - (mid, style.display, select)
var marks = new Array();

// called whenever the marktable is reloaded to check for changes
// and adjust the marks array accordingly
// state=0 means no change
// state=-1 means a change
// state=mid where mid is the value of the new mark column to display
function updateMarkDisplay(state) {
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');
    if (!theBook.getElementById("sidtable")) {
        //marks not displayed
        selMarks.style.display = "none";
        return;
    } else if (state != 0) {
        //the mark selection box has changed need to
        //keep the state of any of the previous marks the same
        for (var markno = 0; markno < marks.length; markno++) {
            if (marks[markno][2] == "selected") {
                var mid = marks[markno][0];
                if (document.getElementById("sel-" + mid)) {
                    document.getElementById("sel-" + mid).selected = "selected";
                }
            }
        }
        if (document.getElementById("sel-" + state)) {
            //if state is set the mid of a newly created mark then display it
            document.getElementById("sel-" + state).selected = "selected";
        }
        marks.length = 0;
        //blank old marks array first
        changeMarkDisplay();
        ldUiObjects.updateDisplay(ldUiObjects.elements[selMarks.getAttribute('name')])
    } else {
        //no change
        markDisplay();
    }
}

// takes the display state from the selected values in the options
// and stores in the marks array (calls markDisplay to apply them)
function changeMarkDisplay() {
    var i = 0;
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');
    for (var c = 0; c < selMarks.options.length; c++) {
        if (selMarks.options[c].selected) {
            marks[c] = Array(selMarks.options[c].value, "table-cell", "selected");
        } else {
            marks[c] = Array(selMarks.options[c].value, "none", "");
        }
    }
    markDisplay();
}

// takes the selected state from those stored in the marks array
// and applies them to the marktable
function markDisplay() {
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');

    var theRows = theBook.getElementsByTagName("tr");
    var i = 0;
    var sids = new Array();
    for (var c = 0; c < theRows.length; c++) {
        if (theRows[c].attributes.getNamedItem("id")) {
            var rowId = escape(theRows[c].attributes["id"].value);
            sids[i] = rowId.substring(4, rowId.length);
            //sids[i] = theRows[c].attributes.getNamedItem("id").value;
            i++;
        }
    }
    for (var markno = 0; markno < marks.length; markno++) {
        var mark = marks[markno];
        theBook.getElementById(mark[0]).style.display = mark[1];
        selMarks.options[markno].selected = mark[2];
        var i = 0;
        while (( sid = sids[i++])) {
            theBook.getElementById(sid + "-" + mark[0]).style.display = mark[1];
        }
    }
}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the selery menu in the bookoptions
//  and grows selery buttons

function seleryCheckKey(event, formObj) {
    if (event.keyCode == 13) {
        formObj.submit();
    }
}

function selerySubmit(liObj) {
    liObj.getElementsByTagName("input")[0].setAttribute("checked", "checked");
    //assumes the form to be the direct parent of the fieldset containing the selery ul
    liObj.parentNode.parentNode.parentNode.submit();
}

function selerySelectSubmit(selectObj) {
    selectObj.form.submit();
}

function seleryGrow(buttonObj, limit) {
    var start = buttonObj.value;
    var end = ++start;
    if (end > limit) {
        end = 0;
    }
    buttonObj.value = end;
    buttonObj.parentNode.getElementsByTagName("input")[0].value = end;
}

function selerySwitch(servantclass, fieldvalue, bookname){
	switchedId = "switch" + servantclass;
	newfielddivId = "switch" + servantclass + fieldvalue;
	if(document.getElementById(newfielddivId)){
		document.getElementById(switchedId).innerHTML=document.getElementById(newfielddivId).innerHTML;
		}
	else if(window.frames["view" + bookname].document.getElementById(newfielddivId)){
		window.frames["view" + bookname].document.getElementById(switchedId).innerHTML=window.frames["view" + bookname].document.getElementById(newfielddivId).innerHTML;
		}
	else if(window.frames["viewmodal"] && window.frames["viewmodal"].document.getElementById(newfielddivId)){
		window.frames["viewmodal"].document.getElementById(switchedId).innerHTML=window.frames["viewmodal"].document.getElementById(newfielddivId).innerHTML;
		}
	else{
		window.frames["view" + bookname].document.getElementById(switchedId).innerHTML='';
		}
	}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the Book Tabs and bookframe

//  only called when index is loaded or the LogIn button is hit
//  displays the cover or login page respectively
function loadLogin(page) {
    //window.frames["viewlogbook"].location.href="logbook/exit.php";
    //setTimeout(window.frames["viewlogbook"].location.href=page,200);
    window.frames["viewlogbook"].location.href = page;
    document.getElementById("viewlogbook").style.display = "block";
    document.getElementById("viewlogbook").focus();
}

//  only called once after a new session has been started
//  flashscreen is the aboutbook followed after delay by markbook
function logInSuccess() {
    document.getElementById("navtabs").innerHTML = viewlogbook.document.getElementById("hiddennavtabs").innerHTML;
    document.getElementById("logbook").innerHTML = viewlogbook.document.getElementById("hiddenlogbook").innerHTML;
    document.getElementById("logbook").className = "loggedin";
    document.getElementById("loginlabel").innerHTML = viewlogbook.document.getElementById("hiddenloginlabel").innerHTML;
    document.getElementById("viewlogbook").innerHTML = "";
    document.getElementById("viewlogbook").style.display = "none";
    document.getElementById("logbookoptions").innerHTML = "";
    document.getElementById("logbookoptions").style.display = "none";
    $(document.getElementById("navtabs")).find('select').uniform();
    //viewBook("aboutbook");
}
//  only called when the LogOut button is hit
function logOut() {
    window.frames["viewlogbook"].location.href = "logbook/exit.php";
    console.log(window.frames["viewlogbook"].location.href);
}
// called if session reopens the login screen, in any frame.
function refreshloginscreen(frame) {
    if (frame.id == "viewlogbook") {
        return
        }
    logOut()
    var books=document.getElementsByClassName('bookoptions')
    for (var i=0; i<books.length; i++) {
        books[i].style.display = 'none';
        }
    frame.style.top = "80px";
    frame.style.height = "100%";
    }
//	Reloads the book without giving focus (never used for logbook!)
//	always called by logbook if a session is set,
//	also called when changes in one book needs to update another
function loadBook(book) {
    var currentbook = "";
    if (document.getElementById("currentbook")) {
        currentbook = document.getElementById("currentbook").getAttribute("class");
    }
    if (book == "") {
        book = currentbook;
    }
    if (book != "") {
        window.frames["view" + book].location.href = book + ".php";
    }
    if (book != currentbook) {
        document.getElementById("view" + book).style.display = "none";
        document.getElementById(book + "options").style.display = "none";
    }
    //window.frames["view"+book].history.forward();
}

function loadBookOptions(book) {
    document.getElementById(book + "options").innerHTML = window.frames["view" + book].document.getElementById("hiddenbookoptions").innerHTML;
    $('#' + book + 'optionshandle').off('click');
    $(document.getElementById(book + "options")).find('select').uniform()
    $(document.getElementById(book + "options")).find('select[multiple="multiple"]').each(function(index, element){
        ldUiObjects.multiSelect(element);
    });
}

function viewBook(newbook) {
    //bring the new tab and book to the top
    var newFrame=document.getElementById("view" + newbook)
    newFrame.style.display = "block";
    newFrame.focus();
    if (document.getElementById(newbook + "optionshandle") &&
        document.getElementById(newbook + "optionshandle").getAttribute('class').search(/\s?open\s?/) > -1) {
        document.getElementById(newbook + "optionshandle").style.display = "block";
    } else {
        document.getElementById(newbook + "options").style.display = "block";
    }
    // hide the oldbook and tab
    var currentTab = document.getElementById("currentbook")
    if (currentTab){
        var oldbook = document.getElementById("currentbook").getAttribute("class");
        if (oldbook == newbook) {
            return
        }
        document.getElementById(oldbook + "options").style.display = "none";
        document.getElementById(oldbook + "optionshandle").style.display = "none";
        document.getElementById("view" + oldbook).style.display = "none";
        currentTab.removeAttribute('id');
    }
    document.getElementById(newbook + "tab").firstChild.setAttribute("id", "currentbook");
}

// A print function that handles pages designated as printable
function printGenericContent(iFrameName) {
    var printWindow;
    var contentToPrint = "";
    var currentbook = document.getElementById("currentbook").getAttribute("class");

    if (window.frames["view" + currentbook].document.getElementById("viewcontent")) {
        if (window.frames["view" + currentbook].document.getElementById("heading")) {
            contentToPrint = window.frames["view" + currentbook].document.getElementById("heading").innerHTML;
        }

        var contentDiv = window.frames["view" + currentbook].document.getElementById("viewcontent");

        var alinks = contentDiv.getElementsByTagName("a");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("href", "#");
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("input");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("button");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("select");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("disabled", "disabled");
        }

        contentToPrint = contentToPrint + contentDiv.innerHTML;
    } else {
        contentToPrint = "<h3>There is no printer friendly content on this page.</h3>";
    }
    parent.openModalWindow('', "<html><head><link rel='stylesheet' type='text/css' href='css/printstyle.css' /></head><body><br />" + contentToPrint + "</body></html>", true);
}
// Keep the php session alive
function sessionAlive(pathtobook) {
    var url = pathtobook + "httpscripts/session_alive.php?uniqueid=1";
    var xmlHttp = false;
    requestxmlHttp();
    function requestxmlHttp() {
        try {
            xmlHttp = new XMLHttpRequest();
        } catch (failed) {
            xmlHttp = false;
        }
        if (!xmlHttp) {
            alert("Error initializing XMLHttpRequest!");
        }
    }


    xmlHttp.open("GET", url, true);
    xmlHttp.send(null);
}

//------------------------------------------------------
//
function tinyTabs(tabObject) {
    // the id of containing div (eg. area for statementbank)
    var tabmenuId = tabObject.parentNode.parentNode.parentNode.id;
    var chosentab = tabObject.getAttribute("class");
    var currentbook = document.getElementById("currentbook").getAttribute("class");

    window.frames["view" + currentbook].document.getElementById("current-tinytab").removeAttribute("id");
    window.frames["view" + currentbook].document.getElementById("tinytab-" + tabmenuId + "-" + chosentab).firstChild.setAttribute("id", "current-tinytab");
    var targetId = "tinytab-display-" + tabmenuId;
    var sourceId = "tinytab-xml-" + tabmenuId + "-" + chosentab;
    var fragment = window.frames["view" + currentbook].document.getElementById(sourceId).innerHTML;
    window.frames["view" + currentbook].document.getElementById(targetId).innerHTML = "";
    window.frames["view" + currentbook].document.getElementById(targetId).innerHTML = fragment;
    if (window.frames["view" + currentbook].document.getElementById("statementbank")) {
        //this must be running the statement bank
        filterStatements(subarea, ability);
    }
}

var previousPage = "";
var previousPageScroll = 0;
var previousScroll = new Array();
/**
 * propigates dialog from child frame to take full page dialog.
 * This replaces the xsl transformation from xml js function
 * and opens a new iframe in a dialog display.
 * htmlStr is a full document including style and js tags.
 */
function openModalWindow(src,content, printable){
    var html="<iframe id='content-frame' name='viewmodal' width=800>";
    if (printable) {
        html="<div class='printable'>" +
        "<span class='fa fa-arrows-h'></span>" +
        "<span class='fa fa-print'></span>" +
        "</div><div class='xslt'><iframe id='content-frame' width=750></div>"
    }
	vexMainModal = vex.open({content: html, contentClassName: 'thanks-modal', closeClassName: 'modal-close', showCloseButton: true});
    $(".vex .fa-arrows-h").on('click', function(event) {
        event.stopPropagation();
        $('.vex .vex-content').toggleClass('stretch');
    })
    $(".vex .fa-print").on('click', function(event) {
        event.stopPropagation();
        var printDoc = document.getElementById('content-frame').contentWindow
        $(document.getElementById('content-frame').contentWindow.document).find('hr').replaceWith('<p style="page-break-after:always">&nbsp;</p>');
        //print framecontents only in IE
        if (!printDoc.document.execCommand('print', false, null)){
            printDoc.print();
            }
        })
	//add scrollbar functions
    $('.vex .vex-overlay').css('right', 'auto');
    $('.vex.vex-ld-theme').css('background', 'rgba(0,0,0,0.2)');
    $('.vex.vex-ld-theme').on('click', function(event) {
        if ($(event.target).hasClass('thanks-modal') || $(event.target).hasClass('printable')) {
            //clicked inside modal do not close
            return; 
        } else {
            //check for tinymce, if present check if dirty:
            var tinyMce = document.getElementById('content-frame').contentWindow.tinyMCE
            if (tinyMce) {
                saveTinyMceChangesAlert(vexMainModal, tinyMce)
                return
            } else {
                vex.close();
            }
        }
    })
    $('.vex.vex-ld-theme .vex-close').off('click');
    $('.vex.vex-ld-theme .vex-close').on('click', function(event) {
        event.stopPropagation();
        //check for tinymce, if present check if dirty:
        var tinyMce = document.getElementById('content-frame').contentWindow.tinyMCE
        if (tinyMce){
            saveTinyMceChangesAlert(vexMainModal, tinyMce)
        } else {
            vex.close();
        }
    })
    if (src!=''||content!='') {
        updateModalContents(vexMainModal, src, content);
    }
    return vexMainModal
}
function updateModalContents(modalObject, src, content) {
    var iFrame=modalObject.find('iframe')[0];
	if(src!=''){
        iFrame.src=src;
        $(iFrame).load(function(){
            if($(this).contents().find("#bookbox")){
                $(this).contents().find("#bookbox").toggleClass( "bookbox-active" );
                //$(this).height( $(this).contents().find("#bookbox").outerHeight(true));
                //$('.vex.vex-ld-theme .thanks-modal').height($(this).contents().find("#bookbox").outerHeight(true)+5); //firefox padding
                $(this).height($(this).contents().find("#bookbox").outerHeight(true)+5); //firefox padding
                $(modalObject).addClass( "thanks-modal-active" );
            } else if ($(this).contents().find("body")) {
                $(modalObject).height($(this).contents().find("body").outerHeight(true));
            }
        });
        }
    else{
        $(iFrame).load(function(){
            $('.vex .vex-content').css('background-image', 'none');
            var height=$(this).contents().find("html").outerHeight(true);
            if (modalObject.find('.printable').length > 0) {
                $('.vex.vex-ld-theme .xslt').height(height);
                $('.vex.vex-ld-theme .xslt').css('background-color', '#fff');
                height=height+50;
                }
            $(modalObject).height(height);
            })
        iFrame.contentWindow.document.write(content);
		iFrame.contentWindow.document.close();
        }
    }
function saveTinyMceChangesAlert(vexMainModal, tinyMce) {
    if (tinyMce.activeEditor && tinyMce.activeEditor.isDirty()){
        if (document.getElementById('content-frame').contentWindow.document.getElementById('vex-alert')){
            var alertText=document.getElementById('content-frame').contentWindow.document.getElementById('vex-alert').outerHTML
            vexAlert=vex.open({content:alertText, contentClassName:'alert-modal', showCloseButton:false});
            vexAlert.find('#vex-alert').css('display', 'block')
            vexAlert.find('.vex-dialog-button-primary').on('click',function(){
                $(document.getElementById('content-frame').contentWindow.document).find('button[name="sub"]').click()
                vex.close(vexMainModal.data().vex.id);
                vex.close(vexAlert.data().vex.id);
                })
            vexAlert.find('.vex-dialog-button-secondary').on('click',function(){
                vex.close(vexMainModal.data().vex.id);
                vex.close(vexAlert.data().vex.id);
                })
                return
            }
            else {
                tinyMce.activeEditor.execCommand('post');
            }

        }
    //if(document.getElementById('content-frame').contentWindow.document.getElementById('addcategory').length>0 && document.getElementById('content-frame').contentWindow.document.getElementById('addcategory').value=='yes'){
    		tinyMce.editors[0].execCommand('post');
    //	}
    tinyMceHasChangedAlert(vexMainModal);
    vex.close(vexMainModal.data().vex.id);
    }
function tinyMceHasChangedAlert(vexMainModal){
    if (document.getElementById('content-frame').contentWindow.document.getElementById('vex-flash-message')){
        var alertText=document.getElementById('content-frame').contentWindow.document.getElementById('vex-flash-message').outerHTML
        vexAlert=vex.open({content:alertText, contentClassName:'alert-modal', showCloseButton:false});
        vexAlert.find('#vex-flash-message').css('display', 'block');
        setTimeout(function() {
            vex.close(vexAlert.data().vex.id);
            },1000);
        }
    }
/**
 * adds the images and attributes to required input fields
 * inits the js-calendar elements and the tooltip titles
 */
function loadRequired(book) {
//	var dictionary=[];
//	getDictionary('es',function(err,resp){
//		var dictionary=JSON.parse(resp);
//		console.log(dictionary);//works because is asynchronous
//		});
//	console.log(dictionary);//it's an empty array
    var firstFocus;
    var formObject;
    var elementObject;
    var imageRequired;
    firstFocus = -1;
    for ( i = 0; i < window.frames["view" + book].document.forms.length; i++) {
        formObject = window.frames["view"+book].document.forms[i];
        for ( c = 0; c < formObject.elements.length; c++) {
            elementObject = formObject.elements[c];
            if (elementObject.className.indexOf("required") != -1) {
                if (elementObject.tagName != 'SELECT') {
                    elementObject.setAttribute("onChange", "validateRequired(this)");
                } else {
                    elementObject.setAttribute("onChange", "validateSelectRequired(this)");
                }
                imageRequired = window.frames["view" + book].document.createElement("span");
                imageRequired.className = "required";
                elementObject.parentNode.insertBefore(imageRequired, elementObject);
            }
            if (elementObject.className.indexOf("eitheror") != -1) {
                elementObject.setAttribute('onChange', 'validateRequiredOr(this)');
                imageRequired = window.frames["view" + book].document.createElement("span");
                imageRequired.className = "required";
                elementObject.parentNode.insertBefore(imageRequired, elementObject);
            }
            if (elementObject.className.indexOf("switcher") != -1) {
                switcherId = elementObject.getAttribute("id");
                parent.selerySwitch(switcherId, elementObject.value, book);
                elementObject.setAttribute("onChange", "parent.selerySwitch('" + switcherId + "',this.value,'" + book + "')");
            }

            // add event handlers to the checkbox input elements
            if (elementObject.getAttribute("type") == "checkbox" && elementObject.name == "sids[]") {
                elementObject.onchange = function() {
                    window.frames["view" + book].checkrowIndicator(this)
                };
            }
            if (elementObject.getAttribute("type") == "radio" && elementObject.parentNode.tagName != "TH") {
                elementObject.parentNode.onclick = function() {
                    window.frames["view" + book].checkRadioIndicator(this)
                };
            }
            if (elementObject.getAttribute("tabindex") == "1" && firstFocus == "-1") {
                firstFocus = c;
            }
            if (elementObject.getAttribute("maxlength")) {
                var maxlength = elementObject.getAttribute("maxlength");
                if (maxlength > 180) {
                    elementObject.style.width = "80%";
                } else if (maxlength > 50) {
                    elementObject.style.width = "60%";
                } else if (maxlength < 20 && maxlength > 0) {
                    elementObject.style.width = maxlength + "em";
                }
            }
            if (elementObject.getAttribute("type") == "date") {
                var inputId = elementObject.getAttribute("id");
                window.frames["view" + book].Calendar.setup({
                    inputField : inputId,
                    ifFormat : "%Y-%m-%d",
                    button : "calendar-" + inputId
                });
            }
        }
    }

      $(".navbar-header").click(function(){
        $(".navbar-collapse").toggleClass("navbar-collapse-show");
      });
      $(".navbar-collapse").click(function(){
        $(this).toggleClass("navbar-collapse-show");
      });

    /*load the first tiny-tab (if there is one)*/
    if (window.frames["view" + book].document.getElementById("current-tinytab")) {
        tinyTabs(window.frames["view" + book].document.getElementById("current-tinytab"));
    }

    /*load the first tiny-tab (if there is one)*/
    if (window.frames["view" + book].document.getElementById("listplus")) {
        window.frames["view" + book].listplusInit();
    }

	if(window.frames["view" + book].document.getElementById("sharearea")){
		window.frames["view" + book].shareareaInit();
		}

	if(window.frames["viewmodal"] && window.frames["viewmodal"].document.getElementById("editsingleattendance")){
		var sid=window.frames["viewmodal"].document.getElementById("editsingleattendance").value;
		var colid=window.frames["viewmodal"].document.getElementById("colid").value;
		var cell=window.frames["view" + book].document.getElementById(colid+'-'+sid);
		window.frames["viewmodal"].updateStudentAttendance(sid,cell);
		}

	if (window.frames["view" + book].document.getElementById("openexport")) {
		window.frames["view" + book].openexportInit();
		}

	if (window.frames["viewmedbook"].document.getElementById("Date0") && window.frames["viewmedbook"].document.getElementById("time")) {
		var date=window.frames["viewmedbook"].document.getElementById("Date0");
		var time=window.frames["viewmedbook"].document.getElementById("time");
		if(date.value==''){window.frames["viewmedbook"].displayCurrentDate('Date0');}
		if(time.value==''){window.frames["viewmedbook"].displayCurrentTime('time');}
		}

    /*prepares the span elements with title attributes for qtip*/
    if (window.frames["view" + book].tooltip) {
        window.frames["view" + book].tooltip.init();
    }

    /*prepares a sidtable if it is present*/
    if (window.frames["view" + book].document.getElementById("sidtable")) {
        window.frames["view" + book].sidtableInit();
    }

    /*prepares a document drop if it is present*/
    if (window.frames["view" + book].document.getElementById("formdocumentdrop")) {
        window.frames["view" + book].documentdropInit();
    }

	/*displays a notice if it is present*/
	if(window.frames["view" + book].document.getElementById("notice")) {
		window.frames["view" + book].openAlert(book);
		}
    /*give focus to the tab=1 form element if this is a form*/
    /*should always be last!*/
    if (i > 0) {
        if (firstFocus == -1) {
            firstFocus = 0;
        }
        if (window.frames["view"+book].document.forms[0].elements[firstFocus]) {
            window.frames["view"+book].document.forms[0].elements[firstFocus].focus();
        }
    }

    previousScroll[book] = 0;

    /*heights*/
    var contentsHeight = $('#view' + book).contents().find("#bookbox").outerHeight(true);
    var frameHeight = $('#view' + book).height();
    var menuHeight = $('#' + book + "options").outerHeight(true);
    var collapseHeight = $('#' + book + "optionshandle").height();
    var headerHeight = $('.booktabs').height();
    var windowHeight = $(window).outerHeight(true);

    currentPage=$('#view' + book).contents().find("input[name='current']").val();
    if(previousPage==currentPage && book!="logbook" && previousPageScroll>0){
      //$('#' + book + "options").css("display", "none");
      resizeFrame(windowHeight - headerHeight, headerHeight, book);
      $(window.frames["view" + book]).scrollTop(0);
      }

    /*default settings*/
    if (book != "logbook") {
        resizeFrame(windowHeight - headerHeight - menuHeight, menuHeight + headerHeight, book);
    }
    if ($('#' + book + 'optionshandle').hasClass('open')){
        $('#' + book + "options").css("display", "block");
        $('#' + book + 'optionshandle').css("display", "none");
        $('#' + book + 'optionshandle').removeClass('open');
        $('#' + book + 'optionshandle').off("click");
    }
    if (contentsHeight >= frameHeight && contentsHeight <= (frameHeight + menuHeight)) {
       // $('#view' + book).contents().find("#bookbox").css('padding-bottom', frameHeight);
    }


    /*on frame's scroll resize the frame*/
    $(window.frames["view" + book]).on('scroll', {book:book}, frameScrollFunction);
    
    $(window).resize(function() {
        var windowHeight = $(window).outerHeight(true);
        if ($('#' + book + "optionshandle").hasClass("open")) {
            resizeFrame(windowHeight - headerHeight - collapseHeight, headerHeight + collapseHeight, book);
        } else {
            resizeFrame(windowHeight - headerHeight - menuHeight, menuHeight + headerHeight, book);
        }
    });
}
function frameScrollFunction(event) {
    //original show if scroll 0 hide otherwise function

    /*var book = event.data.book;
    var headerHeight = $('.booktabs').height();
    var menuHeight = $('#' + book + "options").outerHeight(true);
    var collapseOptionsHeight = headerHeight + $('#' + book + 'optionshandle').height();
   
    var windowHeight = $(window).outerHeight(true);
    var currentScroll = new Array();
    currentScroll[book] = $(this).scrollTop();
    if (currentScroll[book] == 0) {
        $('#' + book + "options").slideToggle(300, function() {
            $('#' + book + "options").css("display", "block");
            $('#' + book + 'optionshandle').css({"display":"none", "z-index":-100});
            resizeFrame(windowHeight - headerHeight - menuHeight, menuHeight + headerHeight, book);
            previousPageScroll = 0
        });
    } else if (currentScroll[book] > previousScroll[book] && previousScroll[book] == 0) {
        $(window.frames["view" + book]).off('scroll', frameScrollFunction);
        $('#' + book + "options").slideToggle(300, function() {
            //if (windowHeight - headerHeight > $('#view' + book).contents().find("#bookbox").outerHeight(true)) {
                $('#' + book + 'optionshandle').css({"display":"block", "z-index":100});
                $('#' + book + 'optionshandle').on('click', {
                    book:book,
                    height:windowHeight - headerHeight - menuHeight,
                    top:menuHeight + headerHeight
                }, openBookOptions)
            //} else {
                $(this).scrollTop(3);
            //}
            $('#' + book + "options").css("display", "none");
            resizeFrame(windowHeight - collapseOptionsHeight, collapseOptionsHeight, book);
            previousPage = currentPage;
            previousPageScroll = 1
        });
    }
    previousScroll[book] = currentScroll[book];*/
    //simple hide on scroll function
    var book = event.data.book;
    var headerHeight = $('.booktabs').height();
    var menuHeight = $('#' + book + "options").outerHeight(true);
    var collapseOptionsHeight = headerHeight + $('#' + book + 'optionshandle').height();
    var windowHeight = $(window).outerHeight(true);
    var currentScroll = new Array();
    currentScroll[book] = $(this).scrollTop();
    if (currentScroll[book] > previousScroll[book] && previousScroll[book] == 0) {
        $('#' + book + "options").slideToggle(300, function() {
            $('#' + book + 'optionshandle').addClass("open");
            $('#' + book + 'optionshandle').css("display", "block");
            $('#' + book + 'optionshandle').on('click', {
                book:book,
                height:windowHeight - headerHeight - menuHeight,
                top:menuHeight + headerHeight
            }, openBookOptions);
            $(window.frames["view" + book]).off('scroll', frameScrollFunction);
            $(this).scrollTop(3);
            resizeFrame(windowHeight - collapseOptionsHeight, collapseOptionsHeight, book);
            previousPage = currentPage;
            previousPageScroll = 1
        });
    }
    previousScroll[book] = currentScroll[book];
}
function openBookOptions(event) {
    var book = event.data.book;
    $('#' + book + 'optionshandle').off('click')
    $('#' + book + 'optionshandle').removeClass("open");
    $('#' + book + 'optionshandle').css("display", "none");
    $('#' + book + "options").slideToggle(300, function() {
        resizeFrame(event.data.height, event.data.top, book);
        previousPageScroll = 0;
        previousScroll[book] = 0;
        $(window.frames["view" + book]).on('scroll', {book: book}, frameScrollFunction);
    });
}

function getDictionary(lang,callback){
	var url='scripts/json_dictionary.php?lang='+lang;
	xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4 && xmlHttp.status==200){
			callback(null,xmlHttp.responseText);
			}
		};
	xmlHttp.send();
	}
