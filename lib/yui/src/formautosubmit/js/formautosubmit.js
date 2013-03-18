var CSS,
    FORMAUTOSUBMITNAME = 'core-formautosubmit',
    FORMAUTOSUBMIT,
    INITIALIZED = false;

// The CSS selectors we use
CSS = {
    AUTOSUBMIT : 'autosubmit'
};

FORMAUTOSUBMIT = function() {
    FORMAUTOSUBMIT.superclass.constructor.apply(this, arguments);
};

Y.extend(FORMAUTOSUBMIT, Y.Base, {

    /**
      * Initialize the module
      */
    initializer : function() {
        // Set up local variables
        var applyto,
            thisselect;
        // We only apply the delegation once
        if (!INITIALIZED) {
            INITIALIZED = true;
            applyto = Y.one('body');

            // We don't listen for change events by default as using the keyboard triggers these too.
            applyto.delegate('key', this.process_changes, 'press:13', 'select.' + CSS.AUTOSUBMIT, this);
            applyto.delegate('click', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);

            if (Y.UA.os === 'macintosh' && Y.UA.webkit) {
                // Macintosh webkit browsers like change events, but non-macintosh webkit browsers don't.
                applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);
            }
            if (Y.UA.ios) {
                // IOS doesn't trigger click events because it's touch-based.
                applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);
            }
        }

        // Assign this select items 'nothing' value and lastindex (current value)
        if (this.get('selectid')) {
            thisselect = Y.one('select#' + this.get('selectid'));
            if (thisselect) {
                if (this.get('nothing')) {
                    thisselect.setData('nothing', this.get('nothing'));
                }
                thisselect.setData('startindex', thisselect.get('selectedIndex'));
            } else {
                Y.log("Warning: A single_select element was renderered, but the output is not displayed on the page.");
            }
        }
    },

    /**
      * Check whether the select element was changed
      */
    check_changed : function(e) {
        var select,
            nothing,
            startindex,
            currentindex,
            previousindex;
        select = e.target.ancestor('select.' + CSS.AUTOSUBMIT, true);
        if (!select) {
            return false;
        }

        nothing = select.getData('nothing');
        startindex = select.getData('startindex');
        currentindex = select.get('selectedIndex');

        previousindex = select.getAttribute('data-previousindex');
        select.setAttribute('data-previousindex', currentindex);
        if (!previousindex) {
            previousindex = startindex;
        }

        // Check whether the field has changed, and is not the 'nothing' value
        if ((nothing===false || select.get('value') !== nothing)
                && startindex !== select.get('selectedIndex') && currentindex !== previousindex) {
            return select;
        }
        return false;
    },

    /**
      * Process any changes
      */
    process_changes : function(e) {
        var select = this.check_changed(e),
            form;
        if (select) {
            form = select.ancestor('form', true);
            form.submit();
        }
    }
},
{
    NAME : FORMAUTOSUBMITNAME,
    ATTRS : {
        selectid : {
            'value' : ''
        },
        nothing : {
            'value' : ''
        },
        ignorechangeevent : {
            'value' : false
        }
    }
});

M.core = M.core || {};
M.core.init_formautosubmit = M.core.init_formautosubmit || function(config) {
    return new FORMAUTOSUBMIT(config);
};
