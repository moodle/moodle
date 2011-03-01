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
<table class='chat-message'___tablealign___><tr>
<td class="picture" valign="middle" width="32px">
___avatar___
</td>
<td class="text">
    <table cellspacing="0" cellpadding="0" border="0" ___mymessageclass___>
    <tbody>
        <tr><td class="topleft"></td><td class="top"></td><td class="topright"></td></tr>
        <tr>
            <td class="left"></td>
            <td class="conmts">
            ___message___
            </td>
            <td class="right"></td>
        </tr>
        <tr>
            <td class="bottomleft"></td>
            <td class="bottom"></td>
            <td class="bottomright"></td>
        </tr>
    </tbody>
    </table>
</td>
<tr>
<td colspan="2"___align___>
    <span class="time">___time___</span>
    <span class="user">___sender___</span>
</td>
</tr>
TEMPLATE;
$chattheme_cfg->user_message_right = <<<TEMPLATE
<table class='chat-message'___tablealign___><tr>
<td class="text">
    <table cellspacing="0" cellpadding="0" border="0" ___mymessageclass___>
    <tbody>
        <tr><td class="topleft"></td><td class="top"></td><td class="topright"></td></tr>
        <tr>
            <td class="left"></td>
            <td class="conmts">
            ___message___
            </td>
            <td class="right"></td>
        </tr>
        <tr>
            <td class="bottomleft"></td>
            <td class="bottom"></td>
            <td class="bottomright"></td>
        </tr>
    </tbody>
    </table>
</td>
<td class="picture" valign="middle" width="32px">
___avatar___
</td>
<tr>
<td colspan="2" ___tablealign___>
    <span class="time">___time___</span>
    <span class="user">___sender___</span>
</td>
</tr>
TEMPLATE;
