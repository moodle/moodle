@core @core_block
Feature: Block appearances
  In order to configure blocks appearance
  As a teacher
  I need to add and modify block configuration for the page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name             | course | idnumber |
      | assign   | Test assign name | C1     | assign1  |
      | book     | Test book name   | C1     | book1    |
    And the following "mod_book > chapter" exists:
      | book    | Test book name         |
      | title   | Book title             |
      | content | Book content test test |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | comments  | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"

  Scenario: Block settings can be modified so that a block apprears on any page
    When I click on "Test assign name" "link" in the "region-main" "region"
    Then I should see "Comments" in the "Comments" "block"
    And I am on "Course 1" course homepage
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Display on page types | Any course page |
    And I press "Save changes"
    And I turn editing mode off
    And I click on "Test assign name" "link" in the "region-main" "region"
    And I should not see "Comments"

  Scenario: Block settings can be modified so that a block can be hidden
    When I click on "Test book name" "link" in the "region-main" "region"
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Visible | No |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode off
    And I click on "Test book name" "link" in the "region-main" "region"
    Then I should not see "Comments"
