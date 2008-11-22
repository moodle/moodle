/** JavaScript for /mod/quiz/edit.php
 */

YAHOO.namespace("cats.container");
YAHOO.namespace("quiz.container");
function init() {
    alert("loadde");
    YAHOO.util.Dom.setStyle('randomquestiondialog', 'display', 'block');
    /* zIndex must be way above 99 to be above the active quiz tab*/
    YAHOO.quiz.container.randomquestiondialog = new YAHOO.widget.Dialog("randomquestiondialog",
                {
                  constraintoviewport : true,
                  visible : false,
                  modal:true,
                  width : "100%",
                  iframe:true,
                  zIndex:1000,
                  fixedcenter : true,
                  close: true,
                  draggable: true,
                  dragOnly: true,
                  postmethod: "form"

                 } );
    //show the dialog and depending on from which form (corresponding
    // a specific quiz page) it was triggered, set the value of the form's
    // rqpage input element to the form number
    YAHOO.util.Event.addListener(this.dialog_listeners, "click",
           function(e){
                   this.show();
                var rbutton = YAHOO.util.Event.getTarget(e);
                var rbform = YAHOO.util.Dom.getAncestorByClassName(rbutton,"randomquestionform");
                //this depends on the fact that the element hierarchy be:
                // <form class="randomquestionform"><div>[input elements]</div></form>
                var rbformelements = YAHOO.util.Dom.getChildren
                (YAHOO.util.Dom.getFirstChild(rbform));
                var rqpage=YAHOO.util.Dom.get("rform_qpage");

                for (var i = 0; i < rbformelements.length; i++) {
                    if(YAHOO.util.Dom.hasClass(rbformelements[i],"addonpage_formelement")){
                          //why is this not rqpage.value.value, the first "value" being the element property
                          // and the second the value of that property? I don't understand.
                          rqpage.value=rbformelements[i].attributes.value.value;
                    }
                }
                YAHOO.util.Event.stopEvent(e);
            }, YAHOO.quiz.container.randomquestiondialog,
            YAHOO.quiz.container.randomquestiondialog, true);
    YAHOO.quiz.container.randomquestiondialog.render();


    YAHOO.quiz.container.repaginatedialog = new YAHOO.widget.Dialog("repaginatedialog",
                {
                  modal:true,
                  width : "100%",
                  iframe:true,
                  zIndex:1000,
                  fixedcenter : true,
                  visible : false,
                  close: true,
                  draggable: true,
                  dragOnly: true,
                  constraintoviewport : true,
                  postmethod: "form"
                 } );
    YAHOO.util.Event.addListener("repaginatecommand", "click",
            function(e){
                YAHOO.util.Dom.setStyle('repaginatedialog', 'display', 'block');
                this.show();
            }, YAHOO.quiz.container.repaginatedialog,
            YAHOO.quiz.container.repaginatedialog, true);
    YAHOO.quiz.container.repaginatedialog.render();

}

YAHOO.util.Event.onDOMReady(init,phpGenerated,true);
YAHOO.util.Dom.setStyle('repaginatedialog', 'display', 'block');