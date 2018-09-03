@block @block_activity_results
Feature: The activity results block doesn't displays student scores for unconfigured block
  In order to be display student scores
  As a user
  I need to see the activity results block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Add the block to a the course
    Given I add the "Activity results" block
    Then I should see "Please configure this block and select which activity it should display results from." in the "Activity results" "block"

  Scenario: Try to configure the block on the course page in a course without activities
    Given I add the "Activity results" block
    When I configure the "Activity results" block
    And I should see "There are not yet any activities in this course."
    And I press "Save changes"
    Then I should see "Please configure this block and select which activity it should display results from." in the "Activity results" "block"

  Scenario: Try to configure the block on a resource page in a course without activities
    Given I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name | Test page name |
      | Description | Test page description |
      | page | This is a page |
    And I press "Save and display"
    When I add the "Activity results" block
    And I configure the "Activity results" block
    And I should see "There are not yet any activities in this course."
    And I press "Save changes"
    Then I should see "Please configure this block and select which activity it should display results from." in the "Activity results" "block"
