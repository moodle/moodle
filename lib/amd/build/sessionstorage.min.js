define ("core/sessionstorage",["core/config","core/storagewrapper"],function(a,b){var c=new b(window.sessionStorage);return{get:function get(a){return c.get(a)},set:function set(a,b){return c.set(a,b)}}});
//# sourceMappingURL=sessionstorage.min.js.map
