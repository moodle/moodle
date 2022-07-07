define ("core/localstorage",["core/config","core/storagewrapper"],function(a,b){var c=new b(window.localStorage);return{get:function get(a){return c.get(a)},set:function set(a,b){return c.set(a,b)}}});
//# sourceMappingURL=localstorage.min.js.map
