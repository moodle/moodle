
<SCRIPT LANGUAGE="JavaScript">
<!-- //hide

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

function openpopup(url,name,height,width) {
  fullurl = "<?=$CFG->wwwroot ?>" + url;
  options = "menubar=0,location=0,scrollbars,resizable,width="+width+",height="+height;
  windowobj = window.open(fullurl,name, options);
  windowobj.focus();
}

function copyrichtext(textname) { 
  textname.value = document.richedit.docHtml;
  return true;
}

function checkall() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++)
    void(el[i].checked=1) 
}

function inserttext(text) {
<?PHP 
    if (!empty($SESSION->inserttextform)) {
        $insertfield = "opener.document.forms['$SESSION->inserttextform'].$SESSION->inserttextfield";
    } else {
        $insertfield = "opener.document.forms['theform'].message";
    }
    echo "  text = ' ' + text + ' ';\n";
    echo "  if ( $insertfield.createTextRange && $insertfield.caretPos) {\n";
    echo "      var caretPos = $insertfield.caretPos;\n";
    echo "      caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;\n";
    echo "  } else {\n";
    echo "      $insertfield.value  += text;\n";
    echo "  }\n";
    echo "  $insertfield.focus();\n";
?>
}

<?PHP if ($focus) { echo "function setfocus() { document.$focus.focus() }\n"; } ?>

// done hiding -->
</SCRIPT>

