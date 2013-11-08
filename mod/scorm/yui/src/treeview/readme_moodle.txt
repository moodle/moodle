Description of gallery-sm-treeview integration in Moodle
=========================================================================================

Copyright: SmugMug Inc.
License: BSD
Repository: https://github.com/smugmug/yui-gallery.git

Moodle maintainer: Andrew Nicols (dobedobedoh)

=========================================================================================
Upgrade procedure:
* git clone https://github.com/smugmug/yui-gallery.git
* from that repository copy:
** build/gallery-sm-treeview/gallery-sm-treeview-debug.js to js/gallery-sm-treeview-debug.js
** build/gallery-sm-treeview-sortable/gallery-sm-treeview-sortable-debug.js to js/gallery-sm-treeview-sortable-debug.js
** build/gallery-sm-treeview/assets/gallery-sm-treeview-core.css to assets/moodle-mod_scorm-treeview-core.css
** build/gallery-sm-treeview/assets/skins/sam/*.png to assets/skins/sam
** build/gallery-sm-treeview/assets/skins/sam/gallery-sm-treeview.css to assets/skins/sam/moodle-mod_scorm-treeview.css
** build/gallery-sm-treeview-skin/assets/skins/sam/gallery-sm-treeview-skin.css to assets/skins/sam/moodle-mod_scorm-treeview-skin.css
* run shifter on this directory as required
* update ../../../thirdpartylibs.xml
