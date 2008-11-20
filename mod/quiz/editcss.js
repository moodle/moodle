/** JavaScript for /mod/quiz/edit.php to be loaded in the header
 *  Adds a CSS class to display edit.php for users with JavaScript.
 */
        YAHOO.util.Event.onDOMReady(
            function(){
                YAHOO.util.Dom.addClass('quizcontentsblock', 'usejs');
           });