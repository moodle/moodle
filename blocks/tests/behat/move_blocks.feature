@core @core_block
Feature: Block region moving
  In order to configure blocks appearance
  As a teacher
  I need to modify block region for the page

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
      | activity | course | name             | template |
      | survey   | C1     | Test survey name | 4        |
      | book     | C1     | Test book name   |          |
    And the following "mod_book > chapter" exists:
      | book    | Test book name         |
      | title   | Book title             |
      | content | Book content test test |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Comments" block
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"

  Scenario: Block settings can be modified so that a block can be moved
    When I follow "Test book name"
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Region  | Right |
    And I press "Save changes"
    And I should see "Comments" in the "//*[@id='region-post' or @id='block-region-side-post']" "xpath_element"
