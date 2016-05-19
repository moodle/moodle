@block @block_completionstatus
Feature: Enable Block Completion in a course using manual completion by others
  In order to view the completion block in a course
  As a teacher
  I can add completion block to a course and set up manual completion by others

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | teacher2 | Teacher | 2 | teacher1@example.com | T2 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enablecompletion | 1 |

  Scenario: Add the block to a the course and mark a student complete.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course completion status" block
    And I navigate to "Course completion" node in "Course administration"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Teacher | 1 |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "No" in the "Teacher" "table_row"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Course completion" node in "Reports"
    And I follow "Click to mark user complete"
    # Running completion task just after clicking sometimes fail, as record
    # should be created before the task runs.
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I am on site homepage
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "Yes" in the "Teacher" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Marked complete by Teacher" "table_row"


  Scenario: Add the block to a the course and require multiple roles to mark a student complete.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course completion status" block
    And I navigate to "Course completion" node in "Course administration"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Teacher             | 1 |
      | Non-editing teacher | 1 |
      | id_role_aggregation | ALL selected roles to mark when the condition is met |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "No" in the "Teacher" "table_row"
    And I should see "No" in the "Non-editing teacher" "table_row"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Course completion" node in "Reports"
    And I follow "Click to mark user complete"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Status: In progress" in the "Course completion status" "block"
    And I should see "Yes" in the "Teacher" "table_row"
    And I should see "No" in the "Non-editing teacher" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Marked complete by Teacher" "table_row"
    And I should see "No" in the "Marked complete by Non-editing teacher" "table_row"
    And I log out
    And I log in as "teacher2"
    And I follow "Course 1"
    And I navigate to "Course completion" node in "Reports"
    And I follow "Click to mark user complete"
    # Running completion task just after clicking sometimes fail, as record
    # should be created before the task runs.
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I am on site homepage
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "Yes" in the "Teacher" "table_row"
    And I should see "Yes" in the "Non-editing teacher" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Marked complete by Teacher" "table_row"
    And I should see "Yes" in the "Marked complete by Non-editing teacher" "table_row"
