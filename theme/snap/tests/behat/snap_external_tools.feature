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
# along with Moodle. If not, see <http://www.gnu.org/licenses/>.
#
# Tests for Calendar's anchors aria-label attribute
#
# @package    theme_snap
# @autor      Rafael Becerra
# @copyright  Copyright (c) 2020 Open LMS (http://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @mod_lti @theme_snap_lti
Feature: Configure new external tool type to test it on a course.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I log in as "admin"
    And I am on front page
    And I go to "Site administration > Plugins > Activity modules > External tool > Manage tools" in snap administration
    # Create tool type that opens in a new window.
    And I follow "configure a tool manually"
    And I set the field "Tool name" to "Teaching Tool 1"
    And I set the field "Tool URL" to local url "/mod/lti/tests/fixtures/tool_provider.php"
    And I set the field "Tool configuration usage" to "Show in activity chooser and as a preconfigured tool"
    And I expand all fieldsets
    And I set the field "Default launch container" to "4"
    And I press "Save changes"

  @javascript
  Scenario: External tool is opened in a new window.
    # We need to be sure that a LTI configured to be opened in a new window is opened in a new window on click.
    # This will be Snap's insurance, so any type of LTI works the same.
    And I am on the course main page for "C1"
    And I click on "button.section-modchooser-link" "css_element"
    And I follow "Teaching Tool 1"
    And I set the field "Activity name" to "External tool test"
    And I press "Save and return to course"
    And I click on "li.modtype_lti a.mod-link" "css_element"
    And The document should open in a new tab
    And I should see "This represents a tool provider"
