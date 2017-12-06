YUI.add('moodle-dataformfield_ratingmdl-rater', function(Y) {

    function RATER(config) {
        RATER.superclass.constructor.apply(this, arguments);
    }

    RATER.NAME = 'moodle-dataformfield_ratingmdl-rater';
    RATER.ATTRS = {
        api: {value: M.cfg.wwwroot + '/mod/dataform/field/ratingmdl/rate_ajax.php'},
        fieldid: {value: 0},
        entryid: {value: 0}
    };

    Y.extend(RATER, Y.Base, {
        form: null,
        selector: null,
        ratingcount: null,
        ratingavg: null,
        ratingmax: null,
        ratingmin: null,
        ratingsum: null,

        initializer: function() {
            var fieldid = this.get('fieldid');
            var entryid = this.get('entryid');

            this.form = Y.one('#ratingpost_' + fieldid + '_' + entryid);
            if (!this.form) {
                Y.log('Could not locate the rating form', 'debug');
                return;
            }

            this.selector = Y.one('#ratingmenu_' + fieldid + '_' + entryid);
            if (!this.selector) {
                Y.log('Could not locate the rating selector', 'debug');
                return;
            }
            this.selector.on('change', this.submit_rating, this);
            // Hide the submit buttons.
            Y.one('#ratingpostsubmit_' + fieldid + '_' + entryid).setStyle('display', 'none');

            this.ratingcount = Y.one('#ratingcount_' + fieldid + '_' + entryid);
            this.ratingavg = Y.one('#ratingavg_' + fieldid + '_' + entryid);
            this.ratingmax = Y.one('#ratingmax_' + fieldid + '_' + entryid);
            this.ratingmin = Y.one('#ratingmin_' + fieldid + '_' + entryid);
            this.ratingsum = Y.one('#ratingsum_' + fieldid + '_' + entryid);
        },

        submit_rating : function(e) {
            var theinputs = this.form.all('.ratinginput');
            var thedata = [];

            var inputssize = theinputs.size();
            for (var i = 0; i < inputssize; i++) {
                if (theinputs.item(i).get("name") != "returnurl") {
                    // Dont include return url for ajax requests.
                    thedata[theinputs.item(i).get("name")] = theinputs.item(i).get("value");
                }
            }

            var scope = this;
            var cfg = {
                method: 'POST',
                on: {
                    complete : function(tid, outcome, args) {
                        try {
                            if (!outcome) {
                                alert('IO FATAL');
                                return false;
                            }
                            var data = Y.JSON.parse(outcome.responseText);
                            if (data.success){
                                var entryid = scope.get('entryid');
                                if (data.itemid && data.itemid == entryid) {
                                    if (scope.ratingcount) {
                                        scope.ratingcount.set('innerHTML', data.ratingcount);
                                    }
                                    if (scope.ratingavg) {
                                        scope.ratingavg.set('innerHTML', data.ratingavg);
                                    }
                                    if (scope.ratingmax) {
                                        scope.ratingmax.set('innerHTML', data.ratingmax);
                                    }
                                    if (scope.ratingmin) {
                                        scope.ratingmin.set('innerHTML', data.ratingmin);
                                    }
                                    if (scope.ratingsum) {
                                        scope.ratingsum.set('innerHTML', data.ratingsum);
                                    }
                                }
                                return true;
                            }
                            else if (data.error){
                                alert(data.error);
                                scope.selector.set('selectedIndex', data.value);
                            }
                        } catch(e) {
                            alert(e.message + " " + outcome.responseText);
                        }
                        return false;
                    }
                },
                arguments: {
                    scope: scope
                },
                headers: {
                },
                data: build_querystring(thedata)
            };
            Y.io(this.get('api'), cfg);
        }
    });

    // Define a name space to call.
    M.dataformfield_ratingmdl = M.dataformfield_ratingmdl || {};
    M.dataformfield_ratingmdl.rater = M.dataformfield_ratingmdl.rater || {};
    M.dataformfield_ratingmdl.rater.init = M.dataformfield_ratingmdl.rater.init || function(options) {
        return new RATER(options);
    };

}, '@VERSION@', {
  requires: ['node']
});
