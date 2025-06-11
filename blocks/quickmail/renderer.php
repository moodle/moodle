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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use block_quickmail\controllers\support\controller_form_component;

use block_quickmail\components\draft_message_index_component;
use block_quickmail\components\queued_message_index_component;
use block_quickmail\components\sent_message_index_component;
use block_quickmail\components\alternate_index_component;
use block_quickmail\components\notification_index_component;
use block_quickmail\components\view_message_component;

use block_quickmail\components\broadcast_message_component;
use block_quickmail\components\broadcast_recipient_filter_results_component;
use block_quickmail\components\compose_message_component;

class block_quickmail_renderer extends plugin_renderer_base {

    // Controller form components.
    public function controller_form_component(controller_form_component $component) {
        return $this->render($component);
    }

    protected function render_controller_form_component(controller_form_component $component) {
        $out = '';

        // Render heading, if it exists.
        if (property_exists($component, 'heading')) {
            $out .= $this->output->heading(format_string($component->heading), 2);
        }

        // Render any forms.
        $out .= $component->form->render();

        return $this->output->container($out, 'controller_form_component');
    }

    // Controller templates.
    public function controller_component_template($componentname, $params = []) {
        // Get class full component class name.
        $componentclass = 'block_quickmail\components\\' . $componentname . '_component';

        // Instantiate component including params.
        $component = new $componentclass($params);

        return $this->render($component);
    }

    protected function render_sent_message_index_component(sent_message_index_component $sentmessageindexcomponent) {
        return $this->render_from_template('block_quickmail/sent_message_index',
            $sentmessageindexcomponent->export_for_template($this));
    }

    protected function render_queued_message_index_component(queued_message_index_component $queuedmessageindexcomponent) {
        return $this->render_from_template('block_quickmail/queued_message_index',
            $queuedmessageindexcomponent->export_for_template($this));
    }

    protected function render_draft_message_index_component(draft_message_index_component $draftmessageindexcomponent) {
        return $this->render_from_template('block_quickmail/draft_message_index',
            $draftmessageindexcomponent->export_for_template($this));
    }

    protected function render_alternate_index_component(alternate_index_component $alternateindexcomponent) {
        return $this->render_from_template('block_quickmail/alternate_index',
            $alternateindexcomponent->export_for_template($this));
    }

    protected function render_notification_index_component(notification_index_component $notificationindexcomponent) {
        return $this->render_from_template('block_quickmail/notification_index',
            $notificationindexcomponent->export_for_template($this));
    }

    protected function render_view_message_component(view_message_component $viewmessagecomponent) {
        return $this->render_from_template('block_quickmail/view_message',
            $viewmessagecomponent->export_for_template($this));
    }

    // Broadcast form.
    public function broadcast_message_component($params = []) {
        $broadcastmessagecomponent = new broadcast_message_component($params);

        return $this->render($broadcastmessagecomponent);
    }

    protected function render_broadcast_message_component(broadcast_message_component $broadcastmessagecomponent) {
        $out = '';

        // Render heading.
        $out .= $this->output->heading(format_string($broadcastmessagecomponent->heading), 2);

        // Render compose form.
        $out .= $broadcastmessagecomponent->broadcast_form->render();

        return $this->output->container($out, 'broadcast_message_component');
    }

    // Broadcast recipient filter results.
    public function broadcast_recipient_filter_results_component($params = []) {
        $broadcastrecipientfilterresultscomponent = new broadcast_recipient_filter_results_component($params);

        return $this->render($broadcastrecipientfilterresultscomponent);
    }

    protected function render_broadcast_recipient_filter_results_component(
        broadcast_recipient_filter_results_component $broadcastrecipientfilterresultscomponent
        ) {
        $data = $broadcastrecipientfilterresultscomponent->export_for_template($this);

        return $this->render_from_template('block_quickmail/broadcast_recipient_filter_results', $data);
    }

    // Compose form.
    public function compose_message_component($params = []) {
        $composemessagecomponent = new compose_message_component($params);

        return $this->render($composemessagecomponent);
    }

    protected function render_compose_message_component(compose_message_component $composemessagecomponent) {
        $out = '';

        // Render heading.
        $out .= $this->output->heading(format_string($composemessagecomponent->heading), 2);

        // Render compose form.
        $out .= $composemessagecomponent->compose_form->render();

        return $this->output->container($out, 'compose_message_component');
    }

}
