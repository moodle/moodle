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
 * Strings for component 'chat', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   chat
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['ajax'] = 'Version using Ajax';
$string['autoscroll'] = 'Auto scroll';
$string['beep'] = 'beep';
$string['cantlogin'] = 'Could not log in to chat room!!';
$string['configmethod'] = 'The ajax chat method provide an ajax based chat interface, it contacts server regularly for update. The normal chat method involves the clients regularly contacting the server for updates. It requires no configuration and works everywhere, but it can create a large load on the server with many chatters.  Using a server daemon requires shell access to Unix, but it results in a fast scalable chat environment.';
$string['confignormalupdatemode'] = 'Chatroom updates are normally served efficiently using the <em>Keep-Alive</em> feature of HTTP 1.1, but this is still quite heavy on the server. A more advanced method is to use the <em>Stream</em> strategy to feed updates to the users. Using <em>Stream</em> scales much better (similar to the chatd method) but may not be supported by your server.';
$string['configoldping'] = 'What is the maximum time that may pass before we detect that a user has disconnected (in seconds)? This is just an upper limit, as usually disconnects are detected very quickly. Lower values will be more demanding on your server. If you are using the normal method, <strong>never</strong> set this lower than 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'How often should the chat room itself be refreshed? (in seconds).  Setting this low will make the chat room seem quicker, but it may place a higher load on your web server when many people are chatting. If you are using <em>Stream</em> updates, you can select higher refresh frequencies -- try with 2.';
$string['configrefreshuserlist'] = 'How often should the list of users be refreshed? (in seconds)';
$string['configserverhost'] = 'The hostname of the computer where the server daemon is';
$string['configserverip'] = 'The numerical IP address that matches the above hostname';
$string['configservermax'] = 'Max number of clients allowed';
$string['configserverport'] = 'Port to use on the server for the daemon';
$string['currentchats'] = 'Active chat sessions';
$string['currentusers'] = 'Current users';
$string['deletesession'] = 'Delete this session';
$string['deletesessionsure'] = 'Are you sure you want to delete this session?';
$string['donotusechattime'] = 'Don\'t publish any chat times';
$string['enterchat'] = 'Click here to enter the chat now';
$string['errornousers'] = 'Could not find any users!';
$string['explaingeneralconfig'] = 'These settings are <strong>always</strong> used';
$string['explainmethoddaemon'] = 'These settings matter <strong>only</strong> if you have selected "Chat server daemon" for chat_method';
$string['explainmethodnormal'] = 'These settings matter <strong>only</strong> if you have selected "Normal method" for chat_method';
$string['generalconfig'] = 'General configuration';
$string['chat:deletelog'] = 'Delete chat logs';
$string['chat:exportparticipatedsession'] = 'Export chat session which you took part in';
$string['chat:exportsession'] = 'Export any chat session';
$string['chat:chat'] = 'Access a chat room';
$string['chatintro'] = 'Introduction text';
$string['chatname'] = 'Name of this chat room';
$string['chat:readlog'] = 'Read chat logs';
$string['chatreport'] = 'Chat sessions';
$string['chat:talk'] = 'Talk in a chat';
$string['chattime'] = 'Next chat time';
$string['idle'] = 'Idle';
$string['inputarea'] = 'Input area';
$string['invalidid'] = 'Could not find that chat room!';
$string['list_all_sessions'] = 'List all sessions.';
$string['list_complete_sessions'] = 'List just complete sessions.';
$string['listing_all_sessions'] = 'Listing all sessions.';
$string['messagebeepseveryone'] = '{$a} beeps everyone!';
$string['messagebeepsyou'] = '{$a} has just beeped you!';
$string['messageenter'] = '{$a} has just entered this chat';
$string['messageexit'] = '{$a} has left this chat';
$string['messages'] = 'Messages';
$string['messageyoubeep'] = 'You beeped {$a}';
$string['method'] = 'Chat method';
$string['methoddaemon'] = 'Chat server daemon';
$string['methodnormal'] = 'Normal method';
$string['methodajax'] = 'Ajax method';
$string['modulename'] = 'Chat';
$string['modulename_help'] = 'The chat module allows participants to have a real-time synchronous discussion via the web.  This is a useful way to get a different understanding of each other and the topic being discussed - the mode
of using a chat room is quite different from the asynchronous forums.';
$string['modulenameplural'] = 'Chats';
$string['neverdeletemessages'] = 'Never delete messages';
$string['nextsession'] = 'Next scheduled session';
$string['no_complete_sessions_found'] = 'No complete sessions found.';
$string['noguests'] = 'The chat is not open to guests';
$string['nochat'] = 'No chat found';
$string['nomessages'] = 'No messages yet';
$string['normalkeepalive'] = 'KeepAlive';
$string['normalstream'] = 'Stream';
$string['noscheduledsession'] = 'No scheduled session';
$string['notallowenter'] = 'You are not allow to enter the chat room.';
$string['notlogged'] = 'You are not logged in!';
$string['nopermissiontoseethechatlog'] = 'You don\'t have permission to see the chat logs.';
$string['oldping'] = 'Disconnect timeout';
$string['page-mod-chat-x'] = 'Any chat module page';
$string['pastchats'] = 'Past chat sessions';
$string['pluginadministration'] = 'Chat administration';
$string['pluginname'] = 'Chat';
$string['refreshroom'] = 'Refresh room';
$string['refreshuserlist'] = 'Refresh user list';
$string['removemessages'] = 'Remove all messages';
$string['repeatdaily'] = 'At the same time every day';
$string['repeatnone'] = 'No repeats - publish the specified time only';
$string['repeattimes'] = 'Repeat sessions';
$string['repeatweekly'] = 'At the same time every week';
$string['saidto'] = 'said to';
$string['savemessages'] = 'Save past sessions';
$string['seesession'] = 'See this session';
$string['send'] = 'Send';
$string['sending'] = 'Sending';
$string['serverhost'] = 'Server name';
$string['serverip'] = 'Server ip';
$string['servermax'] = 'Max users';
$string['serverport'] = 'Server port';
$string['sessions'] = 'Chat sessions';
$string['sessionstart'] = 'Chat session will be start in: {$a}';
$string['strftimemessage'] = '%H:%M';
$string['studentseereports'] = 'Everyone can view past sessions';
$string['studentseereports_help'] = 'If set to No, only users have mod/chat:readlog capability are able to see the chat logs';
$string['talk'] = 'Talk';
$string['updatemethod'] = 'Update method';
$string['updaterate'] = 'Update rate:';
$string['userlist'] = 'User list';
$string['usingchat'] = 'Using chat';
$string['usingchat_help'] = 'The chat module contains some features to make chatting a little nicer.

* Smilies - Any smiley faces (emoticons) that you can type elsewhere in Moodle can also be typed here, for example :-)
* Links - Website addresses will be turned into links automatically
* Emoting - You can start a line with "/me" or ":" to emote, for example if your name is Kim and you type ":laughs!" or "/me laughs!" then everyone will see "Kim laughs!"
* Beeps - You can send a sound to other participants by clicking the "beep" link next to their name. A useful shortcut to beep all the people in the chat at once is to type "beep all".
* HTML - If you know some HTML code, you can use it in your text to do things like insert images, play sounds or create different coloured text';
$string['viewreport'] = 'View past chat sessions';
