/* global ns */
/**
 * Create a field without html
 *
 * @param {mixed} parent
 * @param {object} field
 * @param {mixed} params
 * @param {function} setValue
 */
ns.None = function (parent, field, params, setValue) {
  this.parent = parent;
  this.field = field;
  this.params = params;
  this.setValue = setValue;
};

/**
 * Implementation of appendTo
 *
 * None doesn't append anything
 */
ns.None.prototype.appendTo = function () {};

/**
 * Implementation of validate
 *
 * None allways validates
 */
ns.None.prototype.validate = function () {
  return true;
};

/**
 * Collect functions to execute once the tree is complete.
 *
 * @param {function} ready
 */
ns.None.prototype.ready = function (ready) {
  this.parent.ready(ready);
};

/**
 * Remove this item.
 */
ns.None.prototype.remove = function () {
  ns.removeChildren(this.children);
};

// Tell the editor what widget we are.
ns.widgets.none = ns.None;
