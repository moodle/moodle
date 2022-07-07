define ("core/log",["core/loglevel"],function(a){var b=a.methodFactory;a.methodFactory=function(a,c){var d=b(a,c);return function(a,b){if(b){d(b+": "+a)}else{d(a)}}};a.setConfig=function(b){if("undefined"!=typeof b.level){a.setLevel(b.level)}};return a});
//# sourceMappingURL=log.min.js.map
