YUI.add('moodle-block_community-comments', function(Y) {

    var COMMENTSNAME = 'blocks_community_comments';

    var COMMENTS = function() {
        COMMENTS.superclass.constructor.apply(this, arguments);
    };

    Y.extend(COMMENTS, Y.Base, {

        event:null,
        overlayevent:null,
        overlays: [], //all the comment boxes

        initializer : function(params) {

            //attach a show event on the div with id = comments
            for (var i=0;i<this.get('commentids').length;i++)
            {
                var commentid = this.get('commentids')[i];
                this.overlays[commentid] = new M.core.dialogue({
                    headerContent:Y.one('#commentoverlay-'+commentid+' .commenttitle').get('innerHTML'),
                    bodyContent:Y.one('#commentoverlay-'+commentid).get('innerHTML'),
                    visible: false, //by default it is not displayed
                    lightbox : false,
                    zIndex:100,
                    height: '350px'
                });

                this.overlays[commentid].get('contentBox').one('.commenttitle').remove();
                this.overlays[commentid].render();
                this.overlays[commentid].hide();

                // position the overlay in the middle of the web browser window
                var WidgetPositionAlign = Y.WidgetPositionAlign;
                this.overlays[commentid].set("align", {
                    node:"", //empty => viewport
                    points:[WidgetPositionAlign.CC, WidgetPositionAlign.CC]
                });

                Y.one('#comments-'+commentid).on('click', this.show, this, commentid);
            }

        },

        show : function (e, commentid) {

            //hide all overlays
            for (var i=0;i<this.get('commentids').length;i++)
            {
                this.hide(e, this.get('commentids')[i]);
            }

            this.overlays[commentid].show(); //show the overlay

            e.halt(); // we are going to attach a new 'hide overlay' event to the body,
            // because javascript always propagate event to parent tag,
            // we need to tell Yahoo to stop to call the event on parent tag
            // otherwise the hide event will be call right away.

            //we add a new event on the body in order to hide the overlay for the next click
            this.event = Y.one(document.body).on('click', this.hide, this, commentid);
            //we add a new event on the overlay in order to hide the overlay for the next click (touch device)
            this.overlayevent = Y.one("#commentoverlay-"+commentid).on('click', this.hide, this, commentid);
        },

        hide : function (e, commentid) {
            this.overlays[commentid].hide(); //hide the overlay
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
        NAME : COMMENTSNAME,
        ATTRS : {
            commentids: {}
        }
    });

    M.blocks_community = M.blocks_community || {};
    M.blocks_community.init_comments = function(params) {
        return new COMMENTS(params);
    }

}, '@VERSION@', {
    requires:['base','overlay', 'moodle-enrol-notification']
});