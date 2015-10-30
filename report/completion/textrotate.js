var SVGNS='http://www.w3.org/2000/svg',XLINKNS='http://www.w3.org/1999/xlink';

function textrotate_make_svg(el)
{
  var string=el.firstChild.nodeValue;

  // Add absolute-positioned string (to measure length)
  var abs=document.createElement('div');
  abs.appendChild(document.createTextNode(string));
  abs.style.position='absolute';
  document.body.appendChild(abs);
  var textWidth=abs.offsetWidth * 1.2,textHeight=abs.offsetHeight;
  document.body.removeChild(abs);

  // Create SVG
  var svg=document.createElementNS(SVGNS,'svg');
  svg.setAttribute('version','1.1');
  svg.setAttribute('width',20);
  svg.setAttribute('height',textWidth);

  // Add text
  var text=document.createElementNS(SVGNS,'text');
  svg.appendChild(text);
  text.setAttribute('x',textWidth);
  text.setAttribute('y',-textHeight/4);
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
    image.setAttribute('y',textWidth+4);
    image.setAttribute('width',16);
    image.setAttribute('height',16);
    image.setAttributeNS(XLINKNS,'href',icon.src);
    svg.appendChild(image);
  }

  // Replace original content with this new SVG
  el.parentNode.insertBefore(svg,el);
  el.parentNode.removeChild(el);
}

function browser_supports_svg() {
    return document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1");
}

function textrotate_init() {
    if (!browser_supports_svg()) {
        // Feature detect, else bail.
        return;
    }

YUI().use('yui2-dom', function(Y) {
  var elements= Y.YUI2.util.Dom.getElementsByClassName('completion-criterianame', 'span');
  for(var i=0;i<elements.length;i++)
  {
    var el=elements[i];
    el.parentNode.style.verticalAlign='bottom';
    el.parentNode.style.width='20px';

    textrotate_make_svg(el);
  }
});
}
