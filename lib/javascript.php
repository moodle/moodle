
<SCRIPT LANGUAGE="JavaScript">
<!-- //hide
function fillmessagebox(text) {
  document.form.message.value = text;
}

function openpopup(url,name,height,width) {
fullurl = "<?=$CFG->wwwroot ?>" + url;
options = "menubar=0,location=0,scrollbars,resizable,width="+width+",height="+height;
windowobj = window.open(fullurl,"name", options);
windowobj.focus();
}

<? if ($focus) { echo "function setfocus() { document.$focus.focus() }\n"; } ?>

// done hiding -->
</SCRIPT>

