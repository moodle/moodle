YUI.add('moodle-core-lockscroll', function (Y, NAME) {

/**
 * Provides the ability to lock the scroll for a page, allowing nested
 * locking.
 *
 * @module moodle-core-lockscroll
 */

/**
 * Provides the ability to lock the scroll for a page.
 *
 * This is achieved by applying the class 'lockscroll' to the body Node.
 *
 * Nested widgets are also supported and the scroll lock is only removed
 * when the final plugin instance is disabled.
 *
 * @class M.core.LockScroll
 * @extends Plugin.Base
 */
Y.namespace('M.core').LockScroll = Y.Base.create('lockScroll', Y.Plugin.Base, [], {

    /**
     * Whether the LockScroll has been activated.
     *
     * @property _enabled
     * @type Boolean
     * @protected
     */
    _enabled: false,

    /**
     * Handle destruction of the lockScroll instance, including disabling
     * of the current instance.
     *
     * @method destructor
     */
    destructor: function() {
        this.disableScrollLock();
    },

    /**
     * Start locking the page scroll.
     *
     * This is achieved by applying the lockscroll class to the body Node.
     *
     * A count of the total number of active, and enabled, lockscroll instances is also kept on
     * the body to ensure that premature disabling does not occur.
     *
     * @method enableScrollLock
     * @param {Boolean} forceOnSmallWindow Whether to enable the scroll lock, even for small window sizes.
     * @chainable
     */
    enableScrollLock: function(forceOnSmallWindow) {
        if (this.isActive()) {
            Y.log('LockScroll already active. Ignoring enable request', 'warn', 'moodle-core-lockscroll');
            return;
        }

        if (!this.shouldLockScroll(forceOnSmallWindow)) {
            Y.log('Dialogue height greater than window height. Ignoring enable request.', 'warn', 'moodle-core-lockscroll');
            return;
        }

        Y.log('Enabling LockScroll.', 'debug', 'moodle-core-lockscroll');
        this._enabled = true;
        var body = Y.one(Y.config.doc.body);

        // Get width of body before turning on lockscroll.
        var widthBefore = body.getComputedStyle('width');

        // We use a CSS class on the body to handle the actual locking.
        body.addClass('lockscroll');

        // Increase the count of active instances - this is used to ensure that we do not
        // remove the locking when parent windows are still open.
        // Note: We cannot use getData here because data attributes are sandboxed to the instance that created them.
        var currentCount = parseInt(body.getAttribute('data-activeScrollLocks'), 10) || 0,
            newCount = currentCount + 1;
        body.setAttribute('data-activeScrollLocks', newCount);
        Y.log("Setting the activeScrollLocks count from " + currentCount + " to " + newCount,
                'debug', 'moodle-core-lockscroll');

        // When initially enabled, set the body max-width to its current width. This
        // avoids centered elements jumping because the width changes when scrollbars
        // disappear.
        if (currentCount === 0) {
            body.setStyle('maxWidth', widthBefore);
        }

        return this;
    },

    /**
     * Recalculate whether lock scrolling should be on or off.
     *
     * @method shouldLockScroll
     * @param {Boolean} forceOnSmallWindow Whether to enable the scroll lock, even for small window sizes.
     * @return boolean
     */
    shouldLockScroll: function(forceOnSmallWindow) {
        var dialogueHeight = this.get('host').get('boundingBox').get('region').height,
            // Most modern browsers use win.innerHeight, but some older versions of IE use documentElement.clientHeight.
            // We fall back to 0 if neither can be found which has the effect of disabling scroll locking.
            windowHeight = Y.config.win.innerHeight || Y.config.doc.documentElement.clientHeight || 0;

        if (!forceOnSmallWindow && dialogueHeight > (windowHeight - 10)) {
            return false;
        } else {
            return true;
        }
    },

    /**
     * Recalculate whether lock scrolling should be on or off because the size of the dialogue changed.
     *
     * @method updateScrollLock
     * @param {Boolean} forceOnSmallWindow Whether to enable the scroll lock, even for small window sizes.
     * @chainable
     */
    updateScrollLock: function(forceOnSmallWindow) {
        // Both these functions already check if scroll lock is active and do the right thing.
        if (this.shouldLockScroll(forceOnSmallWindow)) {
            this.enableScrollLock(forceOnSmallWindow);
        } else {
            this.disableScrollLock(true);
        }

        return this;
    },

    /**
     * Stop locking the page scroll.
     *
     * The instance may be disabled but the scroll lock not removed if other instances of the
     * plugin are also active.
     *
     * @method disableScrollLock
     * @chainable
     */
    disableScrollLock: function(force) {
        if (this.isActive()) {
            Y.log('Disabling LockScroll.', 'debug', 'moodle-core-lockscroll');
            this._enabled = false;

            var body = Y.one(Y.config.doc.body);

            // Decrease the count of active instances.
            // Note: We cannot use getData here because data attributes are sandboxed to the instance that created them.
            var currentCount = parseInt(body.getAttribute('data-activeScrollLocks'), 10) || 1,
                newCount = currentCount - 1;

            if (force || currentCount === 1) {
                body.removeClass('lockscroll');
                body.setStyle('maxWidth', null);
            }

            body.setAttribute('data-activeScrollLocks', currentCount - 1);
            Y.log("Setting the activeScrollLocks count from " + currentCount + " to " + newCount,
                    'debug', 'moodle-core-lockscroll');
        }

        return this;
    },

    /**
     * Return whether scroll locking is active.
     *
     * @method isActive
     * @return Boolean
     */
    isActive: function() {
        return this._enabled;
    }

}, {
    NS: 'lockScroll',
    ATTRS: {
    }
});


}, '@VERSION@', {"requires": ["plugin", "base-build"]});
