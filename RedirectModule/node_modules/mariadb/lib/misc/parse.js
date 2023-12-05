//  SPDX-License-Identifier: LGPL-2.1-or-later
//  Copyright (c) 2015-2023 MariaDB Corporation Ab

const Errors = require('../misc/errors');

const State = {
  Normal: 1 /* inside  query */,
  String: 2 /* inside string */,
  SlashStarComment: 3 /* inside slash-star comment */,
  Escape: 4 /* found backslash */,
  EOLComment: 5 /* # comment, or // comment, or -- comment */,
  Backtick: 6 /* found backtick */,
  Placeholder: 7 /* found placeholder */
};

const SLASH_BYTE = '/'.charCodeAt(0);
const STAR_BYTE = '*'.charCodeAt(0);
const BACKSLASH_BYTE = '\\'.charCodeAt(0);
const HASH_BYTE = '#'.charCodeAt(0);
const MINUS_BYTE = '-'.charCodeAt(0);
const LINE_FEED_BYTE = '\n'.charCodeAt(0);
const DBL_QUOTE_BYTE = '"'.charCodeAt(0);
const QUOTE_BYTE = "'".charCodeAt(0);
const RADICAL_BYTE = '`'.charCodeAt(0);
const QUESTION_MARK_BYTE = '?'.charCodeAt(0);
const COLON_BYTE = ':'.charCodeAt(0);
const SEMICOLON_BYTE = ';'.charCodeAt(0);

/**
 * Set question mark position (question mark).
 * Question mark in comment are not taken in account
 *
 * @returns {Array} question mark position
 */
module.exports.splitQuery = function (query) {
  let paramPositions = [];
  let state = State.Normal;
  let lastChar = 0x00;
  let singleQuotes = false;

  const len = query.length;
  for (let i = 0; i < len; i++) {
    if (
      state === State.Escape &&
      !((query[i] === QUOTE_BYTE && singleQuotes) || (query[i] === DBL_QUOTE_BYTE && !singleQuotes))
    ) {
      state = State.String;
      lastChar = query[i];
      continue;
    }
    switch (query[i]) {
      case STAR_BYTE:
        if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.SlashStarComment;
        }
        break;

      case SLASH_BYTE:
        if (state === State.SlashStarComment && lastChar === STAR_BYTE) {
          state = State.Normal;
        } else if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.EOLComment;
        }
        break;

      case HASH_BYTE:
        if (state === State.Normal) {
          state = State.EOLComment;
        }
        break;

      case MINUS_BYTE:
        if (state === State.Normal && lastChar === MINUS_BYTE) {
          state = State.EOLComment;
        }
        break;

      case LINE_FEED_BYTE:
        if (state === State.EOLComment) {
          state = State.Normal;
        }
        break;

      case DBL_QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = false;
        } else if (state === State.String && !singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = true;
        } else if (state === State.String && singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case BACKSLASH_BYTE:
        if (state === State.String) {
          state = State.Escape;
        }
        break;
      case QUESTION_MARK_BYTE:
        if (state === State.Normal) {
          paramPositions.push(i, ++i);
        }
        break;
      case RADICAL_BYTE:
        if (state === State.Backtick) {
          state = State.Normal;
        } else if (state === State.Normal) {
          state = State.Backtick;
        }
        break;
    }
    lastChar = query[i];
  }
  return paramPositions;
};

/**
 * Split query according to parameters using placeholder.
 *
 * @param query           query bytes
 * @param info            connection information
 * @param initialValues   placeholder object
 * @param displaySql      display sql function
 * @returns {{paramPositions: Array, values: Array}}
 */
