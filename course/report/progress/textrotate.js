var SVGNS='http://www.w3.org/2000/svg',XLINKNS='http://www.w3.org/1999/xlink';

function textrotate_make_svg(el)
{
  var string=el.firstChild.nodeValue;

  // Create SVG
  var svg=document.createElementNS(SVGNS,'svg');
  svg.setAttribute('version','1.1');
  var width=(el.offsetHeight*9)/8;
  svg.setAttribute('width',width);
  svg.setAttribute('height',el.offsetWidth+20);

  var text=document.createElementNS(SVGNS,'text');
  svg.appendChild(text);
  text.setAttribute('x',el.offsetWidth);
  text.setAttribute('y',-el.offsetHeight/4);
  text.setAttribute('text-anchor','end');
  text.setAttribute('transform','rotate(90)');
  text.appendChild(document.createTextNode(string));

  // Is there an icon near the text?
  var icon=el.parentNode.firstChild;
  if(icon.nodeName.toLowerCase()=='img') {
    el.parentNode.removeChild(icon);
    var image=document.createElementNS(SVGNS,'image');
    var iconx=el.offsetHeight/4;
    if(iconx>width-16) iconx=width-16;
    image.setAttribute('x',iconx);
    image.setAttribute('y',el.offsetWidth+4);
    image.setAttribute('width',16);
    image.setAttribute('height',16);
    image.setAttributeNS(XLINKNS,'href',icon.src);
    svg.appendChild(image);
  }  

  // Replace original content with this new SVG
  el.parentNode.insertBefore(svg,el);
  el.parentNode.removeChild(el);
}

function textrotate_init() {
  var elements=YAHOO.util.Dom.getElementsByClassName('completion-activityname', 'span');
  for(var i=0;i<elements.length;i++)
  {
    var el=elements[i];
    el.parentNode.parentNode.parentNode.style.verticalAlign='bottom';
    textrotate_make_svg(el);
  }

  elements=YAHOO.util.Dom.getElementsByClassName('completion-expected', 'div');
  for(var i=0;i<elements.length;i++)
  {
    var el=elements[i];
    el.style.display='inline';
    //el.style.fontSize='0.6em';
    var parent=el.parentNode;
    parent.removeChild(el);
    parent.insertBefore(el,parent.firstChild);
    textrotate_make_svg(el.firstChild);
  }
}

YAHOO.util.Event.onDOMReady(textrotate_init); 

