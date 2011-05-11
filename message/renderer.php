<?php

/**
 * Messaging libraries
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * message Renderer
 *
 * Class for rendering various message objects
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_renderer extends plugin_renderer_base {

    /**
     * Display the interface to manage message outputs
     *
     * @param   array   $processors The list of message processors
     * @return  string              The text to render
     */
    public function manage_messageoutputs($processors) {
        // Display the current workflows
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->head        = array();
        $table->colclasses  = array();
        $table->data        = array();
        $table->head        = array(
            get_string('name'),
            get_string('enable'),
            get_string('settings'),
        );
        $table->colclasses = array(
            'displayname', 'availability', 'settings',
        );

        foreach ( $processors as $processor ){
            $row = new html_table_row();
            $row->attributes['class'] = 'workflow';

            // Name
            $name = new html_table_cell($processor->name);

            // Enable
            $enable = new html_table_cell();
            if (!$processor->available) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotavailable', 'message'), array('class' => 'error'));
            } else if (!$processor->configured) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotconfigured', 'message'), array('class' => 'error'));
            } else if ($processor->enabled) {
                $row->attributes['class'] .= 'enabled';
                $url = new moodle_url('/admin/message.php', array('disable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('i/hide'),
                          'class' => 'icon',
                          'title' => get_string('outputenabled', 'message'),
                          'alt'   => get_string('outputenabled', 'message'),
                    )
                ));
            } else {
                $row->attributes['class'] .= 'disabled';
                $url = new moodle_url('/admin/message.php', array('enable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('i/show'),
                          'class' => 'icon',
                          'title' => get_string('outputdisabled', 'message'),
                          'alt'   => get_string('outputdisabled', 'message'),
                    )
                ));
            }

            $settings = new html_table_cell();
            if ($processor->available) {
                $settingsurl = new moodle_url('/message/output/'.$processor->name.'/settings.php');
                $settings->text = html_writer::link($settingsurl, get_string('settings', 'message'));
            }

            $row->cells = array($name, $enable, $settings);
            $table->data[] = $row;
        }
        return html_writer::table($table);
    }
}
