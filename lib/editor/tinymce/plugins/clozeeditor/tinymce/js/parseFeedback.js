// JavaScript Document

// Parse Feedback

// delete messy stuff between comments
function deleteInbetweenComments(string) {
    var temp = string;
    temp = strstr(temp, '~');
    temp = strstr(temp, '#');
    return temp;
}

// retrieve comment
function getComment(string, count) {
    var temp = getAnswerCode(string);
    // alert(temp);

    for (var i = 1; i <= countAnswers(); i++) {
        if (i == count) {
            // the last comment is not followed by a ~, as this marks an answer
            if (i != countAnswers()) {
                temp = deleteAfterString(temp, '~');
            }
            temp = deleteTillChar(temp, '#');

            if (typeof(temp) == 'string') {
                return temp;
            } else {
                return '';
            }
        } else {
            temp = deleteInbetweenAnswers(temp);
        }
    }
}
