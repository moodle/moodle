define ("block_starredcourses/repository",["jquery","core/ajax","core/notification"],function(a,b,c){return{getStarredCourses:function getStarredCourses(a){var d=b.call([{methodname:"block_starredcourses_get_starred_courses",args:a}])[0];d.fail(c.exception);return d}}});
//# sourceMappingURL=repository.min.js.map
