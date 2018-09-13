YUI.add('moodle-block_messageteacher-form', function (Y, NAME) {

/**
 * Defines the javascript module for the messageteacher block
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.block_messageteacher = M.block_messageteacher || {};

M.block_messageteacher.form = {
    overlay: null,

    init: function() {
        // Create an overlay (like the ones that Help buttons display) for
        // showing output from asynchronous call.

        Y.all('.messageteacher_link').on('click', this.show_form, this);
    },

    /**
     * Displays the specifies response in the overlay
     *
     * @param data The text to display
     */
    show_response: function(data) {
        // Create a node from the data
        if (!this.overlay) {
            this.overlay = new M.core.dialogue({
                visible: false,
                modal: true,
                close: true,
                draggable: false,
                width: '50%'
            });
            this.overlay.render();
        }
        this.overlay.set('bodyContent', Y.Node.create(data));
        this.overlay.show(); // Show the overlay
    },

    hide_response: function() {
        this.overlay.hide();
    },

    /**
     *
     */
    show_form: function(e) {
        e.preventDefault();
        this.xhr = Y.io(e.currentTarget.get('href'), {
            method: 'GET',
            context: this,
            on: {
                success: function(id, o) {
                    response = Y.JSON.parse(o.responseText);
                    this.show_response(response.output);
                    if (response.script.length > 0) {
                        script = Y.one('#messageteacher_dynamic_script');
                        if (script) {
                            script.remove();
                        }
                        el = document.createElement('script');
                        el.id = 'ilp_dynamic_script';
                        el.textContent = response.script;
                        document.body.appendChild(el);
                    }
                    Y.one('#mform1').on('submit', M.block_messageteacher.form.submit_form);
                    Y.one('#id_message').focus(); // Focus the message box
                },
                failure: function(id, o) {
                    this.show_response(o.responseText);
                }
            }
        });
    },

    submit_form: function(e) {
        e.preventDefault();
        var mform = document.getElementById('mform1');

        this.xhr = Y.io(e.target.get('action'), {
            method: 'POST',
            context: this,
            form: mform,
            on: {
                success: function(id, o) {
                    response = Y.JSON.parse(o.responseText);
                    M.block_messageteacher.form.show_response(response.output);
                },
                failure: function(id, o) {
                    M.block_messageteacher.form.show_response(o.responseText);
                }
            }
        });
    }
}


}, '@VERSION@', {"requires": ["base", "io", "io-form", "node", "json", "moodle-core-notification"]});
