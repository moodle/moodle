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

$chattheme_cfg = new stdClass();
$chattheme_cfg->avatar = false;
$chattheme_cfg->align  = false;
$chattheme_cfg->event_message = <<<TEMPLATE
<div class="chat-event">
<span class="time">___time___</span>
<a target='_blank' href="___senderprofile___">___sender___</a>
<span class="event">___event___</span>
</div>
TEMPLATE;
$chattheme_cfg->user_message = <<<TEMPLATE
<div class='chat-message'>
    <div class="chat-message-meta">
        <span class="time">___time___</span>
        <span class="user"><a href="___senderprofile___" target="_blank">___sender___</a></span>
    </div>
    <div class="text">
    ___message___
    </div>
</div>
TEMPLATE;
