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
# Tests for managing contacts in snap.
#
# @package    theme_snap
# @autor      Rafael Monterroza rafael.monterroza@openlms.net
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_message @javascript
Feature: Snap managing contacts
  In order to chat with fellow users
  As a user
  I need to be able to add and remove users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | course1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
    And the following config values are set as admin:
      | messaging         | 1 |
      | messagingallusers | 1 |
      | messagingminpoll  | 1 |

  Scenario: Send a 'contact request' to add a contact in snap
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Search" "field"
    And I set the field with xpath "//*[@data-region='search-input']" to "Student 4"
    And I click on ".btn-submit[data-action='search']" "css_element"
    And I wait until "span.matchtext" "css_element" is visible
    And I click on "span.matchtext" "css_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Add to contacts" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I click on "Add" "button"
    And I log out
    And I log in as "student3"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Search" "field"
    And I set the field with xpath "//*[@data-region='search-input']" to "Student 4"
    And I click on ".btn-submit[data-action='search']" "css_element"
    And I wait until "span.matchtext" "css_element" is visible
    And I click on "span.matchtext" "css_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Add to contacts" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I click on "Add" "button"
    And I log out
    And I log in as "student4"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    Then I should see "2" in the ".bg-primary[data-region='contact-request-count']" "css_element"
    And I click on "Contacts" "link"
    Then I should see "2" in the "//div[@data-region='view-contacts']//*[@data-region='contact-request-count']" "xpath_element"
    And I click on "Requests" "link_or_button"
    And I click on "Student 1 Would like to contact you" "link"
    Then I should see "Accept and add to contacts"
    And I click on "Accept and add to contacts" "link_or_button"
    And I should not see "Accept and add to contacts"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Contacts" "link"
    Then I should see "Student 4" in the "//*[@data-section='contacts']" "xpath_element"

  Scenario: Decline a 'contact request' in snap
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Search" "field"
    And I set the field with xpath "//*[@data-region='search-input']" to "Student 3"
    And I click on ".btn-submit[data-action='search']" "css_element"
    And I wait until "span.matchtext" "css_element" is visible
    And I click on "span.matchtext" "css_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Add to contacts" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I click on "Add" "button"
    And I log out
    And I log in as "student3"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    Then I should see "1" in the ".bg-primary[data-region='contact-request-count']" "css_element"
    And I click on "Contacts" "link"
    Then I should see "1" in the "//div[@data-region='view-contacts']//*[@data-region='contact-request-count']" "xpath_element"
    And I click on "Requests" "link_or_button"
    And I click on "Student 1 Would like to contact you" "link"
    Then I should see "Accept and add to contacts"
    And I click on "Decline" "link_or_button"
    Then I should not see "Accept and add to contacts"

  Scenario: Remove existing contact in snap
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Contacts" "link"
    And I click on "Student 2" "link" in the "//*[@data-section='contacts']" "xpath_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Remove from contacts" "link"
    And I click on "Remove" "button"
    And I go back in "view-conversation" message drawer
    And I should see "No contacts"
