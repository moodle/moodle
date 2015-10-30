<?php
$chattheme_cfg = new stdClass();
$chattheme_cfg->avatar = false;
$chattheme_cfg->align  = false;
$chattheme_cfg->event_message = <<<TEMPLATE
<div class="chat-event course-theme">
<span class="time">___time___</span>
<a target='_blank' href="___senderprofile___">___sender___</a>
<span class="event">___event___</span>
</div>
TEMPLATE;
$chattheme_cfg->user_message = <<<TEMPLATE
<div class='chat-message course-theme'>
    <div class="chat-message-meta">
        <span class="time">___time___</span>
        <span class="user"><a href="___senderprofile___" target="_blank">___sender___</a></span>
    </div>
    <div class="text">
    ___message___
    </div>
</div>
TEMPLATE;
