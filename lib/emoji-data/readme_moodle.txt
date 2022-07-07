Description of emoji data import into Moodle.

1. Download the latest release of emoji data from https://github.com/iamcal/emoji-data/releases

2. Copy emoji_pretty.json into lib/emoji-data/

3. Run the generate_emoji_data.php script to create the new data.js file
   E.g. php lib/emoji-data/generate_emoji_data.php > lib/amd/src/emoji/data.js

4. Build the new emoji data.js file
   E.g. grunt amd --files=lib/amd/src/emoji/data.js

5. Delete the emoji_pretty.json file from step 3
