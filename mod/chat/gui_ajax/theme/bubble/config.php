<?php
$chattheme_cfg = new stdClass();
$chattheme_cfg->avatar = true;
$chattheme_cfg->align  = true;
$chattheme_cfg->event_message = <<<TEMPLATE
<div class="chat-event">
<span class="time">___time___</span>
<a target='_blank' href="___senderprofile___">___sender___</a>
<span class="event">___event___</span>
</div>
TEMPLATE;
$chattheme_cfg->user_message_left = <<<TEMPLATE
<div class='chat-message ___mymessageclass___'>
    <div class="left">
        <span class="text triangle-border left">___message___</span>
        <span class="picture">___avatar___</span>
    </div>
    <div class="chat-message-meta left">
        <span class="time">___time___</span>
        <span class="user">___sender___</span>
    </div>
</div>
TEMPLATE;
$chattheme_cfg->user_message_right = <<<TEMPLATE
<div class='chat-message ___mymessageclass___'>
    <div class="right">
        <span class="text triangle-border right">___message___</span>
        <span class="picture">___avatar___</span>
    </div>
    <div class="chat-message-meta right">
        <span class="time">___time___</span>
        <span class="user">___sender___</span>
    </div>
</div>
TEMPLATE;
