YUI.add('moodle-dataformfield_entrystate-stater', function(Y) {

    function STATER(config) {
        STATER.superclass.constructor.apply(this, arguments);
    }

    STATER.NAME = 'moodle-dataformfield_entrystate-stater';
    STATER.ATTRS = {
        api: {value: M.cfg.wwwroot + '/mod/dataform/field/entrystate/ajax.php'},
        d: {value: 0},
        fieldid: {value: 0},
        entryid: {value: 0},
        sesskey: {value: ''}
    };

    Y.extend(STATER, Y.Base, {
        container: null,

        initializer: function() {
            var entryid = this.get('entryid');
            var fieldid = this.get('fieldid');

            this.container = Y.one('#entrystates_' + entryid + '_' + fieldid);
            if (!this.container) {
                Y.log('Could not locate the state container', 'debug');
                return;
            }
            this.container.all('a').set('href', '#');
            this.container.all('a').on('click', this.submit_state, this);
        },

        submit_state: function(e) {
            e.preventDefault();

            var thedata = [];
            thedata['d'] = this.get('d');
            thedata['fieldid'] = this.get('fieldid');
            thedata['entryid'] = this.get('entryid');
            thedata['sesskey'] = this.get('sesskey');

            // Get the link.
            var link = e.target;
            if (e.target.get('tagName').toLowerCase() != 'a') {
                link = e.target.get('parentNode');
            }
            // Get the requested state.
            var arr = link.get('id').split('_');
            thedata['state'] = arr[2];

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
                                if (data.entryid && (data.entryid == entryid)) {
                                    if (scope.container) {
                                        scope.container.set('innerHTML', data.statescontent);
                                        scope.container.all('a').set('href', '#');
                                        scope.container.all('a').on('click', scope.submit_state, scope);
                                    }
                                }
                                return true;
                            }
                            else if (data.error){
                                alert(data.error);
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
    M.dataformfield_entrystate = M.dataformfield_entrystate || {};
    M.dataformfield_entrystate.stater = M.dataformfield_entrystate.stater || {};
    M.dataformfield_entrystate.stater.init = M.dataformfield_entrystate.stater.init || function(options) {
        return new STATER(options);
    };

}, '@VERSION@', {
  requires: ['node']
});
