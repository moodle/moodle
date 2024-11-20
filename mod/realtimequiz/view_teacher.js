/**
 * Code for a teacher running a quiz
 *
 * @author: Davosmith
 * @package realtimequiz
 **/

realtimequiz.clickednext = 0; // The question number of the last time the teacher clicked 'next'

function realtimequiz_first_question() {
    var sessionname = document.getElementById('sessionname').value;
    if (sessionname.length > 0) {
        sessionname = '&sessionname=' + encodeURIComponent(sessionname);
    }
    realtimequiz_create_request('requesttype=startquiz&quizid=' + realtimequiz.quizid + '&userid=' + realtimequiz.userid + sessionname);
    //Userid needed to authenticate request
}

function realtimequiz_next_question() {
    realtimequiz_update_next_button(false);
    realtimequiz_create_request('requesttype=nextquestion&quizid=' + realtimequiz.quizid + '&userid=' + realtimequiz.userid + '&currentquestion=' + realtimequiz.questionnumber);
    realtimequiz.clickednext = realtimequiz.questionnumber;
    //Userid needed to authenticate request
}

function realtimequiz_update_next_button(enabled) {
    if (!realtimequiz.controlquiz) {
        return;
    }
    if (enabled) {
        if (realtimequiz.clickednext == realtimequiz.questionnumber) { // Teacher already clicked 'next' for this question, so resend that request
            realtimequiz_next_question();
        } else {
            document.getElementById('questioncontrols').innerHTML = '<input type="button" onclick="realtimequiz_next_question()" value="' + realtimequiz.text['next'] + '" />';
        }

    } else {
        document.getElementById('questioncontrols').innerHTML = '<input type="button" onclick="realtimequiz_next_question()" value="' + realtimequiz.text['next'] + '" disabled="disabled" />';
    }
}

function realtimequiz_start_quiz() {
    realtimequiz.controlquiz = true;
    realtimequiz_first_question();
}

function realtimequiz_start_new_quiz() {
    var confirm = window.confirm(realtimequiz.text['startnewquizconfirm']);
    if (confirm == true) {
        realtimequiz_start_quiz();
    }
}

function realtimequiz_reconnect_quiz() {
    realtimequiz.controlquiz = true;
    realtimequiz_create_request('requesttype=teacherrejoin&quizid=' + realtimequiz.quizid);
}

function realtimequiz_init_teacher_view() {
    realtimequiz.controlquiz = false;     // Set to true when controlling the quiz
    var msg = "<div style='text-align: center;'>";
    if (realtimequiz.alreadyrunning) {
        msg += "<input type='button' onclick='realtimequiz_reconnect_quiz();' value='" + realtimequiz.text['reconnectquiz'] + "' />";
        msg += "<p>" + realtimequiz.text['reconnectinstruct'] + "</p>";
        msg += "<input type='button' onclick='realtimequiz_start_new_quiz();' value='" + realtimequiz.text['startnewquiz'] + "' /> <input type='text' name='sessionname' id='sessionname' maxlength='255' value='' />";
        msg += "<p>" + realtimequiz.text['teacherstartnewinstruct'] + "</p>";
    } else {
        msg += "<input type='button' onclick='realtimequiz_start_quiz();' value='" + realtimequiz.text['startquiz'] + "' /> <input type='text' name='sessionname' id='sessionname' maxlength='255' value='' />";
        msg += "<p>" + realtimequiz.text['teacherstartinstruct'] + "</p>";
    }
    msg += "<input type='button' onclick='realtimequiz_join_quiz();' value='" + realtimequiz.text['joinquizasstudent'] + "' />";
    msg += "<p id='status'>" + realtimequiz.text['teacherjoinquizinstruct'] + "</p></div>";
    document.getElementById('questionarea').innerHTML = msg;
}


