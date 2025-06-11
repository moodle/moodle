# Repository Structure

> This project has been created following the Moodle guidelines to create a Moodle plugin, specifically, a [TinyMCE editor plugins
](https://moodledev.io/docs/4.4/apis/plugintypes/tiny).

Once the project has been opened, you will see the following structure:

```plain
MathType Moodle plugin for TinyMCE
├── .github         - contains Github Workflows used by Github Actions.
├── amd             - contains raw, modular JavaScript and compiled, optimized files. 
├── classes         - stores autoloaded PHP classes, allowing to load them without manual 
|                     require or include statements.
├── docs            - contains the files and directories needed to document this project
├── js              - contains a minified script that registers and initializes the plugin
├── lang            - contains a file with a set of strings for english translation.
├── pix             - stores icons for plugins and the system.
└── tests           - contains the behat tests for the plugin.
```
