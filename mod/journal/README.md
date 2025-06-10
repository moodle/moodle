
![Build Status](https://github.com//elearningsoftware/moodle-mod_journal/workflows/Moodle%20Plugin%20CI/badge.svg?branch=master)

# Moodle Journal module
- Documentation: http://docs.moodle.org/en/Journal_module
- Source Code: https://github.com/elearningsoftware/moodle-mod_journal
- License: http://www.gnu.org/licenses/gpl-2.0.txt

## Install from git
- Navigate to Moodle root folder
- If you plan to use git submodules, then:
    - **git submodule add -f https://github.com/elearningsoftware/moodle-mod_journal mod/journal**
    - **cd mod/journal**
    - [For Moodle 2.0 - 2.5 only] **git checkout MOODLE_XY_STABLE** (where XY is the moodle version, e.g: MOODLE_30_STABLE, MOODLE_28_STABLE...)
- If you are not using submodules, then:
    - **git clone git://github.com/elearningsoftware/moodle-mod_journal.git mod/journal**
    - [For Moodle 2.0 - 2.5 only] **git checkout MOODLE_XY_STABLE** (where XY is the moodle version, e.g: MOODLE_30_STABLE, MOODLE_28_STABLE...)
- Click the 'Notifications' link on the frontpage administration block or **php admin/cli/upgrade.php** if you have access to a command line interpreter.

## Install from a compressed file
- Extract the compressed file data
- Rename the main folder to journal
- Copy to the Moodle mod/ folder
- Click the 'Notifications' link on the frontpage administration block or **php admin/cli/upgrade.php** if you have access to a command line interpreter.

