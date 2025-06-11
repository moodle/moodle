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
# Tests for animated images (GIFs, etc.) and their accessibility.
#
# @package    theme_snap
# @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
# Some scenarios will be testing AX through special steps depending on the needed rules.
# https://github.com/dequelabs/axe-core/blob/v3.5.5/doc/rule-descriptions.md#best-practices-rules.

Feature: Animated images should be accessible.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | maxbytes | enablecompletion | initsections |
      | Course 1 | C1        | 0        | topics | 500000   | 1                |       1      |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Animated images can be paused, and their animation can be resumed afterwards.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I upload file "testgif_small.gif" to section 1
    And I follow "Section 1"
    And I wait "3" seconds
    And I hover ".snap-animated-image img" "css_element"
    And I wait until ".anim-pause-button" "css_element" is visible
    Then I click on ".anim-pause-button" "css_element"
    And "img[src$='.gif']" "css_element" should not be visible
    And I hover ".snap-animated-image" "css_element"
    And I wait until ".anim-play-button" "css_element" is visible
    And I click on ".anim-play-button" "css_element"
    And "img[src$='.gif']" "css_element" should be visible
