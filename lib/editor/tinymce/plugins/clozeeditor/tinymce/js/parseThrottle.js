// JavaScript Document

// Throttle Parser

// retrieve throttle value
function getThrottle(string, count) {
    var temp = getAnswerCode(string);

    for (var i = 1; i <= countAnswers(); i++) {
        if (i == count) {
            temp = deleteTillChar(temp, ':');
            temp = deleteAfterString(temp, '#');

            if (typeof(temp) == 'string') {
                return temp;
            } else {
                return '';
            }
        } else {
            temp = deleteInbetweenThrottles(temp);
        }
    }
    return temp;
}

function deleteInbetweenThrottles(string) {
    return deleteTillChar(string, '~');
}
