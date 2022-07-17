<?php
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

// Let's try various forbidden strings that may be leading to incorrect
// or unsafe uses of various PHP/Moodle APIs.

// This smells to classic wrong aliasing of tables (AS keyword forbidden for them).
$sql = 'SELECT * FROM {config} AS configuration';
$sql = 'SELECT c.*
          FROM {config} as c,
          JOIN {user} AS u
          JOIN {log}';

// This smells to regexp expression using the unsafe /e modifier.
$regexp = '/Hello (.*)/e';
$regexp = preg_replace("#(Eloy)#mes", 'strtoupper($1)', $text);

// Backticks aren't welcome within strings.
$text = 'Just a warning `here` so far';
$text = "Just a warning `here` too";

// Let's mix the 3 above together for fun.
$sqlregexptick = '@SELECT * FROM {table} AS t WHERE `column` = :param@mes';

// These should not being caught as /e regexp.
$this->pix_url('<div tabindex="0" class="file-picker fp-generallayout" role="dialog" aria-live="assertive">');
$regexp = preg_replace('/test/me0', 'ignore anything having invalid (not "imsxeADSUXJu") modifiers');
echo '       <div class="fp-restrictions">';
echo '{"foo" : {"fe" : 1} }';

// And these are valid lang string using Markdown formatting.
$string['something_desc'] = 'Some with `backticks` is not so bad';
$string['something_help'] = 'Some with `backticks` is not so bad';

// But not these.
$variable['something_desc'] = 'Some with `backticks` in this context is bad';
$string['something_warn'] = 'Some with `backticks` in this context is bad';

// Fair enough.
