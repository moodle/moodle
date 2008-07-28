var completion_strsaved;

function completion_init() { 
  var toggles=YAHOO.util.Dom.getElementsByClassName('togglecompletion', 'form');
  for(var i=0;i<toggles.length;i++) {
    completion_init_toggle(toggles[i]);
  }
} 

function completion_init_toggle(form) {
  // Store all necessary references for easy access
  var inputs=form.getElementsByTagName('input');
  form.cmid=inputs[0].value;
  form.otherState=inputs[1].value;
  form.image=inputs[2];

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
    this.otherState=0;
  } else {
    this.image.src=this.image.src.replace(/y\.gif$/,'n.gif');
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
  YAHOO.util.Connect.asyncRequest('POST','togglecompletion.php',
      {success:completion_handle_response,failure:completion_handle_failure,scope:this},
      'id='+this.cmid+'&completionstate='+this.otherState+'&fromajax=1');
}

YAHOO.util.Event.onDOMReady(completion_init);
