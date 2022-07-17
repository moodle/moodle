/// Three slashes are incorrect.

//// four are also wrong. Not to talk about the missing upper and final dot

//And no-space, uhm, bad, bad!

// None of the following phpdocs should be causing problems.

/* eslint no-unreachable: "error" */

define([], function() {

    /**
     * Selector class.
     *
     * @param {String} title The title.
     */
    var Selector = function(title) {
        var self = this;

        self._title = title;
        self._reset();
    };

    /** @type {String} The title. */
    Selector.prototype._title = null;

    /**
     * This does not reset anything.
     *
     * @method _reset
     */
    Selector.prototype._reset = function() {
        alert('foo'); // eslint-disable-line no-alert

        // eslint-disable-next-line no-alert
        alert('foo');

        alert('foo'); /* eslint-disable-line no-alert */

        /* eslint-disable-next-line no-alert */
        alert('foo');
    };

    return Selector;
});
