<?php
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

namespace core\route\controller;

use core\router\schema\parameters\path_parameter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for serving ES module files.
 *
 * Resolves all ESM requests under /esm/{revision}/{scriptpath} by delegating to the
 * import_map registry, which is the single source of truth for specifier → file mappings.
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class esm_controller {
    use \core\router\route_controller;

    #[\core\router\route(
        title: 'Serve ESM Content',
        path: '/esm/{revision:[0-9-]+}/{scriptpath:.*}',
        pathtypes: [
            new path_parameter(
                name: 'revision',
                description: 'The revision number of the script to serve.',
                type: \core\param::INT,
            ),
            new path_parameter(
                name: 'scriptpath',
                description: 'The path to the script to serve.',
                type: \core\param::ESM_PATH,
            ),
        ],
        method: ['GET'],
        abortafterconfig: true,
    )]
    /**
     * Serve an ES module file by resolving the specifier via the import map.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $revision
     * @param string $scriptpath
     */
    public function serve(
        ServerRequestInterface $request,
        ResponseInterface $response,
        int $revision,
        string $scriptpath,
    ): ResponseInterface {
        // Normalise the revision: an outdated or invalid value disables long-term caching
        // so browsers always re-fetch rather than serving a stale file.
        if (!min_is_revision_valid_and_current($revision)) {
            $revision = -1;
        }

        $importmap = \core\di::get(\core\output\requirements\import_map::class);
        $fullpath = $importmap->get_path_for_script($scriptpath);
        if ($fullpath !== null && file_exists($fullpath)) {
            return $this->serve_script($request, $response, $revision, $fullpath, basename($fullpath));
        }
        throw new \core\exception\not_found_exception('script', $scriptpath);
    }

    /**
     * Write the file content into the response with appropriate cache headers.
     *
     * When $revision is -1 (invalid/development), short-lived cache headers are used.
     * Otherwise, immutable long-lived cache headers are set with an ETag, and a
     * 304 Not Modified response is returned if the client already has the file cached.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $revision The JS revision number; -1 disables long-term caching.
     * @param string $file Absolute filesystem path to the JS file.
     * @param string $presentedfilename Filename to use in Content-Disposition.
     */
    protected function serve_script(
        ServerRequestInterface $request,
        ResponseInterface $response,
        int $revision,
        string $file,
        string $presentedfilename,
    ): ResponseInterface {
        $now = \core\di::get(\core\clock::class)->time();

        if ($revision === -1) {
            $response = $response
                ->withHeader('Content-Type', 'application/javascript; charset=utf-8')
                ->withHeader('Content-Disposition', "inline; filename=\"{$presentedfilename}\"")
                ->withHeader('Last-Modified', gmdate('D, d M Y H:i:s', $now) . ' GMT')
                ->withHeader('Expires', gmdate('D, d M Y H:i:s', $now + 2) . ' GMT')
                ->withHeader('Pragma', '')
                ->withHeader('Accept-Ranges', 'none');
        } else {
            $etag = sha1($revision . ':' . $file);

            if ($request->hasHeader('If-None-Match') && in_array($etag, $request->getHeader('If-None-Match'))) {
                return $response->withStatus(304);
            }

            $response = $response
                ->withHeader('Content-Type', 'application/javascript; charset=utf-8')
                ->withHeader('ETag', $etag)
                ->withHeader('Content-Disposition', 'inline; filename="' . basename($file) . '"')
                ->withHeader('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT')
                ->withHeader('Expires', gmdate('D, d M Y H:i:s', $now + 31536000) . ' GMT')
                ->withHeader('Pragma', '')
                ->withHeader('Cache-Control', 'public, max-age=31536000, immutable')
                ->withHeader('Accept-Ranges', 'none');
        }

        $response->getBody()->write(file_get_contents($file));
        return $response;
    }
}
