@block @block_site_main_menu
Feature: Add URL to Additional activities block
  In order to add helpful resources for students
  As a admin
  I need to add URLs to the Additional activities block and check it works.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
      | Course 2 | C2        | 0        |                  |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | System       | 1         | site-index      | side-pre      |

  @javascript
  Scenario: Add a URL in Additional activities block and ensure it appears
    Given I log in as "admin"
    And I am on site homepage
    And the following "activity" exists:
      | activity    | url                   |
      | course      | Acceptance test site  |
      | name        | reference link        |
      | intro       | mooooooooodle         |
      | externalurl | http://www.moodle.com |
      | section     | 0                     |
    When I am on the "reference link" "url activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_display | In pop-up |
    And I press "Save and return to course"
    And I turn editing mode on
    Then "reference link" "link" should exist in the "Additional activities" "block"
    And "Add an activity or resource" "button" should exist in the "Additional activities" "block"

  @javascript
  Scenario: Add a URL in Additional activities block can appear in any site page
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I configure the "Additional activities" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And the following "activity" exists:
      | activity    | url                   |
      | course      | Acceptance test site  |
      | name        | reference link        |
      | intro       | mooooooooodle         |
      | externalurl | http://www.moodle.com |
      | section     | 0                     |
    And I am on the "reference link" "url activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_display | Embed |
    And I press "Save and return to course"
    Then I click on "reference link" "link" in the "Additional activities" "block"
    And "reference link" "link" should exist in the "Additional activities" "block"
    And I am on the "C1" "Course" page
    And "reference link" "link" should not exist in the "Additional activities" "block"
    And I navigate to "Badges > Add a new badge" in site administration
    And "reference link" "link" should exist in the "Additional activities" "block"

  @javascript
  Scenario: Add a URL in menu block can appear in any front page
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I configure the "Additional activities" block
    And I set the following fields to these values:
      | Page contexts | Display on the site home and any pages added to the site home. |
    And I press "Save changes"
    And the following "activity" exists:
      | activity    | url                   |
      | course      | Acceptance test site  |
      | name        | reference link        |
      | intro       | mooooooooodle         |
      | externalurl | http://www.moodle.com |
      | section     | 0                     |
    And I am on the "reference link" "url activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_display | Embed |
    And I press "Save and return to course"
    Then I click on "reference link" "link" in the "Additional activities" "block"
    And "reference link" "link" should exist in the "Additional activities" "block"
    And I am on the "C1" "Course" page
    And "Additional activities" "block" should not exist
    And I navigate to "Badges > Add a new badge" in site administration
    And "Additional activities" "block" should not exist

  @javascript
  Scenario: When the Additional activities block is displayed throrought the entire site, adding an URL in a course
  results in adding it in the course and not in the frontpage
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I configure the "Additional activities" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    When I am on the "C2" "Course" page
    And I click on "Add content" "button" in the "Additional activities" "block"
    And I click on "Activity or resource" "button" in the ".dropdown-menu.show" "css_element"
    And I click on "Add a new URL" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Add selected activity" "button" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | name            | reference link        |
      | externalurl     | http://www.moodle.com |
    And I press "Save and return to course"
    Then "reference link" "link" should exist in the "Additional activities" "block"
    And I should see "reference link" in the "region-main" "region"
    And I am on site homepage
    And "reference link" "link" should not exist in the "Additional activities" "block"
