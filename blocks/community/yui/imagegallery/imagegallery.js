YUI.add('moodle-block_community-imagegallery', function(Y) {

    var IMAGEGALLERYNAME = 'blocks_community_imagegallery';

    var IMAGEGALLERY = function() {
        IMAGEGALLERY.superclass.constructor.apply(this, arguments);
    };

    Y.extend(IMAGEGALLERY, Y.Base, {

        event:null,
        previousevent:null,
        nextevent:null,
        overlayevent:null,
        overlay:null, //all the comment boxes
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

            /// create the div for overlay
            var objBody = Y.one(document.body);
            var overlaytitle = Y.Node.create('<div id="imagetitleoverlay" class="hiddenoverlay"></div>');
            objBody.append(overlaytitle);
            var overlay = Y.Node.create('<div id="imageoverlay" class="hiddenoverlay"></div>');
            objBody.append(overlay);

            /// create the overlay
            this.overlay = new M.core.dialogue({
                headerContent:Y.one('#imagetitleoverlay').get('innerHTML'),
                bodyContent:Y.one('#imageoverlay').get('innerHTML'),
                visible: false, //by default it is not displayed
                lightbox : false,
                zIndex:100
            });

            this.overlay.render();
            this.overlay.hide();

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
            var overlaytitle = Y.one('#imagetitleoverlay');
            var previousimagelink = "<div id=\"previousarrow\" class=\"imagearrow\">←</div>";
            var nextimagelink = "<div id=\"nextarrow\" class=\"imagearrow\">→</div>";

            /// need to load the images in the overlay
            var overlay = Y.one('#imageoverlay');
            overlay.setContent('');


            overlay.append(Y.Node.create('<div style="text-align:center"><img id=\"imagetodisplay\" src="' + url
                + '" style="max-height:' + maxheight + 'px;"></div>'));
            this.overlay.destroy();
            this.overlay = new M.core.dialogue({
                headerContent:previousimagelink + '<div id=\"imagenumber\" class=\"imagetitle\"> Image '
                + screennumber + ' / ' + this.imageidnumbers[imageid] + ' </div>' + nextimagelink,
                bodyContent:Y.one('#imageoverlay').get('innerHTML'),
                visible: false, //by default it is not displayed
                lightbox : false,
                zIndex:100
            });
            this.overlay.render();
            this.overlay.hide(); //show the overlay
            this.overlay.set("centered", true);

            e.halt(); // we are going to attach a new 'hide overlay' event to the body,
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

                var overlaywidth = windowwidth - 100;
                if(overlaywidth > screenshot.width) {
                    overlaywidth = screenshot.width;
                }

                this.overlay.set('width', overlaywidth);
                this.overlay.set("centered", true);
                this.overlay.show();

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

            //we add a new event on the body in order to hide the overlay for the next click
            this.event = Y.one(document.body).on('click', this.hide, this);
            //we add a new event on the overlay in order to hide the overlay for the next click (touch device)
            this.overlayevent = Y.one("#imageoverlay").on('click', this.hide, this);

            this.overlay.on('visibleChange',function(e){
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

            this.overlay.hide(); //hide the overlay
            if (this.event != null) {
                this.event.detach(); //we need to detach the body hide event
            //Note: it would work without but create js warning everytime
            //we click on the body
            }
            if (this.overlayevent != null) {
                this.overlayevent.detach(); //we need to detach the overlay hide event
            //Note: it would work without but create js warning everytime
            //we click on the body
            }
        }

    }, {
        NAME : IMAGEGALLERYNAME,
        ATTRS : {
            imageids: {},
            imagenumbers: {},
            huburl: {}
        }
    });

    M.blocks_community = M.blocks_community || {};
    M.blocks_community.init_imagegallery = function(params) {
        return new IMAGEGALLERY(params);
    }

}, '@VERSION@', {
    requires:['base','node','overlay', 'moodle-enrol-notification']
});
