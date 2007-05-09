/***********************************************************************************************************
 * Contains functions for creating and sending Ajax requests.
 * This code needs to be a bit more careful about creating separate requests for different events - if 
 * somebody presses a button several times in quick succession (such as the delete grouping button) then 
 * we get an error. 
 * There also seems to a problem with IE - need to check this out
 ***********************************************************************************************************/


<?php
    echo "var courseid = $courseid;"; 
    echo "var sesskey = '$sesskey';";
?>

/*
 * Creates an XMLHttpRequest object 
 * @return The XMLHttpRequest object created. 
 */
function createRequest() {
    var newrequest = null;
    try {
        newrequest = new XMLHttpRequest();
    } catch (trymicrosoft) {
        // Deal with Microsoft browsers 
        try {
            newrequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (othermicrosoft) {
            try {
                newrequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed) {
                newrequest = null;
            }
        }
    }

    if (newrequest == null) {
        alert("Error creating request object!");
    } else {
        return newrequest;
    }
}

/*
 * Sends an Ajax post request 
 * @param request - The XMLHttpRequest object
 * @param url - The URL to send the request to 
 * @param requeststring - The string containing the variables to send in the post request - the format
 * is basically the same as a GET string
 * @callbackfunction  - The function to call when the response to the request is received 
*/
function sendPostRequest(postrequest, url, requeststring, callbackfunction) {
    // Add on the date and time to get round caching problem
    url = url + "?dummy=" + new Date().getTime();
    // Add the course id and sesskey so we can check these on the server 
    requeststring = 'courseid='+courseid+'&'+'sesskey='+sesskey+'&'+requeststring;
    postrequest.abort();
    postrequest.open('post',  url, true);
    postrequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    postrequest.onreadystatechange = callbackfunction;
    postrequest.send(requeststring);
}

function checkAjaxResponse(request) {
    process = false;

    if (request.readyState == 4 && request.status == 200) {
        process = true;
    }
    if (request.readyState == 4 && request.status != 200) {
        alert('An error has occurred - the page returned a '+ request.status + ' error');
    }
    return process;    
}

var responseFailure = function(o){ 
    alert("Failure callback");
}
