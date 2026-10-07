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
const fileUrl = /* @__PURE__ */ __name((relativeScript, slashArgument) => {
  let url2 = config.wwwroot + relativeScript;
  if (!slashArgument.startsWith("/")) {
    slashArgument = `/${slashArgument}`;
  }
  url2 += config.slasharguments === 0 ? `?file=${encodeURIComponent(slashArgument)}` : slashArgument;
  return url2;
}, "fileUrl");
const relativeUrl = /* @__PURE__ */ __name((relativePath, params = {}, includeSessKey = false) => {
  if (relativePath.startsWith("http:") || relativePath.startsWith("https:") || relativePath.includes("://")) {
    throw new Error("relativeUrl function does not accept absolute urls");
  }
  if (!relativePath.startsWith("/")) {
    relativePath = `/${relativePath}`;
  }
  if (config.admin !== "admin") {
    relativePath = relativePath.replace(/^\/admin\//u, () => `/${config.admin}/`);
  }
  const queryParameters = { ...params };
  if (includeSessKey) {
    queryParameters.sesskey = config.sesskey;
  }
  const entries = Object.entries(queryParameters).map(([parameter, value]) => [parameter, String(value)]);
  const queryString = new URLSearchParams(entries).toString();
  if (queryString !== "") {
    return `${config.wwwroot}${relativePath}?${queryString}`;
  }
  return config.wwwroot + relativePath;
}, "relativeUrl");
const imageUrl = /* @__PURE__ */ __name((imagename, component) => M.util.image_url(imagename, component), "imageUrl");
const url = {
  fileUrl,
  relativeUrl,
  imageUrl
};
var url_default = url;
export {
  url_default as default,
  fileUrl,
  imageUrl,
  relativeUrl
};
//# sourceMappingURL=url.dev.js.map