module.exports.splitQueryPlaceholder = function (query, info, initialValues, displaySql) {
  let paramPositions = [];
  let values = [];
  let state = State.Normal;
  let lastChar = 0x00;
  let singleQuotes = false;
  let car;

  const len = query.length;
  for (let i = 0; i < len; i++) {
    car = query[i];
    if (
      state === State.Escape &&
      !((car === QUOTE_BYTE && singleQuotes) || (car === DBL_QUOTE_BYTE && !singleQuotes))
    ) {
      state = State.String;
      lastChar = car;
      continue;
    }
    switch (car) {
      case STAR_BYTE:
        if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.SlashStarComment;
        }
        break;

      case SLASH_BYTE:
        if (state === State.SlashStarComment && lastChar === STAR_BYTE) {
          state = State.Normal;
        } else if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.EOLComment;
        }
        break;

      case HASH_BYTE:
        if (state === State.Normal) {
          state = State.EOLComment;
        }
        break;

      case MINUS_BYTE:
        if (state === State.Normal && lastChar === MINUS_BYTE) {
          state = State.EOLComment;
        }
        break;

      case LINE_FEED_BYTE:
        if (state === State.EOLComment) {
          state = State.Normal;
        }
        break;

      case DBL_QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = false;
        } else if (state === State.String && !singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = true;
        } else if (state === State.String && singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case BACKSLASH_BYTE:
        if (state === State.String) {
          state = State.Escape;
        }
        break;
      case QUESTION_MARK_BYTE:
        if (state === State.Normal) {
          paramPositions.push(i);
          paramPositions.push(++i);
        }
        break;
      case COLON_BYTE:
        if (state === State.Normal) {
          let j = 1;

          while (
            (i + j < len && query[i + j] >= '0'.charCodeAt(0) && query[i + j] <= '9'.charCodeAt(0)) ||
            (query[i + j] >= 'A'.charCodeAt(0) && query[i + j] <= 'Z'.charCodeAt(0)) ||
            (query[i + j] >= 'a'.charCodeAt(0) && query[i + j] <= 'z'.charCodeAt(0)) ||
            query[i + j] === '-'.charCodeAt(0) ||
            query[i + j] === '_'.charCodeAt(0)
          ) {
            j++;
          }

          paramPositions.push(i, i + j);

          const placeholderName = query.toString('utf8', i + 1, i + j);
          i += j;

          const val = initialValues[placeholderName];
          if (val === undefined) {
            throw Errors.createError(
              `Placeholder '${placeholderName}' is not defined`,
              Errors.ER_PLACEHOLDER_UNDEFINED,
              info,
              'HY000',
              displaySql.call()
            );
          }
          values.push(val);
        }
        break;
      case RADICAL_BYTE:
        if (state === State.Backtick) {
          state = State.Normal;
        } else if (state === State.Normal) {
          state = State.Backtick;
        }
        break;
    }
    lastChar = car;
  }
  return { paramPositions: paramPositions, values: values };
};

module.exports.searchPlaceholder = function (sql) {
  let sqlPlaceHolder = '';
  let placeHolderIndex = [];
  let state = State.Normal;
  let lastChar = '\0';

  let singleQuotes = false;
  let lastParameterPosition = 0;

  let idx = 0;
  let car = sql.charAt(idx++);
  let placeholderName;

  while (car !== '') {
    if (state === State.Escape && !((car === "'" && singleQuotes) || (car === '"' && !singleQuotes))) {
      state = State.String;
      lastChar = car;
      car = sql.charAt(idx++);
      continue;
    }

    switch (car) {
      case '*':
        if (state === State.Normal && lastChar === '/') state = State.SlashStarComment;
        break;

      case '/':
        if (state === State.SlashStarComment && lastChar === '*') state = State.Normal;
        break;

      case '#':
        if (state === State.Normal) state = State.EOLComment;
        break;

      case '-':
        if (state === State.Normal && lastChar === '-') {
          state = State.EOLComment;
        }
        break;

      case '\n':
        if (state === State.EOLComment) {
          state = State.Normal;
        }
        break;

      case '"':
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = false;
        } else if (state === State.String && !singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape && !singleQuotes) {
          state = State.String;
        }
        break;

      case "'":
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = true;
        } else if (state === State.String && singleQuotes) {
          state = State.Normal;
          singleQuotes = false;
        } else if (state === State.Escape && singleQuotes) {
          state = State.String;
        }
        break;

      case '\\':
        if (state === State.String) state = State.Escape;
        break;

      case ':':
        if (state === State.Normal) {
          sqlPlaceHolder += sql.substring(lastParameterPosition, idx - 1) + '?';
          placeholderName = '';
          while (
            ((car = sql.charAt(idx++)) !== '' && car >= '0' && car <= '9') ||
            (car >= 'A' && car <= 'Z') ||
            (car >= 'a' && car <= 'z') ||
            car === '-' ||
            car === '_'
          ) {
            placeholderName += car;
          }
          idx--;
          placeHolderIndex.push(placeholderName);
          lastParameterPosition = idx;
        }
        break;
      case '`':
        if (state === State.Backtick) {
          state = State.Normal;
        } else if (state === State.Normal) {
          state = State.Backtick;
        }
    }
    lastChar = car;

    car = sql.charAt(idx++);
  }
  if (lastParameterPosition === 0) {
    sqlPlaceHolder = sql;
  } else {
    sqlPlaceHolder += sql.substring(lastParameterPosition);
  }

  return { sql: sqlPlaceHolder, placeHolderIndex: placeHolderIndex };
};

