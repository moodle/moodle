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
# Test the Category color setting for snap.
#
# @package   theme_snap
# @copyright Copyright (c) 2023 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: The setting Enable Advanced feeds should be available only when at least one of the options Deadlines, Feedback and grading, Messages or Forum posts is selected.

  @javascript
  Scenario: Go to Snap Snap feeds settings page and enable any of the dependency options
    Given I log in as "admin"
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Snap feeds"
    And I click on "Snap feeds" "link" in the "#snap-admin-tabs" "css_element"
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 1 |
      | Feedback and grading  | 0 |
      | Messages  | 0 |
      | Forum posts  | 0 |
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 0 |
      | Feedback and grading  | 1 |
      | Messages  | 0 |
      | Forum posts  | 0 |
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 0 |
      | Feedback and grading  | 0 |
      | Messages  | 1 |
      | Forum posts  | 0 |
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 0 |
      | Feedback and grading  | 0 |
      | Messages  | 0 |
      | Forum posts  | 1 |
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 1 |
      | Feedback and grading  | 1 |
      | Messages  | 1 |
      | Forum posts  | 1 |
    And I should see "Enable Advanced feeds"
    And I set the following fields to these values:
      | Deadlines  | 0 |
      | Feedback and grading  | 0 |
      | Messages  | 0 |
      | Forum posts  | 0 |
    And I should not see "Enable Advanced feeds"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Changes saved"
    And I click on "Snap feeds" "link" in the "#snap-admin-tabs" "css_element"
    And I should not see "Enable Advanced feeds"
