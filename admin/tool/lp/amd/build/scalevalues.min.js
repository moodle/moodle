define ("tool_lp/scalevalues",["jquery","core/ajax"],function(a,b){var c=[];return{get_values:function get_values(d){var e=a.Deferred();if("undefined"==typeof c[d]){b.call([{methodname:"core_competency_get_scale_values",args:{scaleid:d},done:function done(a){c[d]=a;e.resolve(a)},fail:e.reject}])}else{e.resolve(c[d])}return e.promise()}}});
//# sourceMappingURL=scalevalues.min.js.map
