var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Swizzlable wrapper around the design system ActivityIcon.
 *
 * Themes that need a custom icon can eject this component via the swizzle
 * manifest. All other code imports from @moodle/lms/block_timeline/ActivityIcon
 * so the override applies everywhere without touching call sites.
 *
 * @module     block_timeline/ActivityIcon
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { ActivityIcon as DSActivityIcon } from "@moodlehq/design-system";
const MODULE_NAME_MAP = {
  assign: "assignment",
  bigbluebuttonbn: "bigbluebutton",
  data: "database",
  h5pactivity: "h5p",
  imscp: "ims-package",
  label: "text-and-media",
  lti: "external-tool",
  scorm: "scorm-package",
  qbank: "file-database"
};
const FILE_TYPE_MAP = {
  archive: "file-archive",
  audio: "file-audio",
  calc: "file-spreadsheet",
  chart: "file-graphic",
  database: "file-database",
  document: "file-doc",
  draw: "file-draw",
  eps: "file-eps",
  epub: "file-epub",
  flash: "file-flash",
  gif: "file-gif",
  h5p: "file-h5p",
  image: "file-image",
  impress: "file-presentation",
  isf: "file-isf-flowchart",
  json: "file-json",
  markup: "file-code",
  math: "file-math",
  moodle: "file-moodle",
  oth: "file-oth",
  pdf: "file-pdf",
  powerpoint: "file-ppt",
  psd: "file-psd",
  publisher: "file-pub",
  sourcecode: "file-source-code",
  spreadsheet: "file-xls",
  text: "file-plain-text",
  unknown: "file-unknown",
  video: "file-video",
  writer: "file-text-editor"
};
function resolveResourceIcon(iconurl) {
  const match = iconurl.match(/f(?:\/|%2f)([a-z0-9_-]+)/i);
  const filetype = match ? match[1].toLowerCase() : "";
  return FILE_TYPE_MAP[filetype] ?? "file";
}
__name(resolveResourceIcon, "resolveResourceIcon");
function resolveIcon(modulename, iconurl) {
  if (!modulename || modulename === "undefined") {
    return "file-unknown";
  }
  if (modulename === "resource") {
    return resolveResourceIcon(iconurl);
  }
  return MODULE_NAME_MAP[modulename] ?? modulename;
}
__name(resolveIcon, "resolveIcon");
function ActivityIcon({ modulename, iconurl, alt = "" }) {
  return /* @__PURE__ */ jsxDEV(DSActivityIcon, { icon: resolveIcon(modulename, iconurl), alt, container: "none", size: "xl" }, void 0, false, {
    fileName: "public/blocks/timeline/js/esm/src/ActivityIcon.tsx",
    lineNumber: 119,
    columnNumber: 12
  }, this);
}
__name(ActivityIcon, "ActivityIcon");
export {
  ActivityIcon
};
//# sourceMappingURL=ActivityIcon.dev.js.map
