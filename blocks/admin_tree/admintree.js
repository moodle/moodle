admin_tree = {
    numdivs: null,
    hiddennodes: null,
    closedimg: null,
    openimg: null,
    closedalt: null,
    openalt: null,

    init: function(numdivs, expandnodes, openimg, closedimg, openalt, closedalt) {
        // Store the information we were passed in.
        admin_tree.openimg = openimg;
        admin_tree.closedimg = closedimg;
        admin_tree.openalt = openalt;
        admin_tree.closedalt = closedalt;
        admin_tree.numdivs = numdivs;

        // Initialise the hiddennodes array.
        admin_tree.hiddennodes = new Array();
        for (var i = 1; i <= admin_tree.numdivs; i++) {
            admin_tree.hiddennodes[i] = null;
        }

        // Collapse everything while adding the event handlers.
        for (var i = admin_tree.numdivs; i > 0; i--) {
            admin_tree.collapse(i);
            var togglelink = document.getElementById("vh_div" + i + "indicator").parentNode;
            togglelink.href = '#';
            YAHOO.util.Event.addListener(togglelink, 'click', admin_tree.toggle, i);
        }

        // Re-expand the bits we want expanded.
        for (var i = 0; i < expandnodes.length; i++) {
            admin_tree.expand(expandnodes[i]);
        }
    },

    toggle: function(e, i) {
        if (admin_tree.hiddennodes[i] === null) {
            admin_tree.collapse(i);
        } else {
            admin_tree.expand(i);
        }
        YAHOO.util.Event.preventDefault(e);
    },

    collapse: function(i) {
        if (admin_tree.hiddennodes[i] !== null) {
            return;
        }
        var obj = document.getElementById("vh_div" + i);
        if (obj === null) {
            return;
        }
        var nothing = document.createElement("span");
        nothing.setAttribute("id", "vh_div" + i);
        admin_tree.hiddennodes[i] = obj;
        obj.parentNode.replaceChild(nothing, obj);
        var icon = document.getElementById("vh_div" + i + "indicator");
        icon.src = admin_tree.closedimg;
        icon.alt = admin_tree.closedalt;
    },

    expand: function(i) {
        if (admin_tree.hiddennodes[i] === null) {
            return;
        }
        var nothing = document.getElementById("vh_div" + i);
        var obj = admin_tree.hiddennodes[i];
        admin_tree.hiddennodes[i] = null;
        nothing.parentNode.replaceChild(obj, nothing);
        var icon = document.getElementById("vh_div" + i + "indicator");
        icon.src = admin_tree.openimg;
        icon.alt = admin_tree.openalt;
    }
};
