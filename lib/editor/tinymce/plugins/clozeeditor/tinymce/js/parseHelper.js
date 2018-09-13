// JavaScript Document

// Delete vacuous white spaces in head and tail position
// @str: string which shall be trimmed
function trim(str) {
    // TODO: fill with content
    return str;
}

function isStringInString(string1, searchS) {
    if (string1.indexOf(searchS) == -1) {
        return false;
    } else {
        return true;
    }
}

function deleteLastChar(string) {
    return string.slice(0, -1);
}

// Include '\' in front of all ". This might be vacuous in JavaScript, but was important in PHP
function stripslashes(str) {
    // is this still needed in JavaScript?
    return str;
}

function toggleThrottle() {
    // derived version auf "getElementsByClassName"

    var allHTMLTags = new Array();
    // Create Array of All HTML Tags
    var allHTMLTags = document.getElementsByTagName("*");

    // Loop through all tags using a for loop
    for (i = 0; i < allHTMLTags.length; i++) {
        // Get all tags with the specified class name.
        if (allHTMLTags[i].className == "table_value_throttle") {
            if (getQuizTypeElement().value == "NUMERICAL") {
                allHTMLTags[i].style.display = 'inherit';
            } else {
                allHTMLTags[i].style.display = 'none';
            }
        }
    }
}

function deleteTillChar(string, char) {
    var temp = strstr(string, char);
    temp = substr(temp, 1);
    return temp;
}

function deleteAfterString(completeS, searchS) {
    var temp = strstr(completeS, searchS, true);
    return temp;

}

function getAnswerCode(string) {
    var temp = string;

    temp = deleteTillChar(temp, ':');
    temp = deleteTillChar(temp, ':');

    var run = true;
    while (run) {
        if ((temp.charAt(temp.length - 2) == '}') || (temp.charAt(temp.length - 1) == '}')){
            temp = deleteLastChar(temp);
        } else {
            run = false;
        }
    }
    return temp;
}
