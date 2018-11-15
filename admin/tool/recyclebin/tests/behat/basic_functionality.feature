@tool @tool_recyclebin
Feature: Basic recycle bin functionality
  As a teacher
  I want be able to recover deleted content and manage the recycle bin content
  So that I can fix an accidental deletion and clean the recycle bin

  Background: Course with teacher exists.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher@asd.com |
      | student1 | Student | 1 | student@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
      | Course 2 | C2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C2 | editingteacher |
      | student1 | C2 | student |
      | student2 | C2 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group A | C2 | G1 |
      | Group B | C2 | G2 |
      | Group C | C2 | G3 |
    And the following "group members" exist:
      | user | group |
      | teacher1 | G1 |
      | teacher1 | G2 |
      | student1 | G1 |
      | student2 | G1 |
      | student2 | G2 |
    And the following config values are set as admin:
      | coursebinenable | 1 | tool_recyclebin |
      | categorybinenable | 1 | tool_recyclebin |
      | coursebinexpiry | 604800 | tool_recyclebin |
      | categorybinexpiry | 1209600 | tool_recyclebin |
      | autohide | 0 | tool_recyclebin |

  Scenario: Restore a deleted assignment
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assign |
      | Description | Test |
    And I delete "Test assign" activity
    When I navigate to "Recycle bin" in current page administration
    Then I should see "Test assign"
    And I should see "Contents will be permanently deleted after 7 days"
    And I click on "Restore" "link" in the "region-main" "region"
    And I should see "'Test assign' has been restored"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I should see "Test assign" in the "Topic 1" "section"

  Scenario: Restore a deleted course
    Given I log in as "admin"
    And I go to the courses management page
    And I click on "delete" action for "Course 2" in management course listing
    And I press "Delete"
    And I should see "Deleting C2"
    And I should see "C2 has been completely deleted"
    And I press "Continue"
    And I am on course index
    And I should see "Course 1"
    And I should not see "Course 2"
    When I navigate to "Recycle bin" in current page administration
    Then I should see "Course 2"
    And I should see "Contents will be permanently deleted after 14 days"
    And I click on "Restore" "link" in the "region-main" "region"
    And I should see "'Course 2' has been restored"
    And I wait to be redirected
    And I go to the courses management page
    And I should see "Course 2" in the "#course-listing" "css_element"
    And I am on "Course 2" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I follow "Overview"
    And "Student 1" "text" should exist in the "Group A" "table_row"
    And "Student 2" "text" should exist in the "Group A" "table_row"
    And "Student 2" "text" should exist in the "Group B" "table_row"

  @javascript
  Scenario: Deleting a single item from the recycle bin
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assign |
      | Description | Test |
    And I delete "Test assign" activity
    And I run all adhoc tasks
    And I navigate to "Recycle bin" in current page administration
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected item from the recycle bin?"
    And I click on "Cancel" "button" in the "Confirmation" "dialogue"
    And I should see "Test assign"
    And I click on "Delete" "link"
    And I press "Yes"
    And I should see "'Test assign' has been deleted"
    And I should see "There are no items in the recycle bin."

  @javascript
  Scenario: Deleting all the items from the recycle bin
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assign 1 |
      | Description | Test 1 |
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assign 2 |
      | Description | Test 2 |
    And I delete "Test assign 1" activity
    And I delete "Test assign 2" activity
    And I run all adhoc tasks
    And I navigate to "Recycle bin" in current page administration
    And I should see "Test assign 1"
    And I should see "Test assign 2"
    When I click on "Delete all" "link"
    Then I should see "Are you sure you want to delete all items from the recycle bin?"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"
    And I should see "Test assign 1"
    And I should see "Test assign 2"
    And I click on "Delete all" "link"
    And I press "Yes"
    And I should see "Recycle bin has been emptied"
    And I should see "There are no items in the recycle bin."
