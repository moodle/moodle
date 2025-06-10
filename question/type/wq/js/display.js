var wirisInputs = new Array();
var moodleInputs = new Array();
var wirisDivs = new Array();
var moodleTypes = new Array();
var wirisTypes = new Array();

function get_firstchild(n) {
    x = n.firstChild;
    while (x.nodeType != 1) {
        x = x.nextSibling;
    }
    return x;
}

function get_nextsibling(n) {
    x = n.nextSibling;
    while (x.nodeType != 1) {
        x = x.nextSibling;
    }
    return x;
}


function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function toggleList(obj) {
    obj.checked = false;
    if (obj.className == 'wirisClose') {
        var span;
        for (i = 0; i < wirisDivs.length; i++) {
                wirisDivs[i].style.display = 'block';
                span = get_nextsibling(wirisInputs[i]);
                span.style.position = 'relative';
                span.style.left = '10px';
        }
        obj.className = 'wirisOpen';
    } else {
        for (i = 0; i < wirisDivs.length; i++) {
                wirisDivs[i].style.display = 'none';
        }
        obj.className = 'wirisClose';
    }
}

function hideList() {
    for (i = 0; i < wirisDivs.length; i++) {
        wirisDivs[i].style.display = 'none';
    }
}

var genericInput = document.getElementById('qtype_wq');
genericInput.setAttribute('onClick', 'toggleList(this);');
genericInput.className = 'wirisClose';
genericInput.onclick = function() {toggleList(this);}; // for IE
var genericDiv = genericInput.parentNode.parentNode;
var realDiv = genericDiv.parentNode;

var cancelInput = document.getElementById('chooseqtypecancel');
cancelInput.setAttribute('onClick', 'hideList();');
cancelInput.onclick = function() {hideList();}; // for IE

var w = 0;
var m = 0;

for (i = 0; i < realDiv.children.length; i++) {
    var types = new Array();
    types[i] = realDiv.children[i];
    types[i] = get_firstchild(types[i]);
    types[i] = types[i].htmlFor;
    if (types[i].indexOf('wiris') != -1) {
        wirisTypes[w] = types[i];
        w++;
    }else if (types[i] != 'qtype_wq') {
        moodleTypes[m] = types[i];
        m++;
    }
}

wirisTypes.sort();
wirisTypes.reverse();

for (i = 0; i < wirisTypes.length; i++) {
    wirisInputs[i] = document.getElementById(wirisTypes[i]);
    wirisDivs[i] = wirisInputs[i].parentNode.parentNode;
    wirisDivs[i].style.display = 'none';
    insertAfter(genericDiv, wirisDivs[i]);
}
