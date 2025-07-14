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

namespace communication_matrix\local\spec\features\matrix;

use communication_matrix\local\command;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;

/**
 * Matrix API feature to upload content.
 *
 * https://spec.matrix.org/v1.1/client-server-api/#post_matrixmediav3upload
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait upload_content_v3 {
    /**
     * Upload the content in the matrix/synapse server.
     *
     * @param null|\stored_file $content The content to be uploaded
     * @param null|string $mediaid The mediaid to associate a file with. Supported for v1.7 API an above only.
     * @return Response
     */
    public function upload_content(
        ?\stored_file $content,
        ?string $mediaid = null,
    ): Response {
        $query = [];
        if ($content) {
            $query['filename'] = $content->get_filename();
        }

        if ($mediaid !== null) {
            // Specification of the mediaid requires version 1.7 or above of the upload API.
            // See https://spec.matrix.org/v1.7/client-server-api/#put_matrixmediav3uploadservernamemediaid.
            $this->requires_version('1.7');
            $command = new command(
                $this,
                method: 'PUT',
                endpoint: '_matrix/media/v3/upload/:mediaid',
                sendasjson: false,
                query: $query,
                params: [
                    ':mediaid' => $mediaid,
                ],
            );
        } else {
            $command = new command(
                $this,
                method: 'POST',
                endpoint: '_matrix/media/v3/upload',
                sendasjson: false,
                query: $query,
            );
        }

        if ($content) {
            // Add the content-type, and header.
            $command = $command->withHeader('Content-Type', $content->get_mimetype());
            $command = $command->withBody(Utils::streamFor($content->get_content()));
        }

        return $this->execute($command);
    }
}
