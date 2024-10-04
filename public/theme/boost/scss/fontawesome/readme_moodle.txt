DESCRIPTION OF FONT AWESOME IMPORT INTO MOODLE
-----------------------------------------------

Download the latest free web version from https://github.com/FortAwesome/Font-Awesome/

Font Awesome comes in 2 parts relating to Moodle:

1. Fonts:
  a. Replace the content in lib/fonts with the files in the webfonts folder.
  b. Update lib/thirdpartylibs.xml.

2. SCSS:
  a. Replace the files in this folder (/theme/boost/scss/fontawesome) with the files in the scss folder.
  b. Copy the LICENSE.txt file from the root to theme/boost/scss/fontawesome.
  c. Update theme/boost/thirdpartylibs.xml.

CHANGES
--------
1. The fonts need to be provided using the [[font:core|xxxx]] method. Edit fontawesome/brands.scss, fontawesome/regular.scss and fontawesome/solid.scss to replace:

    url('#{$fa-font-path}/fa-xxxxx-400.zzzzz') format('zzzzz')

with

    url('[[font:core|fa-xxxxx-400.zzzzz]]') format('zzzzz'),

FINALLY
--------

After applying the previous changes to the library:

1. Update the Component library files (for instance, admin/tool/componentlibrary/content/moodle/components/moodle-icons.md).

2. Run `php admin/tool/componentlibrary/cli/fetchicons.php` to update admin/tool/componentlibrary/hugo/site/data/fontawesomeicons.json

3. Run `grunt` to update the CSS style files and the Component library files.
