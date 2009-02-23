Description of PHPMailer 1.73 library import into Moodle

Changes:

class.phpmailer.php
 * Duplicate Message-IDs in Forum mail (MDL-3681)
 * Support for gb18030 (MDL-5229)
 * Correct timezone in date (MDL-12596)
 * Backported fixes for CVE-2007-3215 (MDL-18348)
 * Custom EncodeHeader() to allow multibyte subjects (textlib). Seems that current phpmailer version (2.3) has it properly implemented (though dependent of mbstring).
 * Custom constructor PHPMailer()
 * Custom SetLanguage() function
