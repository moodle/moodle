define(['jquery', 'core_course/actions'], function($, action) {

	function init() {
		// Register a function to be executed after D&D of an activity.
        Y.use('moodle-course-coursebase', function() {
        	M.course.coursebase.registermodules.forEach(function(object, index) {
        		if (object.set_visibility_resource_ui != undefined) {
	                // Ignore camelcase eslint rule for the next line because it is an expected name of the callback.
	                // eslint-disable-next-line camelcase
	                object.set_visibility_resource_ui = function(args) {

	                	// We don't need to render activity again.
	                	return;
	                	// Following code is commented to skip rendering activity card.
	                    // var mainelement = $(args.element.getDOMNode());
	                    // var cmid = getModuleId(mainelement);
	                    // if (cmid) {
	                    //     var sectionreturn = mainelement.find('.' + CSS.EDITINGMOVE).attr('data-sectionreturn');
	                    //     refreshModule(mainelement, cmid, sectionreturn);
	                    // }
	                }
	                M.course.coursebase.registermodules[index] = object;
        		}
        	});
            // M.course.coursebase.register_module({
            // });
        });
	}
	return {
		init: init
	};
});