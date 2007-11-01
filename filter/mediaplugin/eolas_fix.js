// Documentation & updates available at:
// http://codecentre.eplica.is/js/eolasfix/test.htm

(function( Eolas_Fixed,
            win, doc,
            getElementsByTagName,
            outerHTML,
            parentNode,
            tags,
            elmsToRemoveOnload,
            x,
            is_ie,
            y,z,elm,childNode,HTML,dummy,eolasfix)
{
  // run only once!
  if (win[Eolas_Fixed]) return;
  win[Eolas_Fixed] = 1;

  eolasfix = function ()
  {
    // for each tag name specified in Array t
    while (tags[++x])
    {
      // find all elements of that type in the document
      // loop through the elements
      y = 0;
      while (elm = doc[getElementsByTagName](tags[x])[y++])
      {
        if (is_ie)
        {
          HTML = '>';
          z = 0;
          // <param> elements don't show up in innerHTML IE
          // so we need to collect their outerHTML.
          while (childNode = elm.childNodes[z++])
              HTML += childNode[outerHTML];

          // create a 'dummy' element 
          dummy = doc.createElement('i');
          // inject it next to `elm`,
          elm[parentNode].insertBefore(dummy, elm);
          // and turn it into an `elm` clone
          dummy[outerHTML] = elm[outerHTML].replace(/>/, HTML);
          // increment y to skip over it
          y++;

          // then hide the original elm
          elm.style.display = 'none';
          // and save it in 'The List of Elements to Remove Later'.
          elmsToRemoveOnload[elmsToRemoveOnload.length] = elm;
        }
        else
        {
          elm[outerHTML] = elm[outerHTML];
        }
      }
    }
  };

  // For IE run the fix straight away (because the defer="defer"
  // attribute has delayed execution until the DOM has loaded).
  // Then assign a window.onload event to purge the old elements.
  is_ie && !eolasfix() && win.attachEvent('onload', function(){
    x=0;
    while(elm = elmsToRemoveOnload[x++])
        elm[parentNode].removeChild(elm);
  });
  // For Opera set an `DOMContentLoaded` event to run the fix.
  win.opera && doc.addEventListener('DOMContentLoaded', eolasfix, 0);

})( '__Eolas_Fixed',
    window, document,
    'getElementsByTagName',
    'outerHTML',
    'parentNode',
    ['object','embed','applet'],
    [],
    -1 /*@cc_on,1 @*/
  );

