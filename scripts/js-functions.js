// This sets a global variable divName window object
// Needed for updating a unique div after update
var divName="";


<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->


jQuery.fn.isChildof = function(b){
    return (this.parents(b).length > 0);
};






// Update the divName
function ajaxcallback()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		document.getElementById(divName).innerHTML=xmlHttp.responseText;
		document.getElementById(divName).style.display="block";
		//alert("Updating DIV"+divName);	
		document.getElementById("loading").style.display="none";
		
	}
		
}


function formFocusOnElement(elementID)
{
	document.getElementById(elementID).focus()	
}
function toggleTabVis(currentPage, newPage)
{
	// Add one as its from an array :)
	currentPage=currentPage+1;
	newPage=newPage+1;	
	currentPage = "tabPage"+currentPage;
	newPage = "tabPage"+newPage;	
	//alert ("newPage = 	"+newPage+" currentPage = "+currentPage);
	toggleLayerVis(currentPage);
	toggleLayerVis(newPage);
}

<!-- layervis - generic togggler for show/hide on any divs by id-->
function toggleLayerVis(id){
if (document.getElementById) {
	if (this.document.getElementById(id).style.display=="none")
		(this.document.getElementById(id).style.display="block") ;
	else
		(this.document.getElementById(id).style.display="none") ;
	}
else if (document.all) {
	if (this.document.all[id].style.display=="none")
		(this.document.all[id].style.display="block") ;
	else
		(this.document.all[id].style.display="none") ;
	}
else if (document.layers) {
	if (this.document.layers[id].style.display=="none")
		(this.document.layers[id].style.display="block") ;
	else
		(this.document.layers[id].style.display="none") ;
	}
}

// A generic HIDE and SHOW stuff so you don't need to worry about toggles
function divDisplayShow(id)
{
	this.document.getElementById(id).style.display="block";
}

function divDisplayHide(id)
{
	this.document.getElementById(id).style.display="none";
}


// Start of Delete popup box code
function hideDiv(divName) { 
if (document.getElementById) { // DOM3 = IE5, NS6 
document.getElementById(divName).style.visibility = 'hidden'; 
} 
else { 
if (document.layers) { // Netscape 4 
document.divName.visibility = 'hidden'; 
} 
else { // IE 4 
document.all.divName.style.visibility = 'hidden'; 
} 
} 
}



function showDiv(divName) {
	if (document.getElementById)
	{ // DOM3 = IE5, NS6 
		document.getElementById(divName).style.visibility = 'visible'; 
	} 
	else
	{ 
		if (document.layers) { // Netscape 4 
		document.divName.visibility = 'visible'; 
		} 
		else { // IE 4 
		document.all.divName.style.visibility = 'visible'; 
		} 
	} 
	
} 

function ajaxProcessRequest(requestType, funcDivName, processStr)
{


	xmlHttp=GetXmlHttpObject();
	
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}
	divName = funcDivName;
	//alert ("NewdivName="+divName);
	
	var url="/wp-content/plugins/AI-Quiz/scripts/ajax.php?";
	var qrystr=processStr+"&action="+requestType+"&sid="+Math.random();
	
	var sendURL = url+qrystr;
	
	//divName="markersDiv";
	
	//alert ("sendURL="+sendURL);
	
	xmlHttp.onreadystatechange=ajaxcallback
	xmlHttp.open("POST",sendURL,true)
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", qrystr.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(qrystr);
}

function showUserSearchResult(searchStr, updateDivName, otherVars)
{
	if (searchStr.length>=3)
	{
	  
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request")
			return
		}
		var url="/scripts/php_ajax.php?";
		var qrystr="action=userSearch";
		qrystr=qrystr+"&searchStr="+searchStr+"&"+otherVars;
		
		qrystr=qrystr+"&sid="+Math.random();
		
		var sendURL = url+qrystr;
		
		
		divName=updateDivName;
		
		//alert ("URL="+sendURL);
		
		
		xmlHttp.onreadystatechange=ajaxcallback
		xmlHttp.open("POST",sendURL,true)
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", qrystr.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.send(qrystr);
	}
}



/////////////////////////////////////
/////////// AJAX STUFF /////////////
/////////////////////////////////////

function GetXmlHttpObject()
{
	
	//document.getElementById("loading").style.display="block";	
	var objXMLHttp=null;
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
} 

/// END OF AJAX STUFF ////


