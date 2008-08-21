var completion_strsaved, completion_strtitley, completion_strtitlen, 
  completion_stralty, completion_straltn;
var completion_wwwroot;

function completion_init() { 
  var toggles=YAHOO.util.Dom.getElementsByClassName('togglecompletion', 'form');
  for(var i=0;i<toggles.length;i++) {
    completion_init_toggle(toggles[i]);
  }
} 

function completion_init_toggle(form) {
  // Store all necessary references for easy access
  var inputs=form.getElementsByTagName('input');
  for(var i=0;i<inputs.length;i++) {
    switch(inputs[i].name) {
      case 'id' : form.cmid=inputs[i].value; break;
      case 'completionstate' : form.otherState=inputs[i].value; break;
    } 
    if(inputs[i].type=='image') {
      form.image=inputs[i];
    }
  }

  // Create and position 'Saved' text
  var saved=document.createElement('div');
  YAHOO.util.Dom.addClass(saved,'completion-saved-display');
  YAHOO.util.Dom.setStyle(saved,'display','none');
  saved.appendChild(document.createTextNode(completion_strsaved));
  form.appendChild(saved);
  form.saved=saved;

  // Add event handler
  YAHOO.util.Event.addListener(form, "submit", completion_toggle);
}

function completion_handle_response(o) {
  if(o.responseText!='OK') {
    alert('An error occurred when attempting to save your tick mark.\n\n('+o.responseText+'.)');
    return;
  }
  // Change image
  if(this.otherState==1) {
    this.image.src=this.image.src.replace(/n\.gif$/,'y.gif');
    this.image.title=completion_strtitley;
    this.image.alt=completion_stralty;
    this.otherState=0;
  } else {
    this.image.src=this.image.src.replace(/y\.gif$/,'n.gif');
    this.image.title=completion_strtitlen;
    this.image.alt=completion_straltn;
    this.otherState=1;
  }
  // Start animation
  completion_update_animation(this,1.0);
}

function completion_update_animation(form,opacity) {
  if(opacity<0.001) {
    YAHOO.util.Dom.setStyle(form.saved,'display','none');
    return;
  }
  YAHOO.util.Dom.setStyle(form.saved,'opacity',opacity);
  if(opacity>0.999) {
    var pos=YAHOO.util.Dom.getXY(form.image);
    pos[0]+=20; // Icon size + 4px border
    YAHOO.util.Dom.setStyle(form.saved,'display','block');
    YAHOO.util.Dom.setXY(form.saved,pos);
  }
  setTimeout(function() { completion_update_animation(form,opacity-0.1); },100);
}

function completion_handle_failure(o) {
  alert('An error occurred when attempting to connect to our server. The tick mark will not be saved.\n\n('+
    o.status+' '+o.statusText+')');
}

function completion_toggle(e) {
  YAHOO.util.Event.preventDefault(e);
  // By setting completion_wwwroot you can cause it to use absolute path 
  // otherwise script assumes it is called from somewhere in /course
  var target=completion_wwwroot 
    ? completion_wwwroot+'/course/togglecompletion.php' 
    : 'togglecompletion.php';
  YAHOO.util.Connect.asyncRequest('POST',target,
      {success:completion_handle_response,failure:completion_handle_failure,scope:this},
      'id='+this.cmid+'&completionstate='+this.otherState+'&fromajax=1');
}

YAHOO.util.Event.onDOMReady(completion_init);
