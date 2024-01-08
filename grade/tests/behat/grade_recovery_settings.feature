@core @core_grades @javascript
Feature: Admin can set Recover grades default setting
  In order to recover grades
  As an admin
  I need to enable "Recover grades default" from site administration

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | course | name     |
      | assign   | C1     | Assign 1 |

  Scenario Outline: Recover grades default setting can be changed
    Given the following config values are set as admin:
      | recovergradesdefault  | <recovergradesetting>  |
    # Grade student 1 via quick grading
    And I am on the "Assign 1" "assign activity" page logged in as admin
    And I follow "View all submissions"
    And I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "60.00"
    And I press "Save all quick grading changes"
    # Confirm that assigned grade was saved
    And I am on the "Course 1" "grades > Grader report > View" page
    And I should see "60.00" in the "Student One" "table_row"
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "Student One" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    And I press "Enrol users"
    And I set the field "Select users" to "student1"
    # Confirm the "Recover user's old grades if possible" checkbox state based on Recover grades default setting
    When I click on "Show more..." "link"
    Then the field "Recover user's old grades if possible" matches value "<oldgraderecover>"
    # Confirm that "Recover user's old grades if possible" checkbox state can be changed manually
    And I click on "Recover user's old grades if possible" "checkbox" in the "Enrol users" "dialogue"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    # Confirm whether re-enrolled student's grade is recovered or not based on student enrolment settings
    And I am on the "Course 1" "grades > Grader report > View" page
    And I <gradevisibility> see "60.00" in the "Student One" "table_row"

    Examples:
      | recovergradesetting | oldgraderecover | gradevisibility |
      | 0                   | 0               | should          |
      | 1                   | 1               | should not      |