<!-- layervis - generic togggler for show/hide on any divs by id-->
function layervis(id){
if (document.getElementById) {
	if (this.document.getElementById(id).style.display=="none")
		(this.document.getElementById(id).style.display="block") ;
	else
		(this.document.getElementById(id).style.display="none") ;
	}
else if (document.all) {
	if (this.document.all[id].style.display=="none")
		(this.document.all[id].style.display="block") ;
	else
		(this.document.all[id].style.display="none") ;
	}
else if (document.layers) {
	if (this.document.layers[id].style.display=="none")
		(this.document.layers[id].style.display="block") ;
	else
		(this.document.layers[id].style.display="none") ;
	}
}


function showPopupBox(feedbackType, valueStr) 
{	

	var hiddenDivName="popupBox";
	
	if (document.getElementById)
	{ // DOM3 = IE5, NS6 
		document.getElementById(hiddenDivName).style.visibility = 'visible'; 
	} 
	else
	{ 
		if (document.layers) { // Netscape 4 
		document.hiddenDivName.visibility = 'visible'; 
		} 
		else { // IE 4 
		document.all.hiddenDivName.style.visibility = 'visible'; 
		
		} 
	}
	divName="feedbackText";
	
	//var submissionID=document.getElementById('submissionID').value;
	//qrystr+= "&processType="+processType;
	
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}
	
	var url="/scripts/php_ajax_popup.php?";

	var qrystr = "feedbackType=" + feedbackType + "&"+valueStr;
	qrystr+= "&action=drawFeedbackPopup";
	qrystr+= "&sid="+Math.random();
	

	
	var sendURL = url+qrystr;
	//alert (sendURL);	
		
	xmlHttp.onreadystatechange=ajaxcallback
	xmlHttp.open("POST",sendURL,true)
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", qrystr.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(qrystr);
}


function ajaxFormItemEdit(updateDivName, processStr, elementIDs, FCKelementIDs)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}
	divName = updateDivName;
	//alert ("NewdivName="+divName);
	
	var elementStr = "";
	if(elementIDs)
	{
		var elementArray = elementIDs.split(",");
		var thisValue = "";
		
		for (i in elementArray)
		{
			elementID = elementArray[i];
			// get the value of the criteria
			
			
			// check to see if its a checkbox!
			if(elementID.indexOf("CHECKBOX_")==0)
			 {
				 var checkValue="";
				//alert ("CHECK"+elementID);
				// Get the check value
				if (document.getElementById(elementID).checked)
				{
					thisValue=1;
					//alert ("Checked");
				}
				else
				{
					thisValue=0;
					//alert ("NOT Checked");					
				}
				

				// Now rename the element ID
				elementID = elementID.substr(9);
				//alert (elementID+" = "+thisValue);
				
			 }
			 // Else its a regular textfield
			 else
			 {
				thisValue = document.getElementById(elementID).value;
			 }
			
			elementStr+="&"+elementID+"="+encodeURIComponent(thisValue);
		}	
	}
	
	//alert (elementStr);
	
	var url="/wp-content/plugins/AI-Quiz/scripts/ajax.php?sid="+Math.random();
	var qrystr=processStr+elementStr;
	
	var sendURL = url //+qrystr;
	//alert ("sendURL="+sendURL);
	
	xmlHttp.onreadystatechange=ajaxcallback
	xmlHttp.open("POST",url,true)
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", qrystr.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(qrystr);
}









// F. Permadi 2005.
// Highlights table row
// Copyright (C) F. Permadi
// This code is provided "as is" and without warranty of any kind.  Use at your own risk.



// These variables are for saving the original background colors
var savedStates=new Array();
var savedStateCount=0;

/////////////////////////////////////////////////////
// This function takes an element as a parameter and 
//   returns an object which contain the saved state
//   of the element's background color.
/////////////////////////////////////////////////////
function saveBackgroundStyle(myElement)
{
  saved=new Object();
  saved.element=myElement;
  saved.className=myElement.className;
  saved.backgroundColor=myElement.style["backgroundColor"];
  return saved;   
}

/////////////////////////////////////////////////////
// This function takes an element as a parameter and 
//   returns an object which contain the saved state
//   of the element's background color.
/////////////////////////////////////////////////////
function restoreBackgroundStyle(savedState)
{
  savedState.element.style["backgroundColor"]=savedState.backgroundColor;
  if (savedState.className)
  {
    savedState.element.className=savedState.className;    
  }
}

