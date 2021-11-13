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
