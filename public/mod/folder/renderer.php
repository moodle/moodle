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

/**
 * Folder module renderer
 *
 * @package   mod_folder
 * @copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class mod_folder_renderer extends plugin_renderer_base {

    /**
     * Returns html to display the content of mod_folder
     * (Description, folder files and optionally Edit button)
     *
     * @param stdClass $folder record from 'folder' table (please note
     *     it may not contain fields 'revision' and 'timemodified')
     * @return string
     */
    public function display_folder(stdClass $folder) {
        static $treecounter = 0;

        $folderinstances = get_fast_modinfo($folder->course)->get_instances_of('folder');
        if (!isset($folderinstances[$folder->id]) ||
                !($cm = $folderinstances[$folder->id]) ||
                !($context = context_module::instance($cm->id))) {
            // Some error in parameters.
            // Don't throw any errors in renderer, just return empty string.
            // Capability to view module must be checked before calling renderer.
            return '';
        }

        $data = [];
        if (trim($folder->intro)) {
            if ($folder->display == FOLDER_DISPLAY_INLINE && $cm->showdescription) {
                // for "display inline" do not filter, filters run at display time.
                $data['intro'] = format_module_intro('folder', $folder, $cm->id, false);
            }
        }
        $buttons = [];
        // Display the "Edit" button if current user can edit folder contents.
        // Do not display it on the course page for the teachers because there
        // is an "Edit settings" option in the action menu with the same functionality.
        $canmanagefolderfiles = has_capability('mod/folder:managefiles', $context);
        $canmanagecourseactivities = has_capability('moodle/course:manageactivities', $context);
        if ($canmanagefolderfiles && ($folder->display != FOLDER_DISPLAY_INLINE || !$canmanagecourseactivities)) {
            $editbutton = new single_button(new moodle_url('/mod/folder/edit.php', ['id' => $cm->id]),
                get_string('edit'), 'post', single_button::BUTTON_PRIMARY);
            $editbutton->class = 'navitem';
            $data['edit_button'] = $editbutton->export_for_template($this->output);
            $data['hasbuttons'] = true;
        }

        $downloadable = folder_archive_available($folder, $cm);
        if ($downloadable) {
            $downloadbutton = new single_button(new moodle_url('/mod/folder/download_folder.php', ['id' => $cm->id]),
                get_string('downloadfolder', 'folder'), 'get');
            $downloadbutton->class = 'navitem ms-auto';
            $data['download_button'] = $downloadbutton->export_for_template($this->output);
            $data['hasbuttons'] = true;
        }

        $foldertree = new folder_tree($folder, $cm);
        if ($folder->display == FOLDER_DISPLAY_INLINE) {
            // Display module name as the name of the root directory.
            $foldertree->dir['dirname'] = $cm->get_formatted_name(array('escape' => false));
        }

        $data['id'] = 'folder_tree'. ($treecounter++);
        $data['showexpanded'] = !empty($foldertree->folder->showexpanded);
        $data['dir'] = $this->renderable_tree_elements($foldertree, ['files' => [], 'subdirs' => [$foldertree->dir]]);

        // Add isroot key to the root element.
        if (!empty($data['dir']) && isset($data['dir'][0])) {
            $data['dir'][0]['isroot'] = true;
        }

        return $this->render_from_template('mod_folder/folder', $data);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('renderable_tree_elements()', since: '4.3', mdl: 'MDL-78847', final: true)]
    protected function htmllize_tree() {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Internal function - Creates elements structure suitable for mod_folder/folder template.
     *
     * @param folder_tree $tree The folder tree to work with.
     * @param array $dir The subdir and files structure to convert into a tree.
     * @return array The structure to be rendered by mod_folder/folder template.
     */
    protected function renderable_tree_elements(folder_tree $tree, array $dir): array {
        if (empty($dir['subdirs']) && empty($dir['files'])) {
            return [];
        }
        $elements = [];
        foreach ($dir['subdirs'] as $subdir) {
            $htmllize = $this->renderable_tree_elements($tree, $subdir);
            $image = $this->output->pix_icon(file_folder_icon(), '', 'moodle');
            $elements[] = [
                'name' => $subdir['dirname'],
                'icon' => $image,
                'subdirs' => $htmllize,
                'hassubdirs' => !empty($htmllize),
                'isroot' => false,
            ];
        }
        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            $filenamedisplay = clean_filename($filename);

            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $filename, false);
            if (file_extension_in_typegroup($filename, 'web_image')) {
                $image = $url->out(false, ['preview' => 'tinyicon', 'oid' => $file->get_timemodified()]);
                $image = html_writer::empty_tag('img', ['src' => $image, 'alt' => '', 'class' => 'icon']);
            } else {
                $image = $this->output->pix_icon(file_file_icon($file), '', 'moodle');
            }

            if ($tree->folder->forcedownload) {
                $url->param('forcedownload', 1);
            }

            $elements[] = [
                'name' => $filenamedisplay,
                'icon' => $image,
                'url' => $url,
                'subdirs' => null,
                'hassubdirs' => false,
            ];
        }

        return $elements;
    }
}

class folder_tree implements renderable {
    public $context;
    public $folder;
    public $cm;
    public $dir;

    public function __construct($folder, $cm) {
        $this->folder = $folder;
        $this->cm     = $cm;

        $this->context = context_module::instance($cm->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'mod_folder', 'content', 0);
    }
}
