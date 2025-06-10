/**
 * JavaScript library to rearrange the question type chooser dialog when adding
 * a new question from the question bank.
 *
 * This file applies to moodle version 2.7 and later, for previous version see
 * display.js file.
 * **/
YUI.add('moodle-qtype_wq-question_chooser', function(Y) {
    // Namespace for Wiris Quizzes.
    M.qtype_wq = M.qtype_wq || {};
    // Question chooser class.
    M.qtype_wq.question_chooser = {
        /**
         * Array with all the real Wiris Quizzes questions.
         * */
        wirisquestions: null,
        /**
         * Start point.
         * */
        init: function() {
            this.wiris_section();
        },
        /**
         * Moves all Wiris Quizzes questions under node_before and populates the array
         * this.wirisquestions.
         */
        move_wiris_questions: function(node_before) {
            var wirisdivs = [];
            Y.all('div.option').each(function(node) {
                var input = node.one('input');
                if(input && input.getAttribute('value')
                && (input.getAttribute('value').indexOf('wiris') !== -1)){
                    node_before.insert(node, 'after');
                    node_before = node;
                    wirisdivs.push(node);
                }
            });
            this.wirisquestions = wirisdivs;
        },
        /**
         * Unused function. Join all Wiris Quizzes questions in a section after
         * QUESTIONS and before OTHER.
         * */
        wiris_section: function() {
            var label = Y.one('label[for=qtype_qtype_wq]');
            var label = label ? label : Y.one('label[for=item_qtype_wq]');
            if(label){
                // Convert qtype option into section title and move to the bottom.
                var wq = label.ancestor('div');
                var name = wq.one('span.typename').remove(false);
                var summary = wq.one('span.typesummary').remove(false);
                wq.one('label').remove(true);
                wq.append(name).append(summary).addClass('moduletypetitle');
                var container = wq.ancestor();
                wq.remove();
                container.insertBefore(wq, container.one('div.separator'));
                container.insertBefore(Y.Node.create('<div class="separator"/>'),wq);
                // Move all Wiris qtypes under title.
                this.move_wiris_questions(wq);
            }
        },
    }
}, '@VERSION@', {"requires": ["moodle-question-chooser"]});
