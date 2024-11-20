# Moodle Jupyternotebook module
- Source Code: https://gitlab.com/dne-elearning/moodle-magistere/moodle-mod_jupyternotebook
- License: http://www.gnu.org/licenses/gpl-3.0.html

## Install from an archive
- Extract the archive in the /mod/jupyternotebook folder
- Install by connecting to your moodle as an administrator or user the CLI script **admin/cli/upgrade.php** if you have access to a console.

## Configuration

The Jupyternotebook module requires a *Github* repository to host the Python files. The Github repository information must be entered in the plugin settings page.

Github documentation is available here : https://docs.github.com/fr  
\
When creating a Jupyternotebook activity you must provide a *.ipynb file* and an URL pointing to an accessible *Jupyter server*.

Information on running a Jupyter server can be found :
- here for a single-user server : https://jupyter-notebook.readthedocs.io/en/stable/public_server.html
- here for a multi-user server : https://jupyterhub.readthedocs.io/en/latest/

A default URL can be set in the plugin settings page.

## Description

A course module to integrate Jupyter notebooks within Moodle courses. This module requires a Jupyter server and a Github repository.

You can find more information on Jupyter and its notebook here : https://docs.jupyter.org/en/latest/

