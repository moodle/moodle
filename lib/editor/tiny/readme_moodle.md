# This is a description for the TinyMCE 6 library integration with Moodle.

## Upgrade procedure for TinyMCE Editor

1. Store an environment variable to the Tiny directory in the Moodle repository (the current directory).

 ```
 MOODLEDIR=`pwd`
 ```

2. Check out a clean copy of TinyMCE of the target version.

 ```
 tinymce=`mktemp -d`
 cd "${tinymce}"
 git clone https://github.com/tinymce/tinymce.git
 cd tinymce
 git checkout [version]
 ```

3. Update the typescript configuration to generate ES6 modules with ES2020 target.

 ```
 sed -i 's/"module".*es.*",/"module": "es6",/' tsconfig.shared.json
 sed -i 's/"target.*es.*",/"target": "es2020",/' tsconfig.shared.json
 ```

4. Rebuild TinyMCE

 ```
 yarn
 yarn build
 ```

5. Remove the old TinyMCE configuration and replace it with the newly built version.

 ```
 rm -rf "${MOODLEDIR}/js"
 cp -r modules/tinymce/js "${MOODLEDIR}/js"
 ```

6. Check the (Release notes)[https://www.tiny.cloud/docs/tinymce/6/release-notes/] for any new plugins, premium plugins, menu items, or buttons and add them to classes/manager.php

## Update procedure for included TinyMCE translations

1. Visit https://www.tiny.cloud/get-tiny/language-packages/ and download the "TinyMCE 6 All languages" zip file.
2. Check the list of languages and confirm that the German translation is still at 100%. If not, then make a note of a language which is.
3. Unzip the translation into a new directory:

 ```bash
 langdir=`mktemp -d`
 cd "${langdir}"
 unzip path/to/langs.zip
 ```

4. Run the translation tool:

 ```bash
 node "${MOODLEDIR}/tools/createLangStrings.mjs"
 ```

 This will generate a language file for each available Language, as well as a `tinystrings.json`, and a `strings.php` which will be used in the subsequent steps.

5. Copy the `tinystrings.json` file into the Moodle directory

 ```
 cp tinystrings.json "${MOODLEDIR}/tinystrings.json"
 ```

6. Copy the content of the `strings.php` file over the existing tiny strings:

 ```
 sed -i "/string\['tiny:/d" "${MOODLEDIR}/lang/en/editor_tiny.php"
 cat strings.php >> "${MOODLEDIR}/lang/en/editor_tiny.php"
 ```

7. Commit changes
8. If required, the remaining language strings can be fed into AMOS.

---

**Note:** A set of language files are also generated for all supported translations and may be submitted to AMOS if desired.

**Note:** You will need to manually check for any Moodle-updated language strings as part of this change (for example any from the en_fixes).

---
