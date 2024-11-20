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

declare(strict_types=1);

namespace tool_moodlenet\task;

/**
 * Ad-hoc task to perform post install tasks.
 * We use this to set the active activity chooser footer plugin to tool_moodlenet.
 * We couldn't do this directly in install.php, because there is an admin_apply_default_settings() call after all plugins are
 * installed and that would reset whatever value we had set earlier to 'hidden'.
 *
 * @package   tool_moodlenet
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_install extends \core\task\adhoc_task {
    public function execute() {
        set_config('activitychooseractivefooter', 'tool_moodlenet');
    }
}
