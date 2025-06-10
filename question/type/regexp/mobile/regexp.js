var that = this;
var result = {

    componentInit: function() {

        if (!this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }
        var div = document.createElement('div');
        div.innerHTML = this.question.html;
         // Get question questiontext.
        var questiontext = div.querySelector('.qtext');
         // Get question input.
        var input = div.querySelector('input[type="text"][name*=answer]');

        if (div.querySelector('.readonly') !== null) {
            this.question.readonly = true;
        }
        if (div.querySelector('.feedback') !== null) {
            this.question.feedback = div.querySelector('.feedback');
            this.question.feedbackHTML = true;
        }

        this.question.text = questiontext.innerHTML;
        this.question.input = input;

        if (typeof this.question.text == 'undefined') {
            this.logger.warn('Aborting because of an error parsing question.', this.question.name);
            return this.CoreQuestionHelperProvider.showComponentError(this.onAbort);
        }

        // Check if question is marked as correct for displaying relevant icon & its colour.

        if (input.classList.contains('incorrect')) {
            this.question.input.correctIcon = 'fa-times';
            this.question.input.correctIconColor = 'danger';
        } else if (input.classList.contains('correct')) {
            this.question.input.correctIcon = 'fa-check';
            this.question.input.correctIconColor = 'success';
        } else if (input.classList.contains('partiallycorrect')) {
            this.question.input.correctIcon = 'fa-check-square';
            this.question.input.correctIconColor = 'warning';
        }

        // Check if the answer is inside the question text.
        if (input.classList.contains('inlineinput')) {
            this.question.input.isInline = true;
        } else {
            this.question.input.isInline = false;
        }

        // @codingStandardsIgnoreStart
        // Wait for the DOM to be rendered.
        setTimeout(() => {

        });
        // @codingStandardsIgnoreEnd
        return true;
    }
};
result;