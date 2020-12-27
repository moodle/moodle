define ("core/pending",["jquery"],function(a){var b=function(b){var c=a.Deferred();b=b||{};M.util.js_pending(b);c.then(function(){return M.util.js_complete(b)}).catch();return c};b.prototype.constructor=b;return b});
//# sourceMappingURL=pending.min.js.map
