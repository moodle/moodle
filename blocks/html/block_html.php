<?php

class block_html extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_html');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('newhtmlblock', 'block_html'));
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($this->content_is_trusted()) {
            // fancy html allowed only on course, category and system blocks.
            $filteropt = new stdClass;
            $filteropt->noclean = true;
        } else {
            $filteropt = null;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        if (isset($this->config->text)) {
            // rewrite url
            $this->config->text['text'] = file_rewrite_pluginfile_urls($this->config->text['text'], 'pluginfile.php', $this->context->id, 'block_html', $this->instance->id);
            $this->content->text = format_text($this->config->text['text'], $this->config->text['format'], $filteropt);
        } else {
            $this->content->text = '';
        }

        unset($filteropt); // memory footprint

        return $this->content;
    }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB;

        // Move embedded files into a proper filearea and adjust HTML links to match
        $data->text['text'] = file_save_draft_area_files($data->text['itemid'], $this->context->id, 'block_html', $this->instance->id, array('subdirs'=>true), $data->text['text']);

        parent::instance_config_save($data, $nolongerused);
    }

    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_html', $this->instance->id);
        return true;
    }

    function content_is_trusted() {
        return in_array($this->page->context->contextlevel, array(CONTEXT_COURSE, CONTEXT_COURSECAT, CONTEXT_SYSTEM));
    }

    /**
     * Will be called before an instance of this block is backed up, so that any links in
     * any links in any HTML fields on config can be encoded.
     * @return string
     */
    function get_backup_encoded_config() {
        /// Prevent clone for non configured block instance. Delegate to parent as fallback.
        if (empty($this->config)) {
            return parent::get_backup_encoded_config();
        }
        $data = clone($this->config);
        $data->text = backup_encode_absolute_links($data->text);
        return base64_encode(serialize($data));
    }

    /**
     * This function makes all the necessary calls to {@link restore_decode_content_links_worker()}
     * function in order to decode contents of this block from the backup
     * format to destination site/course in order to mantain inter-activities
     * working in the backup/restore process.
     *
     * This is called from {@link restore_decode_content_links()} function in the restore process.
     *
     * NOTE: There is no block instance when this method is called.
     *
     * @param object $restore Standard restore object
     * @return boolean
     **/
    function decode_content_links_caller($restore) {
        global $CFG, $DB;

        if ($restored_blocks = $DB->get_records_select("backup_ids", "table_name = 'block_instance' AND backup_code = ? AND new_id > 0", array($restore->backup_unique_code), "", "new_id")) {
            $restored_blocks = implode(',', array_keys($restored_blocks));
            $sql = "SELECT bi.*
                      FROM {block_instance} bi
                           JOIN {block} b ON b.id = bi.blockid
                     WHERE b.name = 'html' AND bi.id IN ($restored_blocks)";

            if ($instances = $DB->get_records_sql($sql)) {
                foreach ($instances as $instance) {
                    $blockobject = block_instance('html', $instance);
                    $blockobject->config->text = restore_decode_absolute_links($blockobject->config->text);
                    $blockobject->config->text = restore_decode_content_links_worker($blockobject->config->text, $restore);
                    $blockobject->instance_config_commit();
                }
            }
        }

        return true;
    }

    function send_file($context, $filearea, $itemid, $filepath, $filename) {
        $fs = get_file_storage();
        $fullpath = $context->id.'block_html'.$itemid.$filepath.$filename;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close();
        send_stored_file($file, 60*60, 0, false);
    }
}
