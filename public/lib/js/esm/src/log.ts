// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

/** Numeric log levels. */
export const levels = {
    TRACE: 0,
    DEBUG: 1,
    INFO: 2,
    WARN: 3,
    ERROR: 4,
    SILENT: 5,
} as const;

export type LogLevelName = keyof typeof levels;
export type LogLevel = typeof levels[LogLevelName];

/** The console method that corresponds to each non-SILENT level. */
const consoleMethods: Record<number, 'trace' | 'debug' | 'info' | 'warn' | 'error'> = {
    [levels.TRACE]: 'trace',
    [levels.DEBUG]: 'debug',
    [levels.INFO]: 'info',
    [levels.WARN]: 'warn',
    [levels.ERROR]: 'error',
};

let currentLevel: LogLevel = levels.WARN;
let defaultLevel: LogLevel = levels.WARN;

/**
 * Resolve a level value from a numeric level or string name.
 */
function resolveLevel(level: LogLevel | LogLevelName): LogLevel {
    if (typeof level === 'string') {
        const upper = level.toUpperCase() as LogLevelName;
        if (upper in levels) {
            return levels[upper];
        }
        return levels.WARN;
    }
    return level;
}

/**
 * Format a message with an optional source prefix.
 */
function formatMessage(message: unknown, source?: string): string {
    const msg = String(message);
    return source ? `${source}: ${msg}` : msg;
}

/**
 * Log at a given level.
 */
function logAtLevel(level: LogLevel, message: unknown, source?: string): void {
    if (level < currentLevel) {
        return;
    }
    const method = consoleMethods[level];
    if (method) {
        // eslint-disable-next-line no-console
        console[method](formatMessage(message, source));
    }
}

/**
 * Set the current log level.
 *
 * @param level A numeric level (0–5) or string name ('TRACE', 'DEBUG', 'INFO', 'WARN', 'ERROR', 'SILENT').
 */
function setLevel(level: LogLevel | LogLevelName): void {
    currentLevel = resolveLevel(level);
}

/**
 * Get the current numeric log level.
 */
function getLevel(): LogLevel {
    return currentLevel;
}

/**
 * Set the default log level used by {@link resetLevel}.
 */
function setDefaultLevel(level: LogLevel | LogLevelName): void {
    defaultLevel = resolveLevel(level);
}

/**
 * Reset the current level to the default.
 */
function resetLevel(): void {
    currentLevel = defaultLevel;
}

/**
 * Enable all logging (set level to TRACE).
 */
function enableAll(): void {
    currentLevel = levels.TRACE;
}

/**
 * Disable all logging (set level to SILENT).
 */
function disableAll(): void {
    currentLevel = levels.SILENT;
}

/**
 * Configure the logger from a config object.
 *
 * @param config An object with an optional `level` property.
 */
function setConfig(config: { level?: LogLevel | LogLevelName }): void {
    if (typeof config.level !== 'undefined') {
        setLevel(config.level);
    }
}

/** Log a trace message. */
function trace(message: unknown, source?: string): void {
    logAtLevel(levels.TRACE, message, source);
}

/** Log a debug message. */
function debug(message: unknown, source?: string): void {
    logAtLevel(levels.DEBUG, message, source);
}

/** Log an info message. */
function info(message: unknown, source?: string): void {
    logAtLevel(levels.INFO, message, source);
}

/** Log a warning message. */
function warn(message: unknown, source?: string): void {
    logAtLevel(levels.WARN, message, source);
}

/** Log an error message. */
function error(message: unknown, source?: string): void {
    logAtLevel(levels.ERROR, message, source);
}

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
    setConfig,
};

export default log;
