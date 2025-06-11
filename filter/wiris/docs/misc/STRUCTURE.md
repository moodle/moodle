# Repository Structure

> This project has been created following the Moodle guidelines to create a Moodle plugin, specifically, a [Moodle filter plugin](https://moodledev.io/docs/5.0/apis/plugintypes/filter).

Once the project has been opened, you will see the following structure:

```plain
MathType filter
├── .github         - contains Github Workflows used by Github Actions.
├── classes         - stores autoloaded PHP classes, allowing to load them without manual 
|                     require or include statements.
├── db              - database schema, upgrade steps, and access control rules, for proper 
|                     installation, updates, and permissions.
├── docs            - contains the files and directories needed to document this project
├── integration     - contains the back-end render services implementation.
├── lang            - contains a file with a set of strings for english translation.
├── pix             - stores icons for plugins and the system.
├── render          - contains the front-end render services.
├── subfilters      - implements the behavior of MathType filter for JS and PHP rendering.
└── tests           - contains the behat tests for the plugin and steps implementation.
```
