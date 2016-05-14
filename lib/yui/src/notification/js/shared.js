var DIALOGUE_PREFIX,
    BASE,
    CONFIRMYES,
    CONFIRMNO,
    TITLE,
    QUESTION,
    CSS;

DIALOGUE_PREFIX = 'moodle-dialogue',
BASE = 'notificationBase',
CONFIRMYES = 'yesLabel',
CONFIRMNO = 'noLabel',
TITLE = 'title',
QUESTION = 'question',
CSS = {
    BASE : 'moodle-dialogue-base',
    WRAP : 'moodle-dialogue-wrap',
    HEADER : 'moodle-dialogue-hd',
    BODY : 'moodle-dialogue-bd',
    CONTENT : 'moodle-dialogue-content',
    FOOTER : 'moodle-dialogue-ft',
    HIDDEN : 'hidden',
    LIGHTBOX : 'moodle-dialogue-lightbox'
};

// Set up the namespace once.
M.core = M.core || {};
