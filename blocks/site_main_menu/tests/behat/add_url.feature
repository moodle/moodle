@block @block_site_main_menu
Feature: Add URL to main menu block
  In order to add helpful resources for students
  As a admin
  I need to add URLs to the main menu block and check it works.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
      | Course 2 | C2        | 0        |                  |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | System       | 1         | site-index      | side-pre      |

  @javascript
  Scenario: Add a URL in menu block and ensure it appears
    Given the following "activity" exists:
      | activity    | url                   |
      | course      | Acceptance test site  |
      | name        | google        |
      | intro       | gooooooooogle         |
      | externalurl | http://www.google.com |
      | section     | 0                     |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I am on the "google" "url activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_display | In pop-up |
    And I press "Save and return to course"
    Then "google" "link" should exist in the "Main menu" "block"
    And "Add an activity" "button" should exist in the "Main menu" "block"
    And "Add an activity or resource" "button" should exist in the "Main menu" "block"
