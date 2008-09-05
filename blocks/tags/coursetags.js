/**
 * coursetags.js
 * @author j.beedell@open.ac.uk July07
 *
 * getKeywords modified from an original script (Auto Complete Textfield)
 * from The JavaScript Source http://javascript.internet.com
 * originally created by: Timothy Groves http://www.brandspankingnew.net/
 */


function ctags_show_div(mydiv) {
    for(x in coursetagdivs) {
        if(mydiv == coursetagdivs[x]) {
            document.getElementById(coursetagdivs[x]).style.display="block";
        } else {
            document.getElementById(coursetagdivs[x]).style.display="none";
        }
    }
    return false;
}

var sug = "";
var sug_disp = "";

function ctags_getKeywords() {
  /*
  // This 'workaround' removing the xhtml strict form autocomplete="off" needs to
  // be added to the body onload() script to work - but decided not to include
  // (having the browser list might help with screen readers more than this script)
  // document.forms['coursetag'].setAttribute("autocomplete", "off");
  */
  var input = document.forms['coursetag'].coursetag_new_tag.value;
  var len = input.length;
  sug_disp = ""; sug = "";

  if (input.length) {
    for (ele in coursetag_tags)
    {
      if (coursetag_tags[ele].substr(0,len).toLowerCase() == input.toLowerCase())
      {
        sug_disp = input + coursetag_tags[ele].substr(len);
        sug = coursetag_tags[ele];
        break;
      }
    }
  }
  document.forms['coursetag'].coursetag_sug_keyword.value = sug_disp;
  if (!sug.length || input == sug_disp) {
    document.getElementById('coursetag_sug_btn').style.display = "none";
  } else {
    document.getElementById('coursetag_sug_btn').style.display = "block";
  }
}

function ctags_setKeywords() {
  document.forms['coursetag'].coursetag_new_tag.value = sug;
  ctags_hideSug();
}

function ctags_hideSug() {
  document.forms['coursetag'].coursetag_sug_keyword.value = "";
  document.getElementById('coursetag_sug_btn').style.display = "none";
}
