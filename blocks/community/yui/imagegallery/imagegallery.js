YUI.add('moodle-block_community-imagegallery', function(Y) {

    var IMAGEGALLERYNAME = 'blocks_community_imagegallery';

    var IMAGEGALLERY = function() {
        IMAGEGALLERY.superclass.constructor.apply(this, arguments);
    };

    Y.extend(IMAGEGALLERY, Y.Base, {

        event:null,
        previousevent:null,
        nextevent:null,
        panelevent:null,
        panel:null, //all the images boxes
        imageidnumbers: [],
        imageloadingevent: null,
        loadingimage: null,

        initializer : function(params) {

            //create the loading image
            var objBody = Y.one(document.body);
            this.loadingimage = Y.Node.create('<div id="hubloadingimage" class="hiddenoverlay">'
                +'<img src=\'' + M.cfg.wwwroot +'/pix/i/loading.gif\'>'
                +'</div>');
            objBody.append(this.loadingimage);

            // Create the div for panel.
            var objBody = Y.one(document.body);
            var paneltitle = Y.Node.create('<div id="imagetitleoverlay" class="hiddenoverlay"></div>');
            objBody.append(paneltitle);
            var panel = Y.Node.create('<div id="imageoverlay" class="hiddenoverlay"></div>');
            objBody.append(panel);

            /// Create the panel.
            this.panel = new M.core.dialogue({
                headerContent:Y.one('#imagetitleoverlay').get('innerHTML'),
                bodyContent:Y.one('#imageoverlay').get('innerHTML'),
                visible: false, //by default it is not displayed
                lightbox : false,
                zIndex:100
            });

            this.panel.render();
            this.panel.hide();

            //attach a show event on the image divs (<tag id='image-X'>)
            for (var i=0;i<this.get('imageids').length;i++)
            {
                var imageid = this.get('imageids')[i];
                this.imageidnumbers[imageid] = this.get('imagenumbers')[i];
                Y.one('#image-'+imageid).on('click', this.show, this, imageid, 1);
            }

        },

        show : function (e, imageid, screennumber) {

            if (this.imageloadingevent != null) {
                this.imageloadingevent.detach();
            }

            var url = this.get('huburl') + "/local/hub/webservice/download.php?courseid="
            + imageid + "&filetype=screenshot&imagewidth=original&screenshotnumber=" + screennumber;

            /// set the mask
            if (this.get('maskNode')) {
                this.get('maskNode').remove();
            }
            var objBody = Y.one(document.body);
            var mask = Y.Node.create('<div id="ss-mask"><!-- --></div>');
            objBody.prepend(mask);
            this.set('maskNode', Y.one('#ss-mask'));

            //display loading image
            Y.one('#hubloadingimage').setStyle('display', 'block');
            Y.one('#hubloadingimage').setStyle("position", 'fixed');
            Y.one('#hubloadingimage').setStyle("top", '50%');
            Y.one('#hubloadingimage').setStyle("left", '50%');

            var windowheight = e.target.get('winHeight');
            var windowwidth = e.target.get('winWidth');

            var maxheight = windowheight - 150;

            //load the title + link to next image
            var paneltitle = Y.one('#imagetitleoverlay');
            var previousimagelink = "<div id=\"previousarrow\" class=\"imagearrow\">←</div>";
            var nextimagelink = "<div id=\"nextarrow\" class=\"imagearrow\">→</div>";

            // Need to load the images in the panel.
            var panel = Y.one('#imageoverlay');
            panel.setContent('');

            panel.append(Y.Node.create('<div style="text-align:center"><img id=\"imagetodisplay\" src="' + url
                + '" style="max-height:' + maxheight + 'px;"></div>'));
            this.panel.destroy();
            this.panel = new M.core.dialogue({
                headerContent:previousimagelink + '<div id=\"imagenumber\" class=\"imagetitle\"><h1> Image '
                + screennumber + ' / ' + this.imageidnumbers[imageid] + ' </h1></div>' + nextimagelink,
                bodyContent:Y.one('#imageoverlay').get('innerHTML'),
                visible: false, //by default it is not displayed
                lightbox : false,
                zIndex:100,
                closeButtonTitle: this.get('closeButtonTitle')
            });
            this.panel.render();
            this.panel.hide(); //show the panel
            this.panel.set("centered", true);

            e.halt(); // we are going to attach a new 'hide panel' event to the body,
            // because javascript always propagate event to parent tag,
            // we need to tell Yahoo to stop to call the event on parent tag
            // otherwise the hide event will be call right away.

            //once the image is loaded, update display
            this.imageloadingevent = Y.one('#imagetodisplay').on('load', function(e, url){
                //hide the loading image
                Y.one('#hubloadingimage').setStyle('display', 'none');

                //display the screenshot
                var screenshot = new Image();
                screenshot.src = url;

                var panelwidth = windowwidth - 100;
                if(panelwidth > screenshot.width) {
                    panelwidth = screenshot.width;
                }

                this.panel.set('width', panelwidth);
                this.panel.set("centered", true);
                this.panel.show();

                // Focus on the close button
                this.panel.get('buttons').header[0].focus();

            }, this, url);

            var previousnumber = screennumber - 1;
            var nextnumber = screennumber + 1;
            if (previousnumber == 0) {
                previousnumber = this.imageidnumbers[imageid];
            }
            if (nextnumber > this.imageidnumbers[imageid]) {
                nextnumber = 1;
            }

            Y.one('#previousarrow').on('click', this.show, this, imageid, previousnumber);
            Y.one('#nextarrow').on('click', this.show, this, imageid, nextnumber);
            Y.one('#imagenumber').on('click', this.show, this, imageid, nextnumber);

            // We add a new event on the body in order to hide the panel for the next click.
            this.event = Y.one(document.body).on('click', this.hide, this);
            // We add a new event on the panel in order to hide the panel for the next click (touch device).
            this.panelevent = Y.one("#imageoverlay").on('click', this.hide, this);

            this.panel.on('visibleChange',function(e){
                if(e.newVal == 0){
                    this.get('maskNode').remove()
                }
            }, this);
        },

        hide : function (e) {

            // remove the mask
            this.get('maskNode').remove();

            //hide the loading image
            Y.one('#hubloadingimage').setStyle('display', 'none');

            this.panel.hide(); //hide the panel
            if (this.event != null) {
                this.event.detach(); //we need to detach the body hide event
            //Note: it would work without but create js warning everytime
            //we click on the body
            }
            if (this.panelevent != null) {
                this.panelevent.detach(); //we need to detach the panel hide event
            //Note: it would work without but create js warning everytime
            //we click on the body
            }
        }

    }, {
        NAME : IMAGEGALLERYNAME,
        ATTRS : {
            imageids: {},
            imagenumbers: {},
            huburl: {},
            closeButtonTitle : {
                validator : Y.Lang.isString,
                value : 'Close'
            }
        }
    });

    M.blocks_community = M.blocks_community || {};
    M.blocks_community.init_imagegallery = function(params) {
        return new IMAGEGALLERY(params);
    }

}, '@VERSION@', {
    requires:['base','node', 'moodle-core-notification']
});
