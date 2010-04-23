function checkform(e) {
    var error = false;
    if (document.getElementById('surveyform')) {
        var surveyform = document.getElementById('surveyform');
        for (var i=0; i < surveycheck.questions.length; i++) {
            var tempquestion = surveycheck.questions[i];
            if (surveyform[tempquestion['question']][tempquestion['default']].checked) {
                error = true;
            }
        }
    }
    if (error) {
        alert(M.str.survey.questionsnotanswered);
        YAHOO.util.Event.preventDefault(e);
        return false;
    } else {
        return true;
    }
}

function survey_attach_onsubmit() {
    if (document.getElementById('surveyform')) {
        var surveyform = document.getElementById('surveyform');
        YAHOO.util.Event.addListener('surveyform', "submit", checkform);
    }
}