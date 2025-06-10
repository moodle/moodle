<?php
// This file is part of Moodle - https://moodle.org/
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
 * Definitions of constants for tool_crawler
 *
 * @package   tool_crawler
 * @copyright 2019 Nicolas Roeser, Ulm University <nicolas.roeser@uni-ulm.de>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The crawler has determined the exact size of the requested resource. This value does not indicate that the crawler has indeed
 * fully downloaded the resource; it may also rely on header fields that it has seen.
 *
 * Value for the `filesizestatus` column in the database table.
 */
define('TOOL_CRAWLER_FILESIZE_EXACT', 0);

/**
 * The crawler has detected that the requested resource is _at least_ the size stored in the `filesize` column in the database
 * table. This value is normally used when the download has been aborted, but some data has already been received.
 *
 * Value for the `filesizestatus` column in the database table.
 */
define('TOOL_CRAWLER_FILESIZE_ATLEAST', 1);

/**
 * The crawler has tried to, but has been unable to detect the size of the resource, and can not give a minimum size. This can
 * happen if a redirection is followed, but the final header is not processed at all: for example, if an overlong header is
 * encountered and the crawler has decided to abort the download, then the final header will not be seen.
 *
 * This value is to be distinguished from NULL, which indicates that the crawler has made _no attempt_ to find out about the size of
 * the requested resource. So NULL says that there is no information about the meaning of the value in the `filesize` column in the
 * database table (if that is non-NULL at all).
 *
 * Value for the `filesizestatus` column in the database table.
 */
define('TOOL_CRAWLER_FILESIZE_UNKNOWN', 2);

/**
 * Do not consume more networking resources than necessary to retrieve information about the size of most linked resources. Abort
 * the download of external HTML documents after a (quite big) initial part, which is usually large enough to extract the document
 * title. For non-HTML documents, rely on the HTTP `Content-Length` header if present; and if not, report the size as unknown.
 *
 * Value for the `networkstrain` configuration setting.
 */
define('TOOL_CRAWLER_NETWORKSTRAIN_REASONABLE', 'reasonable');

/**
 * Invest a significant amount of networking resources when attempting to detect the sizes of linked external documents. This is
 * done by downloading external documents up to the configured big file size limit, but only if their length is not known from the
 * HTTP `Content-Length` header.
 *
 * Value for the `networkstrain` configuration setting.
 */
define('TOOL_CRAWLER_NETWORKSTRAIN_RESOLUTE', 'resolute');

/**
 * Consume a giant amount of networking resources when following links in order to find the exact target resource sizes. If their
 * size is not previously known from the HTTP `Content-Length` header, fully download non-HTML documents and external HTML
 * documents, in order to determine it. This also means that there is enough of the document text available so that the document
 * title can be extracted from each and every HTML document (if it has one and is properly formatted).
 *
 * Value for the `networkstrain` configuration setting.
 */
define('TOOL_CRAWLER_NETWORKSTRAIN_EXCESSIVE', 'excessive');

/**
 * Liberally waste networking resources when scraping links. Act like for `TOOL_CRAWLER_NETWORKSTRAIN_EXCESSIVE`; but in addition to
 * that always fully download all HTML documents.
 *
 * Value for the `networkstrain` configuration setting.
 */
define('TOOL_CRAWLER_NETWORKSTRAIN_WASTEFUL', 'wasteful');

/**
 * Priority levels for queue items. By default they have a value of 0.
 */
define('TOOL_CRAWLER_PRIORITY_DEFAULT', 0);
define('TOOL_CRAWLER_PRIORITY_NORMAL', 50);
define('TOOL_CRAWLER_PRIORITY_HIGH', 100);

/**
 * Node level assigned to each node based on whether it is the parent node, or
 * a child node discovered within a parent when crawling, or any child of a child
 * node (or even further removed).
 */
define('TOOL_CRAWLER_NODE_LEVEL_PARENT', 2);
define('TOOL_CRAWLER_NODE_LEVEL_DIRECT_CHILD', 1);
define('TOOL_CRAWLER_NODE_LEVEL_INDIRECT_CHILD', 0);
