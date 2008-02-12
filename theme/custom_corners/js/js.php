<script src="<?php echo $CFG->httpsthemewww ?>/custom_corners/js/CSSClass_min.js" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
    var script = {
        
        corrections: function () {
            if (top.user) {
                top.document.getElementsByTagName('frameset')[0].rows = "117,30%,0,200";
            }
            
            // check for layouttabel and add haslayouttable class to body
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
            
            function setbodytag (tagname) {
                var bd = document.getElementsByTagName('body')[0];
                if (bd) {
                    CSSClass.add(bd, tagname);
                } else {
                    setTimeout(function() { setbodytag(tagname) }, 30);
                }
            }
            
            setTimeout(function() { setbodytag(tagname) }, 30);
        },
        
        init: function() {
            script.corrections();
        }
    };
/* ]]> */
</script>