/////////////////////////////////////////////////////
// This function is used by highlightTableRow() to find table cells (TD) node
/////////////////////////////////////////////////////
function findNode(startingNode, tagName)
{
  // on Firefox, the TD node might not be the firstChild node of the TR node
  myElement=startingNode;
  var i=0;
  while (myElement && (!myElement.tagName || (myElement.tagName && myElement.tagName!=tagName)))
  {
    myElement=startingNode.childNodes[i];
    i++;
  }  
  if (myElement && myElement.tagName && myElement.tagName==tagName)
  {
    return myElement;
  }
  // on IE, the TD node might be the firstChild node of the TR node  
  else if (startingNode.firstChild)
    return findNode(startingNode.firstChild, tagName);
  return 0;
}

/////////////////////////////////////////////////////
// Highlight table row.
// newElement could be any element nested inside the table
// highlightColor is the color of the highlight
/////////////////////////////////////////////////////
function highlightTableRow(myElement, highlightColor)
{
  var i=0;
  // Restore color of the previously highlighted row
  for (i; i<savedStateCount; i++)
  {
    restoreBackgroundStyle(savedStates[i]);          
  }
  savedStateCount=0;

  // To get the node to the row (ie: the <TR> element), 
  // we need to traverse the parent nodes until we get a row element (TR)
  // Netscape has a weird node (if the mouse is over a text object, then there's no tagName
  while (myElement && ((myElement.tagName && myElement.tagName!="TR") || !myElement.tagName))
  {
    myElement=myElement.parentNode;
  }

  // If you don't want a particular row to be highlighted, set it's id to "header"
  // If you don't want a particular row to be highlighted, set it's id to "header"
  if (!myElement || (myElement && myElement.id && myElement.id=="header") )
    return;
		  
  // Highlight every cell on the row
  if (myElement)
  {
    var tableRow=myElement;
    
    // Save the backgroundColor style OR the style class of the row (if defined)
    if (tableRow)
    {
	  savedStates[savedStateCount]=saveBackgroundStyle(tableRow);
      savedStateCount++;
    }

    // myElement is a <TR>, then find the first TD
    var tableCell=findNode(myElement, "TD");    

    var i=0;
    // Loop through every sibling (a sibling of a cell should be a cell)
    // We then highlight every siblings
    while (tableCell)
    {
      // Make sure it's actually a cell (a TD)
      if (tableCell.tagName=="TD")
      {
        // If no style has been assigned, assign it, otherwise Netscape will 
        // behave weird.
        if (!tableCell.style)
        {
          tableCell.style={};
        }
        else
        {
          savedStates[savedStateCount]=saveBackgroundStyle(tableCell);        
          savedStateCount++;
        }
        // Assign the highlight color
        tableCell.style["backgroundColor"]=highlightColor;

        // Optional: alter cursor
        tableCell.style.cursor='default';
        i++;
      }
      // Go to the next cell in the row
      tableCell=tableCell.nextSibling;
    }
  }
}

/////////////////////////////////////////////////////
// This function is to be assigned to a <table> mouse event handler.
// If the element that fired the event is within a table row,
//   this function will highlight the row.
/////////////////////////////////////////////////////
function trackTableHighlight(mEvent, highlightColor)
{
  if (!mEvent)
    mEvent=window.event;
		
  // Internet Explorer
  if (mEvent.srcElement)
  {
    highlightTableRow( mEvent.srcElement, highlightColor);
  }
  // Netscape and Firefox
  else if (mEvent.target)
  {
    highlightTableRow( mEvent.target, highlightColor);		
  }
}

/////////////////////////////////////////////////////
// Highlight table row.
// newElement could be any element nested inside the table
// highlightColor is the color of the highlight
/////////////////////////////////////////////////////
function highlightTableRowVersionA(myElement, highlightColor)
{
  var i=0;
  // Restore color of the previously highlighted row
  for (i; i<savedStateCount; i++)
  {
    restoreBackgroundStyle(savedStates[i]);          
  }
  savedStateCount=0;

  // If you don't want a particular row to be highlighted, set it's id to "header"
  if (!myElement || (myElement && myElement.id && myElement.id=="header") )
    return;
		  
  // Highlight every cell on the row
  if (myElement)
  {
    var tableRow=myElement;
    
    // Save the backgroundColor style OR the style class of the row (if defined)
    if (tableRow)
    {
	  savedStates[savedStateCount]=saveBackgroundStyle(tableRow);
      savedStateCount++;
    }

    // myElement is a <TR>, then find the first TD
    var tableCell=findNode(myElement, "TD");    

    var i=0;
    // Loop through every sibling (a sibling of a cell should be a cell)
    // We then highlight every siblings
    while (tableCell)
    {
      // Make sure it's actually a cell (a TD)
      if (tableCell.tagName=="TD")
      {
        // If no style has been assigned, assign it, otherwise Netscape will 
        // behave weird.
        if (!tableCell.style)
        {
          tableCell.style={};
        }
        else
        {
          savedStates[savedStateCount]=saveBackgroundStyle(tableCell);        
          savedStateCount++;
        }
        // Assign the highlight color
        tableCell.style["backgroundColor"]=highlightColor;

        // Optional: alter cursor
        tableCell.style.cursor='default';
        i++;
      }
      // Go to the next cell in the row
      tableCell=tableCell.nextSibling;
    }
  }
}


