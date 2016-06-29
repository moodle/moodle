YUI.add('moodle-core-languninstallconfirm', function (Y, NAME) {

/* global NAME */

/**
 * Home for a Confirmation class.
*
* @module moodle-core-languninstallconfirm
*/

/**
* A class for a language uninstall confirmation.
*
* @class M.core.languninstallconfirm
* @constructor
* @extends Base
*/
function Confirmation() {
    Confirmation.superclass.constructor.apply(this, arguments);
}

var SELECTORS = {
        UNINSTALLBUTTON: '#languninstallbutton',
        UNINSTALLSELECT: '#menuuninstalllang option',
        ENGLISHOPTION:   '#menuuninstalllang option[value=\'en\']'
};

Confirmation.NAME = NAME;
Confirmation.ATTRS = {
    /**
     * Uninstall url
     *
     * @property uninstallUrl
     * @type string
     */
    uninstallUrl: {
        validator: Y.Lang.isString
    }
};
Y.extend(Confirmation, Y.Base, {
        /**
         * Initializer.
         * Registers onclicks.
         *
         * @method initializer
         */
        initializer: function() {
            Y.one(SELECTORS.UNINSTALLBUTTON).on('click', this._confirm, this);
        },
        /**
         * Confirmation.
         * Displays the confirmation dialogue.
         *
         * @method _confirm
         * @protected
         * @param {EventFacade} e
         */
        _confirm: function(e) {
            e.preventDefault();
            var selectedLangCodes = [];
            var selectedLangNames = [];

            Y.all(SELECTORS.UNINSTALLSELECT).each(function(option){
                if (option.get('selected')){
                    selectedLangCodes.push(option.getAttribute('value'));
                    selectedLangNames.push(option.get('text'));
                }
            });
            // Nothing was selected, show warning.
            if (selectedLangCodes.length === 0){
                new M.core.alert({ message: M.util.get_string('selectlangs', 'tool_langimport') }).show();
                return;
            } else if (selectedLangCodes.indexOf('en')> -1) { // Don't uninstall english.
                Y.one(SELECTORS.ENGLISHOPTION).set('selected',false);
                new M.core.alert({ message: M.util.get_string('noenglishuninstall', 'tool_langimport') }).show();
                return;
            }
            var confirmationConfig = {
                modal:  true,
                visible  :  false,
                centered :  true,
                title :  M.util.get_string('uninstall','tool_langimport'),
                question :  M.util.get_string('uninstallconfirm','tool_langimport', selectedLangNames.join(", "))
            };
            new M.core.confirm(confirmationConfig)
                .show()
                .on('complete-yes', this._uninstall, this, selectedLangCodes);
        },
        /**
         * Uninstall.
         * Redirects to an uninstall process.
         *
         * @method _uninstall
         * @protected
         * @param {EventFacade} e
         * @param {Array} langCodes array of lang codes to be uninstalled
         */
        _uninstall : function(e, langCodes) {
            Y.config.win.location.href = this.get('uninstallUrl') + '?mode=4' +
                                         '&sesskey=' + M.cfg.sesskey +
                                         '&confirmtouninstall=' + langCodes.join('-');
        }

});

Y.namespace('M.core.languninstallconfirm').Confirmation = Confirmation;
Y.namespace('M.core.languninstallconfirm').init = function(config) {
    return new Confirmation(config);
};

}, '@VERSION@', {"requires": ["base", "node", "moodle-core-notification-confirm", "moodle-core-notification-alert"]});
