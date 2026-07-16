import{fetchOne as r,fetchMany as m}from"@moodle/lms/core/ajax";import{getString as u}from"@moodle/lms/core/stringUtils";import l from"@moodle/lms/core/config";/**
 * Data-access layer for the Timeline block.
 *
 * All AJAX calls live here — views only ever talk to this module, never to
 * @moodle/lms/core/ajax directly. Every call wraps an existing core_calendar
 * or core_course web service; block_timeline defines none of its own.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const v=e=>r({methodname:"core_calendar_get_action_events_by_timesort",args:{timesortfrom:e.timesortfrom??0,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}}),d=e=>r({methodname:"core_course_get_enrolled_courses_by_timeline_classification",args:{classification:"all",limit:e.limit??2,offset:e.offset??0,sort:"fullname ASC",searchvalue:e.searchvalue??null}}),p=e=>r({methodname:"core_calendar_get_action_events_by_courses",args:{courseids:e.courseids,timesortfrom:e.timesortfrom??null,timesortto:e.timesortto??null,limitnum:e.limitnum??10,searchvalue:e.searchvalue??null}}),_=e=>r({methodname:"core_calendar_get_action_events_by_course",args:{courseid:e.courseid,timesortfrom:e.timesortfrom,timesortto:e.timesortto??null,aftereventid:e.aftereventid??0,limitnum:e.limitnum??20,searchvalue:e.searchvalue??null}}),E=(e,t)=>{r({methodname:"core_user_update_user_preferences",args:{preferences:[{type:e,value:t}]}}).catch(()=>{})};async function C(e){const t=[...new Set(e)];if(t.length===0)return new Map;const n=await u("strftimedaydate","langconfig"),[o]=await m([{methodname:"core_get_user_dates",args:{contextid:l.contextid??1,timestamps:t.map(s=>({timestamp:s,format:n}))}}]);return new Map(t.map((s,i)=>[s,o.dates[i]]))}export{d as getEnrolledCourses,_ as getEventsByCourse,p as getEventsByCourses,C as getFormattedDays,v as getTimelineEvents,E as setUserPreference};
