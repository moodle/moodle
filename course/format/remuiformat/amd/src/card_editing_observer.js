define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, Ajax, Templates, Notification) {

    /**
     * element SELECTORS list
     * @type {Object}
     */
	var SELECTORS = {
		GENERAL_ROOT: '.general-section',
		ACTIVITY: 'li.activity',
		COURSE_PROGRESS: '.course-prgress-container'
	};

    /**
     * Ajax PROMISES
     * @type {Object}
     */
	var PROMISES = {
        /**
         * GET_COURSE_PROGRESS promise call
         * @param {Integer} courseid Course id
         */
		GET_COURSE_PROGRESS: function(courseid) {
			return Ajax.call([{
                methodname: 'format_remuiformat_course_progress_data',
                args: {
                    courseid: courseid
                }
            }])[0];
		}
	};

    /**
     * Update course progress when activity duplicated or deleted
     */
    function update_course_progress() {
        var container = $(SELECTORS.GENERAL_ROOT + ' ' + SELECTORS.COURSE_PROGRESS);
        var courseid = container.data('courseid');
        PROMISES.GET_COURSE_PROGRESS(courseid)
        .done(function(response) {
            response.courseid = courseid;
            Templates.render('format_remuiformat/course_progress', response)
            .done(function(html, js) {
                Templates.replaceNode(container, html, js);
            })
            .fail(Notification.exception);
        })
        .fail(Notification.exception);
    }

    /**
     * Initialize js
     */
    function init() {
        $(document).bind('DOMNodeRemoved', function(event) {
            if ($(event.target).is('li.activity')) {
                update_course_progress();
            }
        });
    }

    // Must return the init function.
    return {
        init: init
    };
});
