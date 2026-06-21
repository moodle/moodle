var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * URL utility functions.
 *
 * @module     core/url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
import config from "./config";
const fileUrl = /* @__PURE__ */ __name((relativeScript, slashArg) => {
  let url = config.wwwroot + relativeScript;
  if (slashArg.charAt(0) !== "/") {
    slashArg = `/${slashArg}`;
  }
  if (config.slasharguments) {
    url += slashArg;
  } else {
    url += `?file=${encodeURIComponent(slashArg)}`;
  }
  return url;
}, "fileUrl");
const relativeUrl = /* @__PURE__ */ __name((relativePath, params = {}, includeSessKey = false) => {
  if (relativePath.indexOf("http:") === 0 || relativePath.indexOf("https:") === 0 || relativePath.indexOf("://") >= 0) {
    throw new Error("relativeUrl function does not accept absolute urls");
  }
  if (relativePath.charAt(0) !== "/") {
    relativePath = `/${relativePath}`;
  }
  if (config.admin !== "admin") {
    relativePath = relativePath.replace(/^\/admin\//, `/${config.admin}/`);
  }
  const queryParams = { ...params };
  if (includeSessKey) {
    queryParams.sesskey = config.sesskey;
  }
  const queryString = new URLSearchParams(
    Object.entries(queryParams).map(([param, value]) => [param, String(value)])
  ).toString();
  if (queryString !== "") {
    return `${config.wwwroot}${relativePath}?${queryString}`;
  }
  return config.wwwroot + relativePath;
}, "relativeUrl");
const imageUrl = /* @__PURE__ */ __name((imagename, component) => M.util.image_url(imagename, component), "imageUrl");
var url_default = {
  fileUrl,
  relativeUrl,
  imageUrl
};
export {
  url_default as default,
  fileUrl,
  imageUrl,
  relativeUrl
};
//# sourceMappingURL=url.dev.js.map
