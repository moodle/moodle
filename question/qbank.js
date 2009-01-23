// This script is included by question_bank_view in question/editlib.php.

question_bank = {
    strselectall: '',
    strdeselectall: '',
    headercheckbox: null,
    firstcheckbox: null,

    init_checkbox_column: function(strselectall, strdeselectall, firstcbid) {
        question_bank.strselectall = strselectall;
        question_bank.strdeselectall = strdeselectall;

        // Find the header checkbox, and initialise it.
        question_bank.headercheckbox = document.getElementById('qbheadercheckbox');
        question_bank.headercheckbox.disabled = false;
        question_bank.headercheckbox.title = strselectall;

        // Find the first real checkbox.
        question_bank.firstcheckbox = document.getElementById(firstcbid);

        // Add the event handler.
        YAHOO.util.Event.addListener(question_bank.headercheckbox, 'change', question_bank.header_checkbox_click);
    },

    header_checkbox_click: function() {
        if (question_bank.firstcheckbox.checked) {
            deselect_all_in('TABLE', null, 'categoryquestions');
            question_bank.headercheckbox.title = question_bank.strselectall;
        } else {
            select_all_in('TABLE', null, 'categoryquestions');
            question_bank.headercheckbox.title = question_bank.strdeselectall;
        }
        question_bank.headercheckbox.checked = false;
    }
};
