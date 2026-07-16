import{fetchOne as t}from"@moodle/lms/core/ajax";/**
 * Data-access layer for the Timeline block — wraps existing core_calendar and
 * core_course web services; block_timeline defines none of its own.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const s=e=>t({methodname:"core_calendar_get_action_events_by_timesort",args:{timesortfrom:e.timesortfrom??0,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}}),n=e=>t({methodname:"core_course_get_enrolled_courses_by_timeline_classification",args:{classification:"all",limit:e.limit??2,offset:e.offset??0,sort:"fullname ASC",searchvalue:e.searchvalue??null}}),o=e=>t({methodname:"core_calendar_get_action_events_by_courses",args:{courseids:e.courseids,timesortfrom:e.timesortfrom??null,timesortto:e.timesortto??null,limitnum:e.limitnum??10,searchvalue:e.searchvalue??null}}),i=e=>t({methodname:"core_calendar_get_action_events_by_course",args:{courseid:e.courseid,timesortfrom:e.timesortfrom,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}});export{n as getEnrolledCourses,i as getEventsByCourse,o as getEventsByCourses,s as getTimelineEvents};