function checkExampleQuestionExampleAnswer(questionID, qType, correctResponse, IDstring)
{
	
	divDisplayHide("exampleQuestionAnswerCorrect"+questionID);
	divDisplayHide("exampleQuestionAnswerInCorrect"+questionID);
	
	var currentOptionID="";


	var isCorrect=false; // Assume its false for now
	
	if(qType=="reflection" || qType=="reflectionText") // If its reflection ONLY show the correct answer as there is no incorrect answer
	{
		isCorrect=true;
	}
	else
	{
		if(qType=="radio") // Check the single response for this and compare to the correct response
		{
			// Get all response and stick into an array
			var optionIDArray = IDstring.split(',');
			
			// get the radio response
			if(document.getElementById("option"+correctResponse).checked)
			{
				isCorrect=true;
				
				// If its correct and radio button type ALL are tryue so show correct for all
				for (var i = 0; i < optionIDArray.length; i++)
				{	
					currentOptionID = optionIDArray[i];	
					divDisplayShow("correctFeedback"+currentOptionID); // Show the correct feedback
					divDisplayHide("incorrectFeedback"+currentOptionID); // Hide the incorrect feedback
	
				}				
			}
			else // They are ALL wrong
			{
				for (var i = 0; i < optionIDArray.length; i++)
				{	
					currentOptionID = optionIDArray[i];	
					divDisplayShow("incorrectFeedback"+currentOptionID); // Show the incorrect feedback
					divDisplayHide("correctFeedback"+currentOptionID); // Hide the correct feedback
	
				}		
			}
		}
		else // Its a checkbox so check all values of checkboxes in the IDstring and compare to the correct response string
		{
			//Turn the IDstring into an array and go through the array of values
			var optionIDArray = IDstring.split(',');
			var correctIDArray = correctResponse.split(',');			
			var responseArrayStr="";
			var responseArrayLocation="";
			for (var i = 0; i < optionIDArray.length; i++)
			{
				currentOptionID = optionIDArray[i];				
				responseArrayLocation = correctResponse.indexOf(currentOptionID);

				if(document.getElementById("option"+optionIDArray[i]).checked)
				{
					if(responseArrayLocation>=0) // Its a correct answer AND is ticked
					{
						// Ticked and correct
						divDisplayShow("correctFeedback"+currentOptionID);
						divDisplayHide("incorrectFeedback"+currentOptionID);
					}
					else
					{
						// ticked and incorrect
						divDisplayShow("incorrectFeedback"+currentOptionID);
						divDisplayHide("correctFeedback"+currentOptionID);						
					}
					
					responseArrayStr=responseArrayStr+optionIDArray[i]+",";					
				}
				else
				{
					if(responseArrayLocation>=0) // Its a correct answer AND is ticked
					{
						// not ticked and incorrect
						divDisplayShow("incorrectFeedback"+currentOptionID);	
						divDisplayHide("correctFeedback"+currentOptionID);											
					}
					else
					{
						// not ticked and correct
						divDisplayShow("correctFeedback"+currentOptionID);	
						divDisplayHide("incorrectFeedback"+currentOptionID);
					}					
				}
			}
			
			// Now remove the last comma
			responseArrayStr = responseArrayStr.slice(0,-1);		
			//alert ("Correct = "+correctResponse+" : Response = "+responseArrayStr);
			
			if(correctResponse==responseArrayStr)
			{
				isCorrect=true;
			}
			
			
		}
	}
	
	
	if(isCorrect==true)
	{
		divDisplayShow("exampleQuestionAnswerCorrect"+questionID);
	}
	else
	{
		divDisplayShow("exampleQuestionAnswerInCorrect"+questionID);
	}
	
	
	
}



