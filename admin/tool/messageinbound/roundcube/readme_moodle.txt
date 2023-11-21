Description of Roundcube Framework 1.6.6 library import into Moodle

We now use the client part of Roundcube Framework as a library in Moodle.
This library is used to receive emails from Moodle.
This library is not used to send emails.

For more information on this version of Roundcube Framework, check out https://github.com/roundcube/roundcubemail/releases/tag/1.6.6

To upgrade this library:
1. Download the latest release of Roundcube Framework (roundcube-framework-xxx-tar.gz) in https://github.com/roundcube/roundcubemail/releases.
2. Extract the contents of the release archive to a temp folder.
3. Copy the following files from the temp folder to the Moodle folder admin/tool/messageinbound/roundcube:
   - rcube_charset.php
   - rcube_imap_generic.php
   - rcube_message_header.php
   - rcube_mime.php
   - rcube_result_index.php
   - rcube_result_thread.php
   - rcube_utils.php
4. Find and replace all array_first() calls with array_shift() in the following files:
   - rcube_imap_generic.php
   - rcube_result_index.php
   - rcube_result_thread.php
5. Update admin/tool/messageinbound/thirdpartylibs.xml.
