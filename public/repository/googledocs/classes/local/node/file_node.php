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

namespace repository_googledocs\local\node;

/**
 * Class used to represent a file node in the googledocs repository.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_node implements node {

    /** @var string The ID of the file node. */
    private $id;

    /** @var string|null The title of the file node. */
    private $title;

    /** @var string The name of the file. */
    private $name;

    /** @var string|null The file's export format. */
    private $exportformat;

    /** @var string The external link to the file. */
    private $link;

    /** @var string The timestamp representing the last modified date. */
    private $modified;

    /** @var string|null The size of the file. */
    private $size;

    /** @var string The thumbnail of the file. */
    private $thumbnail;

    /** @var string|null The type of the Google Doc file (document, presentation, etc.), null if it is a regular file. */
    private $googledoctype;

    /**
     * Constructor.
     *
     * @param \stdClass $gdfile The Google Drive file object
     */
    public function __construct(\stdClass $gdfile) {
        $this->id = $gdfile->id;
        $this->title = $this->generate_file_title($gdfile);
        $this->name = $gdfile->name;
        $this->exportformat = $this->generate_file_export_format($gdfile);
        $this->link = $this->generate_file_link($gdfile);
        $this->modified = ($gdfile->modifiedTime) ? strtotime($gdfile->modifiedTime) : '';
        $this->size = !empty($gdfile->size) ? $gdfile->size : null;
        // Use iconLink as a file thumbnail if set, otherwise use the default icon depending on the file type.
        // Note: The Google Drive API can return a link to a preview thumbnail of the file (via thumbnailLink).
        // However, in many cases the Google Drive files are not public and an authorized request is required
        // to get the thumbnail which we currently do not support. Therefore, to avoid displaying broken
        // thumbnail images in the repository, the icon of the Google Drive file is being used as a thumbnail
        // instead as it does not require an authorized request.
        // Currently, the returned file icon link points to the 16px version of the icon by default which would result
        // in displaying 16px file thumbnails in the repository. To avoid this, the link can be slightly modified in
        // order to get a larger version of the icon as there isn't an option to request this through the API call.
        $this->thumbnail = !empty($gdfile->iconLink) ? str_replace('/16/', '/64/', $gdfile->iconLink) : '';
        $this->googledoctype = !isset($gdfile->fileExtension) ?
            str_replace('application/vnd.google-apps.', '', $gdfile->mimeType) : null;
    }

    /**
     * Create a repository file array.
     *
     * This method returns an array which structure is compatible to represent a file node in the repository.
     *
     * @return array|null The node array or null if the node could not be created
     */
    public function create_node_array(): ?array {
        // Cannot create the file node if the file title was not generated or the export format.
        // This means that the current file type is invalid or unknown.
        if (!$this->title || !$this->exportformat) {
            return null;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'source' => json_encode(
                [
                    'id' => $this->id,
                    'name' => $this->name,
                    'link' => $this->link,
                    'exportformat' => $this->exportformat,
                    'googledoctype' => $this->googledoctype,
                ]
            ),
            'date' => $this->modified,
            'size' => $this->size,
            'thumbnail' => $this->thumbnail,
            'thumbnail_height' => 64,
            'thumbnail_width' => 64,
        ];
    }

    /**
     * Generates and returns the title for the file node depending on the type of the Google drive file.
     *
     * @param \stdClass $gdfile The Google Drive file object
     * @return string The file title
     */
    private function generate_file_title(\stdClass $gdfile): ?string {
        // Determine the file type through the file extension.
        if (isset($gdfile->fileExtension)) { // The file is a regular file.
            return $gdfile->name;
        } else { // The file is probably a Google Doc file.
            // We need to generate the name by appending the proper google doc extension.
            $type = str_replace('application/vnd.google-apps.', '', $gdfile->mimeType);

            if ($type === 'document') {
                return "{$gdfile->name}.gdoc";
            }
            if ($type === 'presentation') {
                return "{$gdfile->name}.gslides";
            }
            if ($type === 'spreadsheet') {
                return "{$gdfile->name}.gsheet";
            }
            if ($type === 'drawing') {
                $config = get_config('googledocs');
                $ext = $config->drawingformat;
                return "{$gdfile->name}.{$ext}";
            }
        }

        return null;
    }

    /**
     * Generates and returns the file export format depending on the type of the Google drive file.
     *
     * @param \stdClass $gdfile The Google Drive file object
     * @return string The file export format
     */
    private function generate_file_export_format(\stdClass $gdfile): ?string {
        // Determine the file type through the file extension.
        if (isset($gdfile->fileExtension)) { // The file is a regular file.
            // The file has an extension, therefore we can download it.
            return 'download';
        } else {
            // The file is probably a Google Doc file, we get the corresponding export link.
            $type = str_replace('application/vnd.google-apps.', '', $gdfile->mimeType);
            $types = get_mimetypes_array();
            $config = get_config('googledocs');

            if ($type === 'document' && !empty($config->documentformat)) {
                $ext = $config->documentformat;
                if ($ext === 'rtf') {
                    // Moodle user 'text/rtf' as the MIME type for RTF files.
                    // Google uses 'application/rtf' for the same type of file.
                    // See https://developers.google.com/drive/v3/web/manage-downloads.
                    return 'application/rtf';
                } else {
                    return $types[$ext]['type'];
                }
            }
            if ($type === 'presentation' && !empty($config->presentationformat)) {
                $ext = $config->presentationformat;
                return $types[$ext]['type'];
            }
            if ($type === 'spreadsheet' && !empty($config->spreadsheetformat)) {
                $ext = $config->spreadsheetformat;
                return $types[$ext]['type'];
            }
            if ($type === 'drawing' && !empty($config->drawingformat)) {
                $ext = $config->drawingformat;
                return $types[$ext]['type'];
            }
        }

        return null;
    }

    /**
     * Generates and returns the external link to the file.
     *
     * @param \stdClass $gdfile The Google Drive file object
     * @return string The link to the file
     */
    private function generate_file_link(\stdClass $gdfile): string {
        // If the google drive file has webViewLink set, use it as an external link.
        $link = !empty($gdfile->webViewLink) ? $gdfile->webViewLink : '';
        // Otherwise, use webContentLink if set or leave the external link empty.
        if (empty($link) && !empty($gdfile->webContentLink)) {
            $link = $gdfile->webContentLink;
        }

        return $link;
    }
}
