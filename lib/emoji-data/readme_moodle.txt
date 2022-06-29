Description of emoji data import into Moodle.

1. Download the latest release of emoji data from https://github.com/iamcal/emoji-data/tags

2. Copy emoji_pretty.json into lib/emoji-data/

3. Run the generate_emoji_data.php script to create the new data.js file
   E.g. php lib/emoji-data/generate_emoji_data.php > lib/amd/src/emoji/data.js

   Once this command has been executed, open the newly created lib/amd/src/emoji/data.js file and make sure that the
   emoji data has been successfully generated.
   ==================================================================================================================
   NOTE: If the following error messages are displayed instead of the emoji data in data.js you should:
   * 'The following categories are missing: xyz, xyz, ...'
      1. add these categories in $categorysortorder in generate_emoji_data.php
      2. include these categories in the emoji picker template (lib/templates/emoji/picker.mustache).
      3. add appropriate icon mappings for these categories in get_core_icon_map() in
         lib/classes/output/icon_system_fontawesome.php.
      4. add language strings for these categories in lang/en/moodle.php.
   * 'The following categories are no longer used: xyz, xyz, ...'
      1. remove these categories from $categorysortorder in generate_emoji_data.php
      2. remove these categories from the emoji picker template (lib/templates/emoji/picker.mustache).
      3. remove the icon mappings for these categories in get_core_icon_map() in
         lib/classes/output/icon_system_fontawesome.php.
      4. remove (deprecate) the language strings for these categories in lang/en/moodle.php.

   Rerun 'php lib/emoji-data/generate_emoji_data.php > lib/amd/src/emoji/data.js'
   ==================================================================================================================

4. Build the new emoji data.js file
   E.g. grunt amd --files=lib/amd/src/emoji/data.js

5. Delete the emoji_pretty.json file from step 3
