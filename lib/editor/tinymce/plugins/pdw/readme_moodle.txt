Description of PDW Toolbar Toggle integration in Moodle
=========================================================================================

Copyright: Guido Neele (www.neele.name)
License: MIT

Moodle maintainer: Jason Fowler (phalacee)

=========================================================================================
Upgrade procedure:
1/ extract standard PDW package into lib/editor/tinymce/plugins/pdw/tinymce/
2/ bump up version.php
3/ update ./thirdpartylibs.xml
4/ reimplement patch in MDL-23646
5/ reimplement patch in MDL-40668
6/ add in "DOM.setStyle(ifr, 'width',DOM.getSize(ifrcon).w); // Resize iframe" (without quotes)
   after "DOM.setStyle(ifr, 'height',DOM.getSize(ifr).h + dy); // Resize iframe"
7/ reimplement patch in MDL-42481
8/ reimplement patch in MDL-42684
9/ reimplement patch in MDL-42887
