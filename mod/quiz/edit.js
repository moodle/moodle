/** JavaScript for /mod/quiz/edit.php
 */

YAHOO.namespace("cats.container");
YAHOO.namespace("quiz.container");
function init() {
    YAHOO.util.Dom.setStyle('randomquestiondialog', 'display', 'block');
    YAHOO.cats.container.module = new YAHOO.widget.Module("module");
    YAHOO.cats.container.show = new YAHOO.widget.Module("show");
    YAHOO.cats.container.hide = new YAHOO.widget.Module("hide");
    YAHOO.cats.container.module.render();
    YAHOO.util.Event.addListener("show", "click", YAHOO.cats.container.module.show,
            YAHOO.cats.container.module, true);
    YAHOO.util.Event.addListener("show", "click",
            function(e){
                YAHOO.cats.container.hide.show;
                // for some reason YUI sets element display "block" so we have to reverse that:
                hideel.setStyle("display", "inline");
                //TODO: get sURL from the phpdata object somehow
                var sUrl="";
                var transaction = YAHOO.util.Connect.asyncRequest("GET", sUrl, null, null);
                YAHOO.util.Event.stopEvent(e);
            }, YAHOO.cats.container.hide, true);
    YAHOO.util.Event.addListener("show", "click", YAHOO.cats.container.show.hide, YAHOO.cats.container.show, true);
    YAHOO.util.Event.addListener("hide", "click", YAHOO.cats.container.module.hide, YAHOO.cats.container.module, true);
    YAHOO.util.Event.addListener("hide", "click",
            function(e){
                YAHOO.cats.container.show.show;
                // for some reason YUI sets element display
                // to "block" so we have to reverse that:
                showel.setStyle("display", "inline");
                var sUrl="$surl";
                var transaction = YAHOO.util.Connect.asyncRequest("GET", sUrl, null, null);
                YAHOO.util.Event.stopEvent(e);
            }, YAHOO.cats.container.show, true);

    YAHOO.util.Event.addListener("hide", "click", YAHOO.cats.container.hide.hide, YAHOO.cats.container.hide, true);

    // Instantiate the Dialog
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

                /*
                //this should work, it doesn't, no time to debug.
                var rqpagehiddenel=YAHOO.util.Dom.getFirstChildBy(rbformelements,function(el) {
                    //alert("infunc");
                    var result=YAHOO.util.Dom.hasClass(el,"addonpage_formelement");
                    //if (result) alert ("yes"); else alert("no");
                    return result;
                });
                //no element in rqpagehiddenel
                //rqpage.value=rqpagehiddenel.value.value;
                */

                //this works instead
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
// Instantiate the Dialog
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


YAHOO.util.Event.addListener(window, "load", init,phpGenerated,true);
YAHOO.util.Dom.setStyle('repaginatedialog', 'display', 'block');

//TODO: take this inside the init function to make sure
//YAHOO.cats.container.module is defined when run
if (YAHOO.cats.container.module && quiz_qbanktool){
    YAHOO.cats.container.module.hide();
    YAHOO.cats.container.show.show();
    showel.setStyle("display", "inline");
    YAHOO.cats.container.hide.hide();
}
