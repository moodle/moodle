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
# Tests for enrolled courses and available courses in the front page in Snap.
#
# @package    theme_snap
# @author     Daniel Cifuentes <daniel.cifuentes@openlms.net>
# @copyright  Copyright (c) 2024 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: Correct functionality of enrolled courses and available courses in the front page in Snap

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
      | Course 2 | C2        | 0        | topics |
      | Course 3 | C3        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student1 | C2     | student        |
      | student2 | C1     | student        |
    Then I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "None"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Enrolled courses are displayed correctly in front page in Snap.
    Given I log in as "student1"
    And I am on site homepage
    And ".snap-home-course" "css_element" should not exist
    And ".snap-home-course-title" "css_element" should not exist
    And ".snap-home-courses-image" "css_element" should not exist
    And I should not see "Course 1"
    And I should not see "Course 2"
    And I should not see "Course 3"
    Then I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "Enrolled courses"
    And I press "Save changes"
    And I log out
    Then I log in as "student1"
    And I am on site homepage
    And ".snap-home-course" "css_element" should exist
    And ".snap-home-course-title" "css_element" should exist
    And ".snap-home-courses-image" "css_element" should exist
    And I should see "Course 1"
    And I should see "Course 2"
    And I should not see "Course 3"
    And I log out

  @javascript
  Scenario: Available courses are displayed correctly in front page in Snap.
    Given I log in as "student1"
    And I am on site homepage
    And ".snap-home-course" "css_element" should not exist
    And ".snap-home-course-title" "css_element" should not exist
    And ".snap-home-courses-image" "css_element" should not exist
    And I should not see "Course 1"
    And I should not see "Course 2"
    And I should not see "Course 3"
    Then I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "List of courses"
    And I press "Save changes"
    And I log out
    Then I log in as "student1"
    And I am on site homepage
    And ".snap-home-course" "css_element" should exist
    And ".snap-home-course-title" "css_element" should exist
    And ".snap-home-courses-image" "css_element" should exist
    And I should see "Course 1"
    And I should see "Course 2"
    And I should see "Course 3"
    And I log out

  @javascript
  Scenario: Hidden courses are displayed correctly in front page in Snap in My Courses and Available courses section.
    Given I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "List of courses"
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin1']" to "Enrolled courses"
    And I press "Save changes"
    And I am on site homepage
    And "Hidden from students" "text" should not exist in the "#frontpage-course-list" "css_element"
    And I am on the course main page for "C1"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id = 'id_visible']" to "Hide"
    Then I press "Save and display"
    And I am on site homepage
    And "Hidden from students" "text" should exist in the "#frontpage-course-list" "css_element"

  @javascript
  Scenario: Users can open the course cards info modal.
    Given I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "List of courses"
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin1']" to "Enrolled courses"
    And I press "Save changes"
    And I am on site homepage
    And ".more-info" "css_element" should not be visible
    And ".additional-options .coursecategory" "css_element" should not be visible
    And ".snap-home-course .favouriteicon .fa-regular" "css_element" should not be visible
    And I hover ".snap-home-course" "css_element"
    And ".more-info" "css_element" should be visible
    And ".additional-options .coursecategory" "css_element" should be visible
    And I should see "0 students"
    And ".snap-home-course .favouriteicon .fa-regular" "css_element" should be visible
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "Show course information" "button"
    And I press the enter key
    And ".snap-home-course-card" "css_element" should be visible
    And ".snap-home-course-card .studentsinfo" "css_element" should be visible
    And I should see "0 students"
    And ".snap-home-course-card .coursecategory" "css_element" should be visible
    And I should see "Lorem ipsum dolor sit amet"
    And I click on ".close" "css_element"
    And ".snap-home-course-card" "css_element" should not be visible
    And I should not see "Lorem ipsum dolor sit amet"
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C3     | student        |
      | student2 | C3     | student        |
    And I am on site homepage
    And I hover ".snap-home-course" "css_element"
    And I should see "2 students"

  @javascript
  Scenario: Users can set course as favourite from course cards in home page.
    Given I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "Enrolled courses"
    And I press "Save changes"
    And I log in as "student2"
    And I follow "My Courses"
    And ".course-card [data-region='favourite-icon'] .hidden" "css_element" should exist
    And I am on site homepage
    And I hover ".snap-home-course" "css_element"
    And ".snap-home-course .favouriteicon .set-favourite" "css_element" should be visible
    And I press the tab key
    And the focused element is "Star this course" "button"
    And I press the enter key
    And I wait until the page is ready
    And ".snap-home-course .favouriteicon .unset-favourite" "css_element" should be visible
    And I follow "My Courses"
    And ".course-card [data-region='favourite-icon'] .hidden" "css_element" should not exist
    And I am on site homepage
    And I hover ".snap-home-course" "css_element"
    And I click on ".snap-home-course .favouriteicon .unset-favourite" "css_element"
    And I wait until the page is ready
    And ".snap-home-course .favouriteicon .set-favourite" "css_element" should be visible
    And I follow "My Courses"
    And ".course-card [data-region='favourite-icon'] .hidden" "css_element" should exist

