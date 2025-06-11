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
# @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_color_check
Feature: When the moodle theme is set to Snap, a color contrast checker can be viewed.

  Background:
    Given I create the following course categories:
      | id | name   | category | idnumber | description |
      |  5 | Cat  5 |     0    |   CAT5   |   Test      |
      | 10 | Cat 10 |   CAT5   |   CAT10  |   Test      |
      | 20 | Cat 20 |   CAT20  |   CAT20  |   Test      |
    And the following config values are set as admin:
      | linkadmincategories | 0 |

  @javascript
  Scenario: Go to Snap settings page, put a color in theme color and see contrast message.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I click on "#theme-settings-snap" "css_element"
    And I set the following fields to these values:
      |  Site color |      #FFAAAA                   |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "This color combination doesn't comply"

  @javascript
  Scenario: Go to Snap settings page, put a valid JSON text in it and see contrast message.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I click on "#theme-settings-snap" "css_element"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette |      #FFAAFF                   |
      |    JSON Text   | {"5":"#FAAFFF","10":"#FABCF0"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I click on "Category color" "link"
    And I should see "The following color categories don't comply"
    And I should see "Against site background color (white): \"5, 10\""

  @javascript
  Scenario: Go to Snap settings page, put a valid JSON text in it and don't see contrast message.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I click on "#theme-settings-snap" "css_element"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the field "JSON Text" to "aaa"
    And I set the following fields to these values:
      |  Color palette |      #FFAAFF                   |
      |    JSON Text   | {"5":"#000000","10":"#000000"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I click on "Category color" "link"
    And I should not see "The following color categories don't comply"
