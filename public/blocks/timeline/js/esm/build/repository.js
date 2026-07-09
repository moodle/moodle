import{fetchOne as t}from"@moodle/lms/core/ajax";/**
 * Data-access layer for the Timeline block — wraps block_timeline and core_calendar web services.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const s=e=>t({methodname:"block_timeline_get_timeline_events",args:{timesortfrom:e.timesortfrom??0,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}}),r=e=>t({methodname:"block_timeline_get_courses_with_events",args:{starttime:e.starttime??null,endtime:e.endtime??null,limit:e.limit??2,offset:e.offset??0,searchvalue:e.searchvalue??null}}),i=e=>t({methodname:"core_calendar_get_action_events_by_course",args:{courseid:e.courseid,timesortfrom:e.timesortfrom,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}});export{r as getCoursesWithEvents,i as getEventsByCourse,s as getTimelineEvents};
