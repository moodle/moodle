/** JavaScript for /mod/quiz/edit.php
 */
var quiz_edit = {};
function quiz_edit_init() {
    YAHOO.util.Dom.setStyle('randomquestiondialog', 'display', 'block');
    /* zIndex must be way above 99 to be above the active quiz tab*/
    quiz_edit.randomquestiondialog = new YAHOO.widget.Dialog("randomquestiondialog",
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
    YAHOO.util.Event.addListener(this.dialoglisteners, "click",
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
            }, quiz_edit.randomquestiondialog,
            quiz_edit.randomquestiondialog, true);

    quiz_edit.randomquestiondialog.cfg.setProperty("keylisteners", [
     new YAHOO.util.KeyListener(document,
                                {keys:[27]},
                                function(types, args, obj) { quiz_edit.randomquestiondialog.hide();
    })
    ]); 

    quiz_edit.randomquestiondialog.render();


    quiz_edit.repaginatedialog = new YAHOO.widget.Dialog("repaginatedialog",
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
            }, quiz_edit.repaginatedialog,
            quiz_edit.repaginatedialog, true);

    quiz_edit.repaginatedialog.cfg.setProperty("keylisteners", [
     new YAHOO.util.KeyListener(document,
                                {keys:[27]},
                                function(types, args, obj) { quiz_edit.repaginatedialog.hide();
    })
    ]); 

    quiz_edit.repaginatedialog.render();

}

YAHOO.util.Event.onDOMReady(quiz_edit_init, quiz_edit_config, true);
YAHOO.util.Dom.setStyle('repaginatedialog', 'display', 'block');