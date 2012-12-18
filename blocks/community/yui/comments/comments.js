YUI.add('moodle-block_community-comments', function(Y) {

    var COMMENTSNAME = 'blocks_community_comments';

    var COMMENTS = function() {
        COMMENTS.superclass.constructor.apply(this, arguments);
    };

    Y.extend(COMMENTS, Y.Base, {

        event:null,
        panelevent: null,
        panels: [], //all the comment boxes

        initializer : function(params) {

            //attach a show event on the div with id = comments
            for (var i=0;i<this.get('commentids').length;i++)
            {
                var commentid = this.get('commentids')[i];
                this.panels[commentid] = new M.core.dialogue({
                    headerContent:Y.Node.create('<h1>')
                        .append(Y.one('#commentoverlay-'+commentid+' .commenttitle').get('innerHTML')),
                    bodyContent:Y.one('#commentoverlay-'+commentid).get('innerHTML'),
                    visible: false, //by default it is not displayed
                    lightbox : false,
                    zIndex:100,
                    closeButtonTitle: this.get('closeButtonTitle')
                });

                this.panels[commentid].get('contentBox').one('.commenttitle').remove();
                this.panels[commentid].render();
                this.panels[commentid].hide();

                Y.one('#comments-'+commentid).on('click', this.show, this, commentid);
            }

        },

        show : function (e, commentid) {

            // Hide all panels.
            for (var i=0;i<this.get('commentids').length;i++)
            {
                this.hide(e, this.get('commentids')[i]);
            }

            this.panels[commentid].show(); //show the panel

            e.halt(); // we are going to attach a new 'hide panel' event to the body,
            // because javascript always propagate event to parent tag,
            // we need to tell Yahoo to stop to call the event on parent tag
            // otherwise the hide event will be call right away.

            // We add a new event on the body in order to hide the panel for the next click.
            this.event = Y.one(document.body).on('click', this.hide, this, commentid);
            // We add a new event on the panel in order to hide the panel for the next click (touch device).
            this.panelevent = Y.one("#commentoverlay-"+commentid).on('click', this.hide, this, commentid);

            // Focus on the close button
            this.panels[commentid].get('buttons').header[0].focus();
        },

        hide : function (e, commentid) {
            this.panels[commentid].hide(); //hide the panel
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
        NAME : COMMENTSNAME,
        ATTRS : {
            commentids: {},
            closeButtonTitle : {
                validator : Y.Lang.isString,
                value : 'Close'
            }
        }
    });

    M.blocks_community = M.blocks_community || {};
    M.blocks_community.init_comments = function(params) {
        return new COMMENTS(params);
    }

}, '@VERSION@', {
    requires:['base', 'moodle-core-notification']
});