/**
 * Ensure that filename requested by server corresponds to query
 * protocol : https://mariadb.com/kb/en/library/local_infile-packet/
 *
 * @param sql         query
 * @param parameters  parameters if any
 * @param fileName    server requested file
 * @returns {boolean} is filename corresponding to query
 */
module.exports.validateFileName = function (sql, parameters, fileName) {
  // in case of windows, file name in query are escaped
  // so for example LOAD DATA LOCAL INFILE 'C:\\Temp\\myFile.txt' ...
  // but server return 'C:\Temp\myFile.txt'
  // so with regex escaped, must test LOAD DATA LOCAL INFILE 'C:\\\\Temp\\\\myFile.txt'
  let queryValidator = new RegExp(
    "^(\\s*\\/\\*([^\\*]|\\*[^\\/])*\\*\\/)*\\s*LOAD\\s+DATA\\s+((LOW_PRIORITY|CONCURRENT)\\s+)?LOCAL\\s+INFILE\\s+'" +
      fileName.replace(/\\/g, '\\\\\\\\').replace('.', '\\.') +
      "'",
    'i'
  );
  if (queryValidator.test(sql)) return true;

  if (parameters != null) {
    queryValidator = new RegExp(
      '^(\\s*\\/\\*([^\\*]|\\*[^\\/])*\\*\\/)*\\s*LOAD\\s+DATA\\s+((LOW_PRIORITY|CONCURRENT)\\s+)?LOCAL\\s+INFILE\\s+\\?',
      'i'
    );
    if (queryValidator.test(sql) && parameters.length > 0) {
      if (Array.isArray(parameters)) {
        return parameters[0].toLowerCase() === fileName.toLowerCase();
      }
      return parameters.toLowerCase() === fileName.toLowerCase();
    }
  }
  return false;
};

/**
 * Parse commands from buffer, returns queries separated by ';'
 * (last one is not parsed)
 *
 * @param bufState buffer
 * @returns {*[]} array of queries contained in buffer
 */
module.exports.parseQueries = function (bufState) {
  let state = State.Normal;
  let lastChar = 0x00;
  let currByte;
  let queries = [];
  let singleQuotes = false;

  for (let i = bufState.offset; i < bufState.end; i++) {
    currByte = bufState.buffer[i];
    if (
      state === State.Escape &&
      !((currByte === QUOTE_BYTE && singleQuotes) || (currByte === DBL_QUOTE_BYTE && !singleQuotes))
    ) {
      state = State.String;
      lastChar = currByte;
      continue;
    }
    switch (currByte) {
      case STAR_BYTE:
        if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.SlashStarComment;
        }
        break;

      case SLASH_BYTE:
        if (state === State.SlashStarComment && lastChar === STAR_BYTE) {
          state = State.Normal;
        } else if (state === State.Normal && lastChar === SLASH_BYTE) {
          state = State.EOLComment;
        }
        break;

      case HASH_BYTE:
        if (state === State.Normal) {
          state = State.EOLComment;
        }
        break;

      case MINUS_BYTE:
        if (state === State.Normal && lastChar === MINUS_BYTE) {
          state = State.EOLComment;
        }
        break;

      case LINE_FEED_BYTE:
        if (state === State.EOLComment) {
          state = State.Normal;
        }
        break;

      case DBL_QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = false;
        } else if (state === State.String && !singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case QUOTE_BYTE:
        if (state === State.Normal) {
          state = State.String;
          singleQuotes = true;
        } else if (state === State.String && singleQuotes) {
          state = State.Normal;
        } else if (state === State.Escape) {
          state = State.String;
        }
        break;

      case BACKSLASH_BYTE:
        if (state === State.String) {
          state = State.Escape;
        }
        break;
      case SEMICOLON_BYTE:
        if (state === State.Normal) {
          queries.push(bufState.buffer.toString('utf8', bufState.offset, i));
          bufState.offset = i + 1;
        }
        break;
      case RADICAL_BYTE:
        if (state === State.Backtick) {
          state = State.Normal;
        } else if (state === State.Normal) {
          state = State.Backtick;
        }
        break;
    }
    lastChar = currByte;
  }
  return queries;
};
