/**
 * So you want to override the navigation huh ??
 * Make it look like your own and totally customise it to be way cool !!
 *
 * Well now you can by following the instructions in this file.
 *
 * It will be essential to have a clear idea about what it is you want to acheive,
 * whilst it is possible to override nearly all of the navbar settings/methods it's
 * not nesecarily going to be an easy task.
 *
 * To begin you must understand the structure of the blocks and particually the navbar
 * object. The following outlines the basic structure:
 *
 *      - Namespace: blocks
 *          - Func: setup_generic_block         Creates a new generic block instance
 *          - Class: genericblock                   Generic block class
 *          - Namespace: navbar
 *              - Var: count                        The # of items that have EVER existed on the navbar
 *              - Var: exists                       True if the navbar exists
 *              - Var: items                        An array of items on the navbar
 *              - Var: node                         The node that is the navbar
 *              - Var: strings                      An object containing strings for the navbar
 *              - Namespace: cfg
 *                  - Var: buffer                   The space buffer around panels
 *                  - Var: position                 The position of the navbar
 *                  - Var: orientation              The orientation of the navbar
 *                  - Namespace: display
 *                      ............                A series of display parameters
 *                  - Namespace: css
 *                      ............                A series of CSS class names
 *                  - Namespace: panel
 *                      ............                A series of conf options for YUI panels
 *              - Func: add                         Adds an item to the navbar
 *              - Func: draw                        Creates the navbar and adds it to the page
 *              - Func: remove                      Removes an item from the navbar
 *              - Func: remove_all                  Removes all items from the navbar
 *              - Func: resize                      Calls the navbar to resize its active item
 *              - Func: hide_all                    Calls the navbar to hide all active items
 *              - Class: item                       A navbar item class
 *              - Namespace: abstract_block_class   A namespace containing all of the properties
 *                      .............               and methods that will be used as the default
 *                      .............               methods for the generic block class.
 *              - Namespace: abstract_item_class    A namespace containing all of the properties
 *                      .............               and methods for the navbar item class
 *              
 * From the structure above you are able to immediatly override any of the vars
 * that are associated with the navigation by simply assigning them a value as
 * shown below:
 * 
 *      blocks.navbar.cfg.buffer = 20; // or
 *
 * You are also able to override all of the properties and methods of the two
 * abstract classes that manage all of the interaction for the blocks and navbar
 * items thanks to the prototyping method that is being used to build the classes.
 *
 * To override a method simply copy the following style of coding:
 *
 *      blocks.genericblock.prototype.init = function(uid) {
 *          // The code for the new init method which will be executed in the
 *          // objects scope and override the old init method.
 *      }
 *
 *      // OR if the following is easier for you to understand
 *
 *      function new_init_method(uid) {
 *          // The code for the new init method which will be executed in the
 *          // objects scope and override the old init method.
 *      }
 *      blocks.genericblock.prototype.init = new_init_method()
 *
 * Alternativily for the navbar items class the there are a series of actions that
 * get fired that you may want to listen to. The events defined are as follows:
 *
 *      navbaritem:drawstart        draw is called
 *      navbaritem:drawcomplete     draw is complete
 *      navbaritem:showstart        show is called
 *      navbaritem:showcomplete     show is complete
 *      navbaritem:hidestart        hide is called
 *      navbaritem:hidecomplete     hide is complete
 *      navbaritem:resizestart      resize is called
 *      navbaritem:resizecomplete   resize is complete
 *      navbaritem:itemremoved      item is removed from the navbar
 *
 * You can listen to any of these events by first finding the appropriate item within
 * the navbar.items array and then calling the following on it:
 *
 *      var uid = x;
 *      blocks.navbar.items[uid].on('navbaritem:showstart', callback, scope);
 *      function callback(navbaritem) {
 *          // What ever you want to do can go here
 *      }
 *
 */

// If this isn't set we don't need an override at all
if (blocks.genericblock) {

    /**
     * Override the default resize_block_space method so that we can ensure
     * it works for this template
     * @param {Y.Node} blocknode
     */
    blocks.genericblock.prototype.resize_block_space = function(blocknode) {
        var blockregion = blocknode.ancestor('#block-region');
        if (blockregion) {
            if (blockregion.all('.sideblock').size() === 0 && this.blockspacewidth === null) {
                // Some spiffy code to reduce the template sideblock to 0 width
                this.blockspacewidth = blockregion.getStyle('width');
            } else if (this.blockspacewidth !== null) {
                // Some spiffy code to set the sideblock width back to the original width
                this.blockspacewidth = null;
            }
        }
    }
    
}