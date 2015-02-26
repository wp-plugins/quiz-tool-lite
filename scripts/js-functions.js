


// This sets a global variable divName window object
// Needed for updating a unique div after update
var divName="";

jQuery.fn.isChildof = function(b){
    return (this.parents(b).length > 0);
};


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



function checkExampleQuestionExampleAnswer(questionID, qType, correctResponse, IDstring)
{
	
	
	divDisplayHide("exampleQuestionAnswerCorrect"+questionID);
	divDisplayHide("exampleQuestionAnswerInCorrect"+questionID);
	
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
		else if(qType=="check") // Its a checkbox so check all values of checkboxes in the IDstring and compare to the correct response string
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
		else if(qType=="text") // Check the inputted value against possible answers
		{
			
			//Turn the IDstring into an array and see if the input is in the array
			var optionValueArray = IDstring.split(',');
			var myResponse = document.getElementById("textBoxID"+questionID).value;
			myResponse = myResponse.toLowerCase();
			if(optionValueArray.indexOf(myResponse) > -1)
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
//	alert (userResponse);
	
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



 // used with the tabs to determind the inital page based on ?tab=1 questystring
function getParam(name) {
    var query = location.search.substring(1);
    if (query.length) {
        var parts = query.split('&');
        for (var i = 0; i < parts.length; i++) {
            var pos = parts[i].indexOf('=');
            if (parts[i].substring(0,pos) == name) {
                return parts[i].substring(pos+1);
            }
        }
    }
    return 1;
}