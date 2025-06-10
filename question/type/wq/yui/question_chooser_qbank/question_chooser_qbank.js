YUI.add('moodle-qtype_wq-question_chooser_qbank', function (Y, NAME) {

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
        this.wirisSection();
      },
      /**
       * Moves all Wiris Quizzes questions under node_before and populates the array
       * this.wirisquestions.
       * @param {node} nodeBefore - Previous node.
       */
      moveWirisQuestions: function(nodeBefore) {
        var wirisdivs = [];
        Y.all('div.option').each(function(node) {
          var input = node.one('input');
          if (
            input &&
            input.getAttribute('value') &&
            input.getAttribute('value').indexOf('wiris') !== -1
          ) {
            nodeBefore.insert(node, 'after');
            nodeBefore = node;
            wirisdivs.push(node);
          }
        });
        this.wirisquestions = wirisdivs;
      },
      /**
       * Unused function. Join all Wiris Quizzes questions in a section after
       * QUESTIONS and before OTHER.
       * */
      wirisSection: function() {
        var label = Y.one('label[for=qtype_qtype_wq]');
        label = label ? label : Y.one('label[for=item_qtype_wq]');
        if (label) {
          // Convert qtype option into section title and move to the bottom.
          var wq = label.ancestor('div');
          var name = wq.one('span.typename').remove(false);
          wq.one('label').remove(true);
          wq.append(name).addClass('moduletypetitle');
          var container = wq.ancestor();
          wq.remove();
          container.insertBefore(wq, container.one('div.separator'));
          container.insertBefore(Y.Node.create('<div class="separator"/>'), wq);
          // Move all Wiris qtypes under title.
          this.moveWirisQuestions(wq);
        }
      }
    };
    
    
    }, '@VERSION@', {"requires": ["moodle-qbank_editquestion-chooser"]});