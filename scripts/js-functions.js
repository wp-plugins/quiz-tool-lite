


// This sets a global variable divName window object
// Needed for updating a unique div after update
var divName="";

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



//function ajaxQuestionResponseUpdate(elementID, questionID, currentUser)
function ajaxQuestionResponseUpdate(elementID, questionID, IDStr, qType, currentUser)
{
	var userResponse = '';
	//if is refection question with text inout, save it to the userResponse string for update
	if (qType=='reflectionText'){
		userResponse = document.getElementById(elementID).value;
	}else{
		//if is single or multi response question, save the selected optionID(s) to the userResponse string for update
		var optionIDArray = IDStr.split(',');

		for (var i = 0; i < optionIDArray.length; i++)
		{		
			//currentOptionID = optionIDArray[i];	
			if(document.getElementById("option"+optionIDArray[i]).checked){
				
				userResponse = userResponse + optionIDArray[i] + ',';
			}
		}		
	}
	
	//alert('test');		
	//alert (userResponse);
	
	// We need question ID AND the logged in user AND the value passed to the beneath query	
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: {			
			"action": "addResponseToDatabase",
			"userResponse": userResponse,
			"currentUser": currentUser,
			"questionID": questionID
		},
		success: function(data){}
	});
	
	return false;		
	
}

//function for picking a date, e.g. used in edit quiz page
jQuery(document).ready(function() {

    jQuery('.MyDate').datepicker({
        dateFormat : 'dd-mm-yy'
    });
});
