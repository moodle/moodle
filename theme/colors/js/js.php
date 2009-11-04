<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
    var CSSClass={};CSSClass.is=function(e,c){if(typeof e=="string")e=document.getElementById(e);var classes=e.className;if(!classes)return false;if(classes==c)return true;return e.className.search("\\b"+c+"\\b")!=-1;};CSSClass.add=function(e,c){if(typeof e=="string")e=document.getElementById(e);if(CSSClass.is(e,c))return;if(e.className)c=" "+c;e.className+=c;};CSSClass.remove=function(e,c){if(typeof e=="string")e=document.getElementById(e);e.className=e.className.replace(new RegExp("\\b"+c+"\\b\\s*","g"),"");};

    var jsscript = {

        corrections: function () {
            if (top.user) {
                top.document.getElementsByTagName('frameset')[0].rows = "117,30%,0,200";
            }

            // check for layouttabel and add haslayouttable class to body
            function setbodytag () {
                var bd = document.getElementsByTagName('body')[0];
                if (bd) {
                    var tagname = 'nolayouttable';
                    if (document.getElementById('middle-column')) {
                        var lc = document.getElementById('left-column');
                        var rc = document.getElementById('right-column');
                        if ( lc && rc ) {
                            tagname = 'haslayouttable rightandleftcolumn';
                        } else if (lc) {
                            tagname = 'haslayouttable onlyleftcolumn';
                        } else if (rc) {
                            tagname = 'haslayouttable onlyrightcolumn';
                        } else {
                            tagname = 'haslayouttable onlymiddlecolumn';
                        }
                    }
                    CSSClass.add(bd, tagname);
                } else {
                    setTimeout(function() { setbodytag() }, 10);
                }
            };

            setbodytag();
        },

        init: function() {
            jsscript.corrections();
        }
    };

    jsscript.init();
/* ]]> */
</script>