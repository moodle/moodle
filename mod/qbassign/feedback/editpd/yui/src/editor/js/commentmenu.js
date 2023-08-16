var COMMENTMENUNAME = "Commentmenu",
    COMMENTMENU;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-qbassignfeedback_editpd-editor
 */

/**
 * COMMENTMENU
 * This is a drop down list of comment context functions.
 *
 * @namespace M.qbassignfeedback_editpd
 * @class commentmenu
 * @constructor
 * @extends M.qbassignfeedback_editpd.dropdown
 */
COMMENTMENU = function(config) {
    COMMENTMENU.superclass.constructor.apply(this, [config]);
};

Y.extend(COMMENTMENU, M.qbassignfeedback_editpd.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function(config) {
        var commentlinks,
            link,
            body,
            comment;

        comment = this.get('comment');
        // Build the list of menu items.
        commentlinks = Y.Node.create('<ul role="menu" class="qbassignfeedback_editpd_menu"/>');

        link = Y.Node.create('<li><a tabindex="-1" href="#">' +
               M.util.get_string('addtoquicklist', 'qbassignfeedback_editpd') +
               '</a></li>');
        link.on('click', comment.add_to_quicklist, comment);
        link.on('key', comment.add_to_quicklist, 'enter,space', comment);

        commentlinks.append(link);

        link = Y.Node.create('<li><a tabindex="-1" href="#">' +
               M.util.get_string('deletecomment', 'qbassignfeedback_editpd') +
               '</a></li>');
        link.on('click', function(e) {
            e.preventDefault();
            this.menu.hide();
            this.remove();
        }, comment);

        link.on('key', function() {
            comment.menu.hide();
            comment.remove();
        }, 'enter,space', comment);

        commentlinks.append(link);

        link = Y.Node.create('<li><hr/></li>');
        commentlinks.append(link);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('commentcontextmenu', 'qbassignfeedback_editpd'));

        body = Y.Node.create('<div/>');

        // Set the body content.
        body.append(commentlinks);
        this.set('bodyContent', body);

        COMMENTMENU.superclass.initializer.call(this, config);
    },

    /**
     * Show the menu.
     *
     * @method show
     * @return void
     */
    show: function() {
        var commentlinks = this.get('boundingBox').one('ul');
            commentlinks.all('.quicklist_comment').remove(true);
        var comment = this.get('comment');

        comment.deleteme = false; // Cancel the deleting of blank comments.

        // Now build the list of quicklist comments.
        Y.each(comment.editor.quicklist.comments, function(quickcomment) {
            var listitem = Y.Node.create('<li class="quicklist_comment"></li>'),
                linkitem = Y.Node.create('<a href="#" tabindex="-1">' + quickcomment.rawtext + '</a>'),
                deletelinkitem = Y.Node.create('<a href="#" tabindex="-1" class="delete_quicklist_comment">' +
                                               '<img src="' + M.util.image_url('t/delete', 'core') + '" ' +
                                               'alt="' + M.util.get_string('deletecomment', 'qbassignfeedback_editpd') + '"/>' +
                                               '</a>');
            linkitem.setAttribute('title', quickcomment.rawtext);
            listitem.append(linkitem);
            listitem.append(deletelinkitem);

            commentlinks.append(listitem);

            listitem.on('click', comment.set_from_quick_comment, comment, quickcomment);
            listitem.on('key', comment.set_from_quick_comment, 'space,enter', comment, quickcomment);

            deletelinkitem.on('click', comment.remove_from_quicklist, comment, quickcomment);
            deletelinkitem.on('key', comment.remove_from_quicklist, 'space,enter', comment, quickcomment);
        }, this);

        COMMENTMENU.superclass.show.call(this);
    }
}, {
    NAME: COMMENTMENUNAME,
    ATTRS: {
        /**
         * The comment this menu is attached to.
         *
         * @attribute comment
         * @type M.qbassignfeedback_editpd.comment
         * @default null
         */
        comment: {
            value: null
        }

    }
});

M.qbassignfeedback_editpd = M.qbassignfeedback_editpd || {};
M.qbassignfeedback_editpd.commentmenu = COMMENTMENU;
