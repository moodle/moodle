
M.mod_survey = {};

M.mod_survey.init = function(Y) {
    if (document.getElementById('surveyform')) {
        var surveyform = document.getElementById('surveyform');
        Y.YUI2.util.Event.addListener('surveyform', "submit", function(e) {
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
                Y.YUI2.util.Event.preventDefault(e);
                return false;
            } else {
                return true;
            }
        });
    }
};
