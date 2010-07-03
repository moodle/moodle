<?php

defined('MOODLE_INTERNAL') || die();

class mod_folder_renderer extends plugin_renderer_base {

    /**
     * Prints file folder tree view
     * @param object $folder instance
     * @param object $cm instance
     * @param object $course
     * @return void
     */
    public function folder_tree($folder, $cm, $course) {
        $this->render(new folder_tree($folder, $cm, $course));
    }

    public function render_folder_tree(folder_tree $tree) {
        global $PAGE;

        echo '<div id="folder_tree">';
        echo $this->htmllize_tree($tree, $tree->dir);
        echo '</div>';
        $this->page->requires->js_init_call('M.mod_folder.init_tree', array(true));
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $result .= '<li>'.s($subdir['dirname']).' '.$this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/mod_folder/content/'.$tree->folder->revision.$file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $result .= '<li><span>'.html_writer::link($url, $filename).'</span></li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

class folder_tree implements renderable {
    public $context;
    public $folder;
    public $cm;
    public $course;
    public $dir;

    public function __construct($folder, $cm, $course) {
        $this->folder = $folder;
        $this->cm     = $cm;
        $this->course = $course;

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'mod_folder', 'content', 0);
    }
}