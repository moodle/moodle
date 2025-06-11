# moodle-tiny_wordimport
This plugin allows for importing Microsoft Word Files (.docx) to Moodle within the new TinyMCE editor.

## Requirements
This theme requires Moodle 4.1+ and the [booktool_wordimport](https://moodle.org/plugins/booktool_wordimport) plugin with minimum version 1.4.14.

## Installation
Install the plugin to folder `<moodle_base_directory>/lib/editor/tiny/plugins/wordimport`.

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins.

## Usage
Either use the Insert Word button in the TinyMCE editor and select a `.docx` Word file from the file picker, or drag and drop a `.docx` file into the editor.
Note that `.doc` files are currently not supported.

## Settings
By default, all editing teachers have the capability `tiny/wordimport:add` to import Word files in the TinyMCE editor, either via the import button or via drag-and-drop.

## Credits
The plugins strongly leans on how https://moodle.org/plugins/atto_wordimport implemented the Word File Import for the Atto editor.

## Contributions
The University of Graz initiated and currently maintains this plugin. Contributions are welcome and highly appreciated. Here's how you can help:

- **Report Bugs:** Spot a problem? Let us know by reporting bugs (preferably on codeberg.org)
- **Feedback:** Offer reviews and suggestions to help us improve.
- **Ideas:** Share your innovative ideas and feature proposals.
- **Code Contributions:** Submit code changes or new features through pull requests.

Your involvement is what drives this plugin forward.

We would like to express our gratitude to the following contributors:

- Bern University of Applied Sciences (BFH), [Luca Bösch](https://github.com/lucaboesch): Code
- [Dan Marsden](https://github.com/danmarsden): Review
