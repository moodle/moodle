function popUpProperties(inobj) {
  op = window.open();
  op.document.open('text/plain');
  for (objprop in inobj) {
    op.document.write(objprop + ' => ' + inobj[objprop] + '\n');
  }
  op.document.close();
}

function fillmessagebox(text) {
  document.form.message.value = text;
}

function copyrichtext(textname) {
/// Legacy stub for old editor - to be removed soon
  return true;
}

function checkall() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++)
    void(el[i].checked=1)
}

function lockoptions(form, master, subitems) {
  // subitems is an array of names of sub items
  // requires that each item in subitems has a
  // companion hidden item in the form with the
  // same name but prefixed by "h"
  if (eval("document."+form+"."+master+".checked")) {
    for (i=0; i<subitems.length; i++) {
      unlockoption(form, subitems[i]);
    }
  } else {
    for (i=0; i<subitems.length; i++) {
      lockoption(form, subitems[i]);
    }
  }
  return(true);
}

function lockoption(form,item) {
  eval("document."+form+"."+item+".disabled=true");/* IE thing */
  eval("document."+form+".h"+item+".value=1");
}

function unlockoption(form,item) {
  eval("document."+form+"."+item+".disabled=false");/* IE thing */
  eval("document."+form+".h"+item+".value=0");
}
