var SELECTOR = {
    COURSELINK: '#menulinkcourseid',
    URLLINK: '#id_linkurl'
};

M.mod_checklist = M.mod_checklist || {};
M.mod_checklist.linkselect = {
    courselink: null,
    urllink: null,

    init: function() {
        this.courselink = Y.one(SELECTOR.COURSELINK);
        this.urllink = Y.one(SELECTOR.URLLINK);

        if (!this.courselink || !this.urllink) {
            return; // If they're not both present, then there is nothing to do.
        }

        this.courselink.on('valuechange', this.fieldChanged, this);
        this.urllink.on('valuechange', this.fieldChanged, this);
        this.fieldChanged();
    },

    fieldChanged: function() {
        var courseVal, urlVal;

        courseVal = this.courselink.get('value');
        urlVal = this.urllink.get('value');

        if (courseVal) {
            this.urllink.set('value', '');
            this.urllink.set('disabled', true);
        } else if (urlVal) {
            this.courselink.set('value', null);
            this.courselink.set('disabled', true);
        } else {
            this.urllink.set('disabled', false);
            this.courselink.set('disabled', false);
        }
    }
};