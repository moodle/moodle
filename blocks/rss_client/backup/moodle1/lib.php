<?php

/**
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    block_rss_client
 * @copyright  2012 Paul Nicholls
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block conversion handler for rss_client
 */
class moodle1_block_rss_client_handler extends moodle1_block_handler {
    public function process_block(array $data) {
        parent::process_block($data);
        $instanceid = $data['id'];
        $contextid = $this->converter->get_contextid(CONTEXT_BLOCK, $data['id']);

        // Moodle 1.9 backups do not include sufficient data to restore feeds, so we need an empty shell rss_client.xml
        // for the restore process to find
        $this->open_xml_writer("course/blocks/{$data['name']}_{$instanceid}/rss_client.xml");
        $this->xmlwriter->begin_tag('block', array('id' => $instanceid, 'contextid' => $contextid, 'blockname' => 'rss_client'));
        $this->xmlwriter->begin_tag('rss_client', array('id' => $instanceid));
        $this->xmlwriter->full_tag('feeds', '');
        $this->xmlwriter->end_tag('rss_client');
        $this->xmlwriter->end_tag('block');
        $this->close_xml_writer();

        return $data;
    }
}