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
# Tests for sending messages in snap.
#
# @package    theme_snap
# @autor      Rafael Monterroza rafael.monterroza@openlms.net
# @copyright  Copyright (c) 2019 OpenLMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_message @javascript
Feature: Snap message send messages
  As a user
  I need to be able to send a message

  Background:
    Given I create the following course categories:
      | id | name   | category | idnumber | description |
      |  5 | Cat 5  |     0    |   CAT5   |   Test      |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | CAT5     | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1 |
      | student2 | G1 |
    And the following config values are set as admin:
      | messaging        | 1 |
      | messagingminpoll | 1 |

  Scenario: Send a message to a group conversation in snap
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "//span[contains(text(),\"Group\")]" "xpath_element"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    When I send "Hi!" message in the message area
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I should see "1" in the ".section[data-region='view-overview-group-messages'] small[data-region='section-total-count-container'] span[data-region='section-total-count']" "css_element"
    And I should see "There are 1 unread conversations" in the "#view-overview-group-messages-unread-count-label" "css_element"
    And I should see "1" in the ".badge[data-region='unread-count']" "css_element"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    Then ".badge.hidden[data-region='unread-count']" "css_element" should exist
    Then "span#view-overview-group-messages-unread-count-label:contains('There are 1 unread conversations')" "css_element" should exist

  Scenario: Send a message to a starred conversation in snap
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "//span[contains(text(),\"Group\")]" "xpath_element"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Star conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I click on "//span[contains(text(),\"Starred\")]" "xpath_element"
    And I should see "Group 1"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    And I send "Hi!" message in the message area
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    And I click on "//span[contains(text(),\"Group\")]" "xpath_element"
    And I should see "No group conversations"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I should see "1" in the ".section[data-region='view-overview-favourites'] span[data-region='section-total-count']" "css_element"
    And I should see "There are 1 unread conversations" in the "#view-overview-group-messages-unread-count-label" "css_element"
    And I should see "1" in the ".badge[data-region='unread-count'] span" "css_element"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    Then "//*[@data-region='unread-count']/span[contains(text(),'There are  unread messages')]" "xpath_element" should exist
    Then "span#view-overview-group-messages-unread-count-label:contains('There are 1 unread conversations')" "css_element" should exist

  Scenario: Send a message to a private conversation via contacts and check unread messages is updated in snap.
    Given the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
      | student3 | student2 |
    And I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Contacts" "link"
    And I click on "Student 2" "link" in the "//*[@data-section='contacts']" "xpath_element"
    When I send "Hi!" message in the message area
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    And I log out
    And I log in as "student3"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Contacts" "link"
    And I click on "Student 2" "link" in the "//*[@data-section='contacts']" "xpath_element"
    When I send "Hello!" message in the message area
    Then I should see "Hello!" in the ".d-flex[data-region='day-messages-container']" "css_element"
    When I send "How are you?" message in the message area
    Then I should see "How are you?" in the ".d-flex[data-region='day-messages-container']" "css_element"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I should see "2" in the ".section[data-region='view-overview-messages'] span[data-region='section-total-count']" "css_element"
    And I should see "There are 2 unread conversations" in the "#view-overview-messages-unread-count-label" "css_element"
    And I should see "1" in the "Student 1" "core_message > Message"
    And I should see "2" in the "Student 3" "core_message > Message"
    And I click on ".rounded-circle[alt='Student 3']" "css_element"
    And I should see "Hello!" in the ".d-flex[data-region='day-messages-container']" "css_element"
    And I should see "How are you?" in the ".d-flex[data-region='day-messages-container']" "css_element"
    Then "//*[@data-region='unread-count']/span[contains(text(),'There are  unread messages')]" "xpath_element" should exist
    And I click on ".rounded-circle[alt='Student 1']" "css_element"
    Then I should see "Hi!" in the ".d-flex[data-region='day-messages-container']" "css_element"
    Then ".badge.hidden[data-region='unread-count']" "css_element" should exist
    Then "span#view-overview-messages-unread-count-label:contains('There are 2 unread conversations')" "css_element" should exist

  Scenario: Message bubble should have a specific color instead of site color.
    Given I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "//span[contains(text(),\"Group\")]" "xpath_element"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    And I click on "conversation-actions-menu-button" "button"
    And I click on "Star conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I click on "//span[contains(text(),\"Starred\")]" "xpath_element"
    And I should see "Group 1"
    And I click on ".rounded-circle[alt='Group 1']" "css_element"
    And I send "Hi!" message in the message area
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    And I check element ".message-app .message.send" with property "background-color" = "#E6E6E6"
    And I check element ".message-app .message.send .tail" with property "border-bottom-color" = "#E6E6E6"

  Scenario: Send a message from course participants.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "#snap-course-tools" "css_element"
    And I click on "#ct-participants-number" "css_element"
    Then I wait until the page is ready
    Then I click on "//a[contains(text(),'Student 2')]" "xpath_element"
    Then I wait until the page is ready
    And I click on "#message-user-button" "css_element"
    Then "//div[contains(@class, 'header-container')]//strong[contains(text(), 'Student 2')]" "xpath_element" should be visible

  Scenario: Opening a direct message through the personal menu should open the message directly in the message page.
    Given the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
      | student3 | student2 |
    And I log in as "student1"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "//a[@title='View my messages']/*[local-name()='svg']" "xpath_element"
    And I click on "Contacts" "link"
    And I click on "Student 2" "link" in the "//*[@data-section='contacts']" "xpath_element"
    When I send "Hi!" message in the message area
    Then I should see "Hi!" in the ".message.clickable[data-region='message']" "css_element"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "#snap-sidebar-menu-feed-messages > div > div > a" "css_element"
    # To check that the message is opened directly.
    And I should see "Hi!" in the "//div[@class='body-container position-relative']//div[@data-region='view-conversation']//div[@data-region='content-message-container']//div[@data-region='message']//div[@data-region='text-container']//p" "xpath_element"
