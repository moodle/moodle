@report @report_progress
Feature: Teacher can view and override users' activity completion data via the progress report.
  In order to view and override a student's activity completion status
  As a teacher
  I need to view the course progress report and click the respective completion status icon

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "activities" exist:
      | activity   | name            | intro   | course | idnumber    | section | completion | completionview | completionusegrade | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign     | my assignment   | A1 desc | C1     | assign1     | 0       | 1          | 0              |                    | 0                                   | 0                |
      | assign     | my assignment 2 | A2 desc | C1     | assign2     | 0       | 2          | 1              |                    | 0                                   | 0                |
      | assign     | my assignment 3 | A3 desc | C1     | assign3     | 0       | 2          | 1              | 1                  | 1                                   | 0                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher1@example.com |
      | student1 | Student | One | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  # Course comprising one activity with auto completion (student must view it) and one with manual completion.
  # This confirms that after being completed by the student and overridden by the teacher, that both activities can still be
  # completed again via normal mechanisms.
  @javascript
  Scenario: Given the status has been overridden, when a student tries to complete it again, completion can still occur.
    # Student completes the activities, manual and automatic completion.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And "Not completed: my assignment. Select to mark as complete." "icon" should exist in the "my assignment" "list_item"
    And "Not completed: my assignment 2" "icon" should exist in the "my assignment 2" "list_item"
    And I click on "Not completed: my assignment. Select to mark as complete." "icon"
    And "Completed: my assignment. Select to mark as not complete." "icon" should exist in the "my assignment" "list_item"
    And I click on "my assignment 2" "link"
    And I am on "Course 1" course homepage
    And "Completed: my assignment 2" "icon" should exist in the "my assignment 2" "list_item"
    And I log out
    # Teacher overrides the activity completion statuses to incomplete.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    And "Student One, my assignment: Completed" "icon" should exist in the "Student One" "table_row"
    And "Student One, my assignment 2: Completed" "icon" should exist in the "Student One" "table_row"
    And I click on "my assignment" "link" in the "Student One" "table_row"
    And I click on "Save changes" "button"
    And "Student One, my assignment: Not completed (set by Teacher One)" "icon" should exist in the "Student One" "table_row"
    And I click on "my assignment 2" "link" in the "Student One" "table_row"
    And I click on "Save changes" "button"
    And "Student One, my assignment 2: Not completed (set by Teacher One)" "icon" should exist in the "Student One" "table_row"
    And I log out
    # Student can now complete the activities again, via normal means.
    Then I log in as "student1"
    And I am on "Course 1" course homepage
    And "Not completed: my assignment (set by Teacher One). Select to mark as complete." "icon" should exist in the "my assignment" "list_item"
    And "Not completed: my assignment 2 (set by Teacher One)" "icon" should exist in the "my assignment 2" "list_item"
    And I click on "Not completed: my assignment (set by Teacher One). Select to mark as complete." "icon"
    And "Completed: my assignment. Select to mark as not complete." "icon" should exist in the "my assignment" "list_item"
    And I click on "my assignment 2" "link"
    And I am on "Course 1" course homepage
    And "Completed: my assignment 2" "icon" should exist in the "my assignment 2" "list_item"
    And I log out
    # And the activity completion report should show the same.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    And "Student One, my assignment: Completed" "icon" should exist in the "Student One" "table_row"
    And "Student One, my assignment 2: Completed" "icon" should exist in the "Student One" "table_row"

  # Course comprising one activity with auto completion (student must view it and receive a grade) and one with manual completion.
  # This confirms that after being overridden to complete by the teacher, that the completion status for activities with automatic
  # completion can no longer be affected by any normal completion mechanisms triggered by the student. Manual completion unaffected.
  @javascript
  Scenario: Given the status has been overridden to complete, when a student triggers completion updates, the status remains fixed.
    # When the teacher overrides the activity completion statuses to complete.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    And "Student One, my assignment: Not completed" "icon" should exist in the "Student One" "table_row"
    And "Student One, my assignment 3: Not completed" "icon" should exist in the "Student One" "table_row"
    And I click on "my assignment" "link" in the "Student One" "table_row"
    And I click on "Save changes" "button"
    And "Student One, my assignment: Completed (set by Teacher One)" "icon" should exist in the "Student One" "table_row"
    And I click on "my assignment 3" "link" in the "Student One" "table_row"
    And I click on "Save changes" "button"
    And "Student One, my assignment 3: Completed (set by Teacher One)" "icon" should exist in the "Student One" "table_row"
    And I log out
    # Then as a student, confirm that automatic completion checks are no longer triggered (such as after an assign submission).
    Then I log in as "student1"
    And I am on "Course 1" course homepage
    And "Completed: my assignment 3 (set by Teacher One)" "icon" should exist in the "my assignment 3" "list_item"
    And I click on "my assignment 3" "link"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I am on "Course 1" course homepage
    And "Completed: my assignment 3 (set by Teacher One)" "icon" should exist in the "my assignment 3" "list_item"
    # And Confirm that manual completion changes are still allowed.
    And I am on "Course 1" course homepage
    And "Completed: my assignment (set by Teacher One). Select to mark as not complete." "icon" should exist in the "my assignment" "list_item"
    And I click on "Completed: my assignment (set by Teacher One). Select to mark as not complete." "icon"
    And "Not completed: my assignment. Select to mark as complete." "icon" should exist in the "my assignment" "list_item"
    And I log out
