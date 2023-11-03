/**
 * Add a create new group modal to the page.
 *
 * @module     core_group/newgroup
 * @class      NewGroup
 * @package    core_group
 * @copyright  2017 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/fragment', 'block_learnerscript/ajax', 'core/yui', 'core/templates', 'core/modal'],
        function($, Str, ModalFactory, ModalEvents, Fragment, Ajax, Y, Templates, Modal) {

    /**
     * Constructor
     *
     * @param {String} selector used to find triggers for the new group modal.
     * @param {int} contextid
     *
     * Each call to init gets it's own instance of this class.
     */
    var NewGroup = function(args, url) {
        this.args = args;
        this.contextid = 1;
        this.url = url;
        this.nodeContent = args.nodeContent || 'ajaxForm';
        this.init(this.args);
    };

    /**
     * @var {Modal} modal
     * @private
     */
    NewGroup.prototype.modal = null;

    /**
     * @var {int} contextid
     * @private
     */
    NewGroup.prototype.contextid = -1;

    /**
     * Initialise the class.
     *
     * @param {String} selector used to find triggers for the new group modal.
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.init = function(args) {
                var resp = this.getBody();
                $('body').append("<div class='"+this.nodeContent+"'></div>");
                var self = this;
                resp.done(function(data) {
//                    Templates.replaceNodeContents('.'+self.nodeContent, data.html);
                    $('.ajaxForm').html(data.html);
                    $('head').append(data.javascript);
                });

                var dlg = $("."+ this.nodeContent).dialog({
                    resizable: true,
                    autoOpen: false,
                    width: "60%",
                    title: this.args.title,
                    modal: true,
                    close: function(event, ui) {
                        $(this).dialog('destroy').remove();
                    }
                });
                var self = this;
                $('.'+this.nodeContent+' .mform').bind('submit', function(e){
                    e.preventDefault();
                    self.submitFormAjax(this);
                })
            // document.getElementById(args.form).addEventListener('submit', function(ev) {
            //     this.submitFormAjax();
            // }

            //     $('.plotforms').on('submit', 'form', this.submitFormAjax(this, $('.plotforms .mform')));
                dlg.dialog("open");

    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.getBody = function(formdata) {
        if (typeof formdata === "undefined") {
            formdata = null;
        } else {
            // Get the content of the modal.
           this.args.jsonformdata = JSON.stringify(formdata);
        }

        var promise = Ajax.call({
            args : this.args,
            url: this.url
            }, false);

        return promise;


        //return Fragment.loadFragment('block_learnerscript', 'example', 1, this.args);
    };

    /**
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.handleFormSubmissionResponse = function(data) {
        if(data.formerror) {
            $('.ajaxForm').html(data.html);
            $('head').append(data.javascript);
                var self = this;
                $('.'+this.nodeContent+' .mform').bind('submit', function(e){
                    e.preventDefault();
                    self.submitFormAjax(this);
                })
        } else {
            alert("Success!");
        }
        // this.modal.hide();
        // // We could trigger an event instead.
        // // Yuk.
        // Y.use('moodle-core-formchangechecker', function() {
        //     M.core_formchangechecker.reset_form_dirty_state();
        // });
        // document.location.reload();
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.handleFormSubmissionFailure = function(data) {
        // if(data.formerror) {
        //     $('.ajaxForm').html(data.html);
        // } else {
        //     alert("Success!");
        // }
        // Oh noes! Epic fail :(
        // Ah wait - this is normal. We need to re-display the form with errors!
        //this.modal.setBody(this.getBody(data));
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    NewGroup.prototype.submitFormAjax = function(form) {

        // We don't want to do a real form submission.
        // Convert all the form elements values to a serialised string.
        this.args.jsonformdata = $(form).serialize();
        var self  = this;
            var promise = Ajax.call({
                args: this.args,
                url: this.url
            });
            promise.done(function(response) {
                self.handleFormSubmissionResponse(response);
            }).fail(function(ex) {
            });
    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    NewGroup.prototype.submitForm = function(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };

    return /** @alias module:core_group/newgroup */ {
        // Public variables and functions.
        /**
         * Attach event listeners to initialise this module.
         *
         * @method init
         * @param {string} selector The CSS selector used to find nodes that will trigger this module.
         * @param {int} contextid The contextid for the course.
         * @return {Promise}
         */
        init: function(args) {
            return new NewGroup(args, url);
        }
    };
});