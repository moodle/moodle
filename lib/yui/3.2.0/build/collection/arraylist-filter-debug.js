/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add('arraylist-filter', function(Y) {

/**
 * Collection utilities beyond what is provided in the YUI core
 * @module collection
 * @submodule arraylist-filter
 */

/**
 * Adds filter method to ArrayList prototype
 * @class ArrayList~filter
 */
Y.mix( Y.ArrayList.prototype, {

    /**
     * <p>Create a new ArrayList (or augmenting class instance) from a subset
     * of items as determined by the boolean function passed as the
     * argument.  The original ArrayList is unchanged.</p>
     *
     * <p>The validator signature is <code>validator( item )</code>.</p>
     *
     * @method filter
     * @param validator { Function } Boolean function to determine in or out
     * @return { ArrayList } New instance based on who passed the validator
     */
    filter: function ( validator ) {
        var items = [];

        Y.Array.each( this._items, function ( item, i ) {
            item = this.item( i );

            if ( validator( item ) ) {
                items.push( item );
            }
        }, this);

        return new this.constructor( items );
    }

} );


}, '3.2.0' ,{requires:['arraylist']});
