# Jupyter Moodle plugin

This Moodle plugin integrates Jupyter Notebooks to offer a virtual programming environment.

The plugin connects to a JupyterHub server and authenticates the Moodle users on the JupyterHub Server. That way they
can access a Jupyter notebook from within Moodle.

Automated grading of Jupyter Notebooks is provided through [Otter-Grader](https://otter-grader.readthedocs.io/en/latest/).  
A quick introduction for writing assignments in the Otter-Grader format and a demo Notebook can be fond here:  
[AutograderNotebook.md](documentation/AutograderNotebook.md)  
[demo.ipynb](documentation/demo.ipynb)

## Plugin Installation

**Make sure you have a compatible JupyterHub and Grading API running and reachable.  
Details on how to set this up can be found here: https://github.com/forschungsprojekte-II-ws2223/jupyterhub-gradeservice**

Download the latest version of this plugin [here](https://github.com/forschungsprojekte-II-ws2223/moodle-mod_jupyter/releases/download/1.0/moodle-mod_jupyter.zip) and add it to your Moodle installation.

### Manual installation

1. Clone this repository:

   ```shell
   git clone git@github.com:forschungsprojekte-II-ws2223/moodle-mod_jupyter.git jupyter
   ```

   (The folder name should be jupyter not moodle-mod_jupyter)

1. Add third-party dependencies with [composer](https://getcomposer.org/download/):

   ```shell
   cd jupyter && composer install
   ```

1. Add the folder to your moodle installation.

## Development Environment Setup

Follow [this](https://github.com/forschungsprojekte-II-ws2223/setup/blob/main/DevEnvSetup.md) guide for setting up the development environment.

There's an .editorconfig in this repo, please use it while working on it.

[EditorConfig VS Code Extension](vscode://extension/EditorConfig.EditorConfig)

## License

**Kuenstliche Intelligenz in die Berufliche Bildung Bringen (KIB3)**
**2022 summer semester student project of University of Stuttgart**

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see [GNU license](https://www.gnu.org/licenses).

## Additional resources

- [Moodle official development main page](https://docs.moodle.org/dev/Main_Page)
- [Moodle official output api page](https://docs.moodle.org/dev/Output_API)
- [Moodle official javascript page](https://docs.moodle.org/dev/Javascript_Modules)
- [Moodle official development activity modules page](https://docs.moodle.org/dev/Activity_modules)
- [Moodle programming course](https://www.youtube.com/playlist?list=PLgfLVzXXIo5q10qVXDVyD-JZVyZL9pCq0)

## Development Team

- Buchholz, Max
- Günther, Ralph
- Klaß, Robin
- König, Solveigh
- Marinic, Noah
- Schüle, Maximilian
- Stoll, Timo
- Weber, Raphael
- Wohlfart, Phillip
- Zhang, Yichi
- Zoller, Nick

developed this plugin in the context of the Student Project of University of Stuttgart in the Summer Semester 2022
