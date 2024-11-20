# This is a description for the TinyMCE 7 library integration with Moodle.

Please note that we have a clone of the official TinyMCE repository which contains the working build and branch for each release. This ensures build repeatability and gives us the ability to patch stable versions of Moodle for security fixes where relevant.

Each Moodle branch has a similar branch in the https://github.com/moodlehq/tinymce.
The Moodle `master` branch is named as the upcoming STABLE branch name, for example during the development of Moodle 4.5.0, the upcoming STABLE branch name will be MOODLE_405_STABLE.

## Patches included in this release

N/A

## Upgrade procedure for TinyMCE Editor

1. Store an environment variable to the Tiny directory in the Moodle repository (the current directory).

 ```
MOODLEDIR=`pwd`/../../
 ```

2. Check out a clean copy of TinyMCE of the target version.

 ```
tinymce=`mktemp -d`
cd "${tinymce}"
git clone https://github.com/tinymce/tinymce.git
cd tinymce
git checkout -b MOODLE_405_STABLE
git reset --hard [desired version]
 ```

3. Install dependencies

 ```
yarn
 ```

4. Check in the base changes

 ```
git commit -m 'MDL: Add build configuration'
 ```

5. Apply any necessary security patches.
6. Rebuild TinyMCE

 ```
yarn
yarn build
 ```

7. Remove the old TinyMCE configuration and replace it with the newly built version.

 ```
rm -rf "${MOODLEDIR}/js"
cp -r modules/tinymce/js "${MOODLEDIR}/js"
 ```

8. Push the build to MoodleHQ for future change support

 ```
# Tag the next Moodle version.
git tag v4.5.0
git remote add moodlehq --tags
git push moodlehq MOODLE_405_STABLE
 ```

9. Check the (Release notes)[https://www.tiny.cloud/docs/tinymce/7/release-notes/] for any new plugins, premium plugins, menu items, or buttons and add them to classes/manager.php

## Update procedure for included TinyMCE translations

1. Visit https://www.tiny.cloud/get-tiny/language-packages/ and download the "TinyMCE 7 All languages" zip file.
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

7. Commit changes. Note: You may need to review individual language changes which do not meet Moodle's guidelines.
8. If required, the remaining language strings can be fed into AMOS.

---

**Note:** A set of language files are also generated for all supported translations and may be submitted to AMOS if desired.

**Note:** You will need to manually check for any Moodle-updated language strings as part of this change (for example any from the en_fixes).

---

## Security fix procedure for TinyMCE Editor

1. Store an environment variable to the Tiny directory in the Moodle repository (the current directory).

 ```
MOODLEDIR=`pwd`../../
 ```

2. Check out a clean copy of TinyMCE of the target version.

 ```
tinymce=`mktemp -d`
cd "${tinymce}"
git clone https://github.com/tinymce/tinymce.git
cd tinymce
git remote add moodlehq https://github.com/moodlehq/tinymce
git fetch moodlehq
git checkout -b MOODLE_405_STABLE moodlehq/MOODLE_405_STABLE
 ```

3. Apply any necessary security patches.
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

---

**Note:** You may need to remove some parts of some patches, such as tests, changelog entries, etc. to get the patch to apply.

**Note:** The generated code may be significantly larger than the source patch

---
