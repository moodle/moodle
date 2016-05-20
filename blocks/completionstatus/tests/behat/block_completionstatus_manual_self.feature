@block @block_completionstatus @block_selfcompletion
Feature: Enable Block Completion in a course using manual self completion
  In order to view the completion block in a course
  As a teacher
  I can add completion block to a course and set up manual self completion

  Scenario: Add the block to a the course and manually complete the course
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enablecompletion | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course completion status" block
    And I add the "Self completion" block
    And I navigate to "Course completion" node in "Course administration"
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_criteria_self | 1 |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "No" in the "Self completion" "table_row"
    And I follow "Complete course"
    And I should see "Confirm self completion"
    And I press "Yes"
    And I should see "Status: In progress" in the "Course completion status" "block"
    # Running completion task just after clicking sometimes fail, as record
    # should be created before the task runs.
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "Yes" in the "Self completion" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Self completion" "table_row"
