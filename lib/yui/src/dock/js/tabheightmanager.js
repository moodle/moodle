/* global TABHEIGHTMANAGER, LOGNS */

/**
 * Dock JS.
 *
 * This file contains the tab height manager.
 * The tab height manager is responsible for ensure all tabs are visible all the time.
 *
 * @module moodle-core-dock
 */

/**
 * Tab height manager.
 *
 * @namespace M.core.dock
 * @class TabHeightManager
 * @constructor
 * @extends Base
 */
TABHEIGHTMANAGER = function() {
    TABHEIGHTMANAGER.superclass.constructor.apply(this, arguments);
};
TABHEIGHTMANAGER.prototype = {
    /**
     * Initialises the dock sizer which then attaches itself to the required
     * events in order to monitor the dock
     * @method initializer
     */
    initializer : function() {
        var dock = this.get('dock');
        dock.on('dock:itemschanged', this.checkSizing, this);
        Y.on('windowresize', this.checkSizing, this);
    },
    /**
     * Check if the size dock items needs to be adjusted
     * @method checkSizing
     */
    checkSizing : function() {
        var dock = this.get('dock'),
            node = dock.get('dockNode'),
            items = dock.dockeditems,
            containermargin = parseInt(node.one('.dockeditem_container').getStyle('marginTop').replace('/[^0-9]+$/', ''), 10),
            dockheight = node.get('offsetHeight') - containermargin,
            controlheight = node.one('.controls').get('offsetHeight'),
            buffer = (dock.get('bufferPanel') * 3),
            possibleheight = dockheight - controlheight - buffer - (items.length*2),
            totalheight = 0,
            id, dockedtitle;
        if (items.length > 0) {
            for (id in items) {
                if (Y.Lang.isNumber(id) || Y.Lang.isString(id)) {
                    dockedtitle = Y.one(items[id].get('title')).ancestor('.'+CSS.dockedtitle);
                    if (dockedtitle) {
                        if (this.get('enabled')) {
                            dockedtitle.setStyle('height', 'auto');
                        }
                        totalheight += dockedtitle.get('offsetHeight') || 0;
                    }
                }
            }
            if (totalheight > possibleheight) {
                this.enable(possibleheight);
            }
        }
    },
    /**
     * Enables the dock sizer and resizes where required.
     * @method enable
     * @param {Number} possibleheight
     */
    enable : function(possibleheight) {
        var dock = this.get('dock'),
            items = dock.dockeditems,
            count = dock.count,
            runningcount = 0,
            usedheight = 0,
            id, itemtitle, itemheight, offsetheight;
        Y.log('Enabling the dock tab sizer.', 'debug', LOGNS);
        this.set('enabled', true);
        for (id in items) {
            if (Y.Lang.isNumber(id) || Y.Lang.isString(id)) {
                itemtitle = Y.one(items[id].get('title')).ancestor('.'+CSS.dockedtitle);
                if (!itemtitle) {
                    continue;
                }
                itemheight = Math.floor((possibleheight-usedheight) / (count - runningcount));
                offsetheight = itemtitle.get('offsetHeight');
                itemtitle.setStyle('overflow', 'hidden');
                if (offsetheight > itemheight) {
                    itemtitle.setStyle('height', itemheight+'px');
                    usedheight += itemheight;
                } else {
                    usedheight += offsetheight;
                }
                runningcount++;
            }
        }
    }
};
Y.extend(TABHEIGHTMANAGER, Y.Base, TABHEIGHTMANAGER.prototype, {
    NAME : 'moodle-core-tabheightmanager',
    ATTRS : {
        /**
         * The dock.
         * @attribute dock
         * @type DOCK
         * @writeOnce
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * True if the item_sizer is being used, false otherwise.
         * @attribute enabled
         * @type Bool
         */
        enabled : {
            value : false
        }
    }
});
