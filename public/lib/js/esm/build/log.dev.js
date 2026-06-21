var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * A logging module providing level-filtered console output.
 *
 * Each log method accepts an optional `source` parameter which, when provided,
 * prefixes the message with `"source: message"` for easier filtering.
 *
 * @module     core/log
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
const levels = {
  TRACE: 0,
  DEBUG: 1,
  INFO: 2,
  WARN: 3,
  ERROR: 4,
  SILENT: 5
};
const consoleMethods = {
  [levels.TRACE]: "trace",
  [levels.DEBUG]: "debug",
  [levels.INFO]: "info",
  [levels.WARN]: "warn",
  [levels.ERROR]: "error"
};
let currentLevel = levels.WARN;
let defaultLevel = levels.WARN;
function resolveLevel(level) {
  if (typeof level === "string") {
    const upper = level.toUpperCase();
    if (upper in levels) {
      return levels[upper];
    }
    return levels.WARN;
  }
  return level;
}
__name(resolveLevel, "resolveLevel");
function formatMessage(message, source) {
  const msg = String(message);
  return source ? `${source}: ${msg}` : msg;
}
__name(formatMessage, "formatMessage");
function logAtLevel(level, message, source) {
  if (level < currentLevel) {
    return;
  }
  const method = consoleMethods[level];
  if (method) {
    console[method](formatMessage(message, source));
  }
}
__name(logAtLevel, "logAtLevel");
function setLevel(level) {
  currentLevel = resolveLevel(level);
}
__name(setLevel, "setLevel");
function getLevel() {
  return currentLevel;
}
__name(getLevel, "getLevel");
function setDefaultLevel(level) {
  defaultLevel = resolveLevel(level);
}
__name(setDefaultLevel, "setDefaultLevel");
function resetLevel() {
  currentLevel = defaultLevel;
}
__name(resetLevel, "resetLevel");
function enableAll() {
  currentLevel = levels.TRACE;
}
__name(enableAll, "enableAll");
function disableAll() {
  currentLevel = levels.SILENT;
}
__name(disableAll, "disableAll");
function setConfig(config) {
  if (typeof config.level !== "undefined") {
    setLevel(config.level);
  }
}
__name(setConfig, "setConfig");
function trace(message, source) {
  logAtLevel(levels.TRACE, message, source);
}
__name(trace, "trace");
function debug(message, source) {
  logAtLevel(levels.DEBUG, message, source);
}
__name(debug, "debug");
function info(message, source) {
  logAtLevel(levels.INFO, message, source);
}
__name(info, "info");
function warn(message, source) {
  logAtLevel(levels.WARN, message, source);
}
__name(warn, "warn");
function error(message, source) {
  logAtLevel(levels.ERROR, message, source);
}
__name(error, "error");
const log = {
  levels,
  trace,
  debug,
  info,
  warn,
  error,
  /** Alias for {@link debug}. */
  log: debug,
  setLevel,
  getLevel,
  setDefaultLevel,
  resetLevel,
  enableAll,
  disableAll,
  setConfig
};
var log_default = log;
export {
  log_default as default,
  levels
};
//# sourceMappingURL=log.dev.js.map
