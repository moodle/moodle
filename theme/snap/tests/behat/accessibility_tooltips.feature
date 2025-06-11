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
# Testing for accessibility scenarios in tooltips
#
# @package    theme_snap
# @author     Dayana Pardo <dayana.pardo@openlms.net>
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@javascript @theme @theme_snap
Feature: Accessible tooltips in the course creation/edit form

  In order to get contextual help in an accessible way
  As an administrator
  I want to interact with tooltips using mouse and keyboard

  Background:
    Given the following config values are set as admin:
      | theme | snap |
    And I log in as "admin"
    And I go to link "/course/edit.php?category=1"

  Scenario: Tooltip for "Course full name" is shown and hidden when clicking
    When I click on "img[alt='Help with Course full name']" "css_element"
    Then I should see "The name displayed in My courses"

    When I click on "img[alt='Help with Course full name']" "css_element"
    Then I should not see "The name displayed in My courses"

  Scenario: Multiple tooltips remain open
    When I click on "img[alt='Help with Course full name']" "css_element"
    And I click on "img[alt='Help with Course short name']" "css_element"
    Then I should see "The name displayed in My courses"
    And I should see "It must be unique"

  Scenario: Tooltip can be toggled with keyboard (Enter, Space)
    When I click on "img[alt='Help with Course full name']" "css_element"
    And the focused element is "//button[.//img[@alt='Help with Course full name']]" "xpath_element"

    And I press the enter key
    Then I should not see "The name displayed in My courses"

    And I press the enter key
    Then I should see "The name displayed in My courses"

    When I press the space key
    Then I should not see "The name displayed in My courses"

    When I press the space key
    Then I should see "The name displayed in My courses"

