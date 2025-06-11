# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests for settings link.
#
# @package    theme_snap
# @copyright  2024 Daniel Cifuentes
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the admin user navigates to the theme selector page in the Site administration, the Snap option is correctly displayed.

  Background:
    Given the following config values are set as admin:
      | linkadmincategories | 1 |

  @javascript
  Scenario: The Snap theme description is correctly displayed in the theme selector.
    Given I log in as "admin"
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I follow "Themes"
    And I click on "#theme-preview-snap" "css_element"
    And I should see "Snap's user-friendly and responsive design"

