define ("core/chart_builder",["jquery"],function(a){return{make:function make(b){var c=a.Deferred();require(["core/chart_"+b.type],function(a){var d=a.prototype.create(a,b);c.resolve(d)});return c.promise()}}});
//# sourceMappingURL=chart_builder.min.js.map
