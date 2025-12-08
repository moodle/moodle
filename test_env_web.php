<?php
require(__DIR__ . '/config.php');

echo "<h1>Moodle Config Debug (Web)</h1>";
echo "SMTP Host: " . $CFG->smtphosts . "<br>";
echo "SMTP User: " . $CFG->smtpuser . "<br>";
echo "SMTP Pass Set: " . (empty($CFG->smtppass) ? 'NO' : 'YES') . "<br>";
echo "From: " . $CFG->noreplyaddress . "<br>";

echo "<h2>Environment Variables</h2>";
echo "SMTP_HOST: " . getenv('SMTP_HOST') . "<br>";
echo "SMTP_USER: " . getenv('SMTP_USER') . "<br>";
