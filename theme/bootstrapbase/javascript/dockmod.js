function customise_dock_for_theme() {
  var dock = M.core_dock;
  dock.on('dock:itemschanged', theme_dockmod_handle_spans);
  dock.on('dock:panelgenerated', theme_dockmod_blockstyle);
}

//Add the "block" class to docked blocks. This prevents having to restyle
//all docked blocks and simply use standard block styling
function theme_dockmod_blockstyle() {
  Y.all('.dockeditempanel_content').each(function(dockblock){
    dockblock.addClass('block');
  });
}

//On docking and undocking the bootstrap spans have to change to
//dock properly
function theme_dockmod_handle_spans() {

  var prepopulatedblockregions = 0;
  var postpopulatedblockregions = 0;

  var blockspre = 0;
  var blockspost = 0;

  var maincontent = Y.one('#region-main');
  var regionpre = Y.one('#block-region-side-pre');
  var regionpost = Y.one('#block-region-side-post');
  var ltr = Y.one('body.dir-ltr');
  var mainwrapper = Y.one('#region-bs-main-and-pre');
  if (!ltr) {
    var mainwrapper = Y.one('#region-bs-main-and-post');
  }

  var body = Y.one('body');

  if (Y.all('.block.dock_on_load').size()>0) {
    // Do not resize during initial load
    return;
  }

  if (body.hasClass('blocks-moving')) {
    // open up blocks during blocks positioning
    return;
  }


  if (body.hasClass('two-column')) {

    var prehasblocks = (regionpre.all('.block').size() > 0);
    if (prehasblocks) {
      regionpre.addClass('span3');
      maincontent.removeClass('span12');
      maincontent.addClass('span9');
    } else {
      regionpre.removeClass('span3');
      maincontent.removeClass('span9');
      maincontent.addClass('span12');
    }
  } else {

    var prehasblocks = (regionpre.all('.block').size() > 0);
    if (prehasblocks) {
      regionpre.addClass('span4');
      maincontent.removeClass('span12');
      maincontent.addClass('span8');
    } else {
      regionpre.removeClass('span4');
      maincontent.removeClass('span8');
      maincontent.addClass('span12');
    }

    var posthasblocks = (regionpost.all('.block').size() > 0);
    if (posthasblocks) {
      regionpost.addClass('span3');
      mainwrapper.removeClass('span12');
      mainwrapper.addClass('span9');
    } else {
      mainwrapper.removeClass('span9');
      mainwrapper.addClass('span12');
      regionpost.removeClass('span3');
    }
  }
  return;

}
