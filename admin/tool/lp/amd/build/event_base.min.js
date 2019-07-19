define ("tool_lp/event_base",["jquery"],function(a){var b=function(){this._eventNode=a("<div></div>")};b.prototype._eventNode=null;b.prototype.on=function(a,b){this._eventNode.on(a,b)};b.prototype._trigger=function(a,b){this._eventNode.trigger(a,[b])};return b});
//# sourceMappingURL=event_base.min.js.map
