<script type="text/javascript">
//<![CDATA[
     function TestObj(id) {
         YAHOO.util.Event.onAvailable(id, colourLabel, this);
     }

    function colourLabel() {
        var i, j;
        var forms = YAHOO.util.Dom.getElementsByClassName ('mform', 'form');
        for (i = 0; i < forms.length; i++) {
            var formsets = forms[i].getElementsByTagName ('fieldset');
            for (j = 0; j < formsets.length; j++) {
                var col = colbuilder();
                // console.log("col " + col);
                YAHOO.util.Dom.setStyle(formsets[j], 'border-top-color', col);
                YAHOO.util.Dom.setStyle(formsets[j], 'border-bottom-color', col);
            }
        }

        // var check = document.getElementById('admin-settings');
        if (document.getElementById('admin-settings') || document.getElementById('admin-search')) {
            var check = 1;
        }
        // console.log("page: " + check);
        if (!check) return;

        var mc = document.getElementById("middle-column");
        var labels = YAHOO.util.Dom.getElementsByClassName ('form-label', 'div', mc);
        var formitems = YAHOO.util.Dom.getElementsByClassName ('form-item', 'div', mc);
        for (i = 0; i < formitems.length; i++) {
            var col = colbuilder();
            // console.log("col " + col);
            YAHOO.util.Dom.setStyle(formitems[i], 'border-top-color', col);
            YAHOO.util.Dom.setStyle(labels[i], 'border-bottom-color', col);
        }
    }

    function randomno()  {
        var ranno = -1;
        while (ranno < 0 || ranno > 255) {
            ranno = (Math.round(Math.random() * 1000));
        }
        return ranno;
    }

    function colbuilder() {
        var r = randomno().toString(16);
        if (r.length < 2) {
            r = ("0" + r);
        }
        var b = randomno().toString(16);
        if (b.length < 2) {
            b = ("0" + b);
        }
        var g = randomno().toString(16);
        if (g.length < 2) {
            g = ("0" + g);
        }
        return ("#" + r + b + g);
    }

     var obj = new TestObj("footer");
//]]>
</script>
