Description of Roundcube Framework library import into Moodle

We now use the client part of Roundcube Framework as a library in Moodle.
This library is used to receive emails from Moodle.
This library is not used to send emails.

For more information on this version of Roundcube Framework, check out https://github.com/roundcube/roundcubemail/releases/tag/X.Y.Z

To upgrade this library:
1. Download the latest release of Roundcube Framework (roundcube-framework-xyz-tar.gz) in https://github.com/roundcube/roundcubemail/releases.
2. Extract the contents of the release archive to a temp folder.
3. Copy the following files from the temp folder to the Moodle folder admin/tool/messageinbound/roundcube:
   - rcube_charset.php
   - rcube_imap_generic.php
   - rcube_message_header.php
   - rcube_mime.php
   - rcube_result_index.php
   - rcube_result_thread.php
   - rcube_utils.php

   To ease the process, you can execute the below command:
   ```
   cp rcube_charset.php \
   rcube_imap_generic.php \
   rcube_message_header.php \
   rcube_mime.php \
   rcube_result_index.php \
   rcube_result_thread.php \
   rcube_utils.php \
   /path/to/moodle/admin/tool/messageinbound/roundcube/
   ```
4. Enter to the /path/to/moodle/admin/tool/messageinbound/roundcube/.
5. Find and replace all array_first() calls with array_shift() in the following files:
   - rcube_imap_generic.php
   - rcube_result_index.php
   - rcube_result_thread.php

   To ease the process, you can execute the below command:
   ```
   sed -i 's/array_first(/array_shift(/g' \
   rcube_imap_generic.php \
   rcube_result_index.php \
   rcube_result_thread.php
   ```
6. Update the library's version in admin/tool/messageinbound/thirdpartylibs.xml.
