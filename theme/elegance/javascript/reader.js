/*! Reader.js
 * copyright  2014 Bas Brands, www.basbrands.nl
 * authors    Bas Brands, David Scotson
 * license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  */
YUI.add('moodle-theme_elegance-reader', function(Y) {

  var reader = 'reader panel';

  var reader = function() {
    reader.superclass.constructor.apply(this, arguments);
  };

  // Make the colour switcher a fully fledged YUI module
  Y.extend(reader, Y.Base, {

    initializer : function(config) {

      var onClick = function(e) {
        var target = e.currentTarget.getAttribute('dataid');
        targetnode = Y.one(target);

        var openBtn = e.currentTarget, panel, bb;

        function showPanel() {
          panel.show();
        }

        function hidePanel() {
          panel.hide();
        }

        obj = Y.Node.create('<div id="#panelcontent" class="panel panel-default readerpanel"><div>');
        contentcontainer = Y.Node.create('<div id="#contentcontainer" class="readercontent"><div>');

        Y.one('body').insert(obj,'#page');
        obj.prepend(contentcontainer,obj);

        var pagewidth = Y.one('body').get('docWidth');
        if ( pagewidth < 480 ) {
          var marginleft = 0;
          var width = pagewidth;
        } else {
          var marginleft = 15;
          var width = pagewidth - 30;
        }

        panel = new Y.Panel({
          srcNode: obj,
          width  : width,
          zIndex : 6,
          modal  : true,
          contstrain: 'body',
          x: marginleft,
          y: 10,
          visible: false,
          render : true,
        });

        bb = panel.get('boundingBox');

        var readercontent = targetnode.getHTML()
        contentcontainer.setHTML(readercontent);

        showPanel();
        panel.after('visibleChange', function (e) {
          if (!e.newVal) { // panel is hidden
              Y.later(0, this, this.destroy);
            }
        });
      };

      Y.one('body').delegate('click', onClick, '.moodlereader');
    },

  }, {
    NAME : 'bootstrap yui modal',
    ATTRS : {
    }
  });
  // Our leaf theme namespace
  M.theme_elegance = M.theme_elegance || {};
  // Initialisation function for the colour switcher
  M.theme_elegance.initreader = function(cfg) {
    return new reader(cfg);
  }

}, '@VERSION@', {requires:['panel','node','node-load','attribute', 'event']});
