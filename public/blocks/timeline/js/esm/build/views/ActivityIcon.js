import{ActivityIcon as n}from"@moodlehq/design-system";import{jsx as f}from"react/jsx-runtime";/**
 * Swizzlable wrapper around the design system ActivityIcon.
 *
 * Themes that need a custom icon can eject this component via the swizzle
 * manifest. All other code imports from `@moodle/lms/block_timeline/views/ActivityIcon`
 * so the override applies everywhere without touching call sites.
 *
 * @module     block_timeline/views/ActivityIcon
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */const l={assign:"assignment",bigbluebuttonbn:"bigbluebutton",data:"database",h5pactivity:"h5p",imscp:"ims-package",label:"text-and-media",lti:"external-tool",scorm:"scorm-package",qbank:"file-database"},o={archive:"file-archive",audio:"file-audio",calc:"file-spreadsheet",chart:"file-graphic",database:"file-database",document:"file-doc",draw:"file-draw",eps:"file-eps",epub:"file-epub",flash:"file-flash",gif:"file-gif",h5p:"file-h5p",image:"file-image",impress:"file-presentation",isf:"file-isf-flowchart",json:"file-json",markup:"file-code",math:"file-math",moodle:"file-moodle",oth:"file-oth",pdf:"file-pdf",powerpoint:"file-ppt",psd:"file-psd",publisher:"file-pub",sourcecode:"file-source-code",spreadsheet:"file-xls",text:"file-plain-text",unknown:"file-unknown",video:"file-video",writer:"file-text-editor"};function r(e){const i=/f(?:\/|%2f)(?<filetype>[\w-]+)/iu.exec(e)?.groups?.filetype.toLowerCase()??"";return o[i]??"file"}function s(e,i){return e==null||["","undefined"].includes(e)?"file-unknown":e==="resource"?r(i):l[e]??e}function c({modulename:e,iconurl:i,alt:t=""}){return f(n,{icon:s(e,i),alt:t,container:"none",size:"xl"})}export{c as ActivityIcon};
