// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AMD code for the frequently used comments chooser for the marking guide grading form.
 *
 * @module     gradingform_guide/comment_chooser
 * @class      comment_chooser
 * @package    core
 * @copyright  2015 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
define(['jquery', 'core/templates', 'core/notification', 'core/yui'], function($, templates, notification) {

    // Private variables and functions.

    return /** @alias module:gradingform_guide/comment_chooser */ {
        // Public variables and functions.
        /**
         * Initialises the module.
         *
         * Basically, it performs the binding and handling of the button click event for
         * the 'Insert frequently used comment' button.
         *
         * @param {Integer} criterionId The criterion ID.
         * @param {String} buttonId The element ID of the button which the handler will be bound to.
         * @param {String} remarkId The element ID of the remark text area where the text of the selected comment will be copied to.
         * @param {Array} commentOptions The array of frequently used comments to be used as options.
         */
        initialise: function(criterionId, buttonId, remarkId, commentOptions) {
            /**
             * Display the chooser dialog using the compiled HTML from the mustache template
             * and binds onclick events for the generated comment options.
             *
             * @param {String} compiledSource The compiled HTML from the mustache template
             * @param {Array} comments Array containing comments.
             */
            function displayChooserDialog(compiledSource, comments) {
                var titleLabel = '<label>' + M.util.get_string('insertcomment', 'gradingform_guide') + '</label>';
                var cancelButtonId = 'comment-chooser-' + criterionId + '-cancel';
                var cancelButton = '<button id="' + cancelButtonId + '">' + M.util.get_string('cancel', 'moodle') + '</button>';

                // Set dialog's body content.
                var chooserDialog = new M.core.dialogue({
                    modal: true,
                    headerContent: titleLabel,
                    bodyContent: compiledSource,
                    footerContent: cancelButton,
                    focusAfterHide: '#' + remarkId,
                    id: "comments-chooser-dialog-" + criterionId
                });

                // Bind click event to the cancel button.
                $("#" + cancelButtonId).click(function() {
                    chooserDialog.hide();
                });

                // Loop over each comment item and bind click events.
                $.each(comments, function(index, comment) {
                    var commentOptionId = '#comment-option-' + criterionId + '-' + comment.id;

                    // Delegate click event for the generated option link.
                    $(commentOptionId).click(function() {
                        var remarkTextArea = $('#' + remarkId);
                        var remarkText = remarkTextArea.val();

                        // Add line break if the current value of the remark text is not empty.
                        if ($.trim(remarkText) !== '') {
                            remarkText += '\n';
                        }
                        remarkText += comment.description;

                        remarkTextArea.val(remarkText);

                        chooserDialog.hide();
                    });

                    // Handle keypress on list items.
                    $(document).off('keypress', commentOptionId).on('keypress', commentOptionId, function() {
                        var keyCode = event.which || event.keyCode;

                        // Enter or space key.
                        if (keyCode == 13 || keyCode == 32) {
                            // Trigger click event.
                            $(commentOptionId).click();
                        }
                    });
                });

                // Destroy the dialog when it is hidden to allow the grading section to
                // be loaded as a fragment multiple times within the same page.
                chooserDialog.after('visibleChange', function(e) {
                    // Going from visible to hidden.
                    if (e.prevVal && !e.newVal) {
                        this.destroy();
                    }
                }, chooserDialog);

                // Show dialog.
                chooserDialog.show();
            }

            /**
             * Generates the comments chooser dialog from the grading_form/comment_chooser mustache template.
             */
            function generateCommentsChooser() {
                // Template context.
                var context = {
                    criterionId: criterionId,
                    comments: commentOptions
                };

                // Render the template and display the comment chooser dialog.
                templates.render('gradingform_guide/comment_chooser', context)
                    .done(function(compiledSource) {
                        displayChooserDialog(compiledSource, commentOptions);
                    })
                    .fail(notification.exception);
            }

            // Bind click event for the comments chooser button.
            $("#" + buttonId).click(function(e) {
                e.preventDefault();
                generateCommentsChooser();
            });
        }
    };
});
