@gradingform @gradingform_guide @javascript
Feature: Display marking guide information to students
  In order for students to see the marking guide information
  As a teacher
  I should be able to change display settings for marking guide information

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name                                 | Assign 1 marking guide    |
      | Description                          | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade Criteria 1  | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade Criteria 2  | Grade 2 description for students | Grade 2 description for markers | 30            |
    And I press "Save marking guide and make it ready"

  Scenario: Confirm that marking guide information is not displayed after student is graded
    # Update the existing marking guide to ensure that marks per criterion is displayed.
    Given I click on "Edit the current form definition" "link"
    And I set the field "Show marks per criterion to students" to "0"
    And I press "Save"
    And I am on the "Assign 1" "assign activity" page
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 1 | 50 | Excellent work! |
      | Grade Criteria 2 | 20 | Try harder      |
    And I press "Save changes"
    When I am on the "Assign 1" "assign activity" page logged in as student1
    # Confirm the marking guide information display after student is graded when marking per criterion display is disabled.
    # Confirm that overall grade is displayed.
    Then I should see "70.00 / 100.00"
    And I should see the marking guide information displayed as:
      | criteria         | description                      | remark          |
      | Grade Criteria 1 | Grade 1 description for students | Excellent work! |
      | Grade Criteria 2 | Grade 2 description for students | Try harder      |

  Scenario: Confirm that marking guide information is displayed after student is graded
    Given I am on the "Assign 1" "assign activity" page logged in as student1
    And I should see "Grade 1 description for students" in the "Grade Criteria 1" "table_row"
    And I should see "Grade 2 description for students" in the "Grade Criteria 2" "table_row"
    # No grade to student1 yet.
    And I should not see "70.00 / 100.00"
    # No need to update marking guide as marking guide definition is already enabled by default
    And I am on the "Assign 1" "assign activity" page logged in as teacher1
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 1 | 50 | Excellent work! |
      | Grade Criteria 2 | 20 | Try harder      |
    And I press "Save changes"
    When I am on the "Assign 1" "assign activity" page logged in as student1
    # Student1 grade is now displayed.
    Then I should see "70.00 / 100.00"
    And I should see the marking guide information displayed as:
      | criteria         | description                      | remark          | maxscore | criteriascore |
      | Grade Criteria 1 | Grade 1 description for students | Excellent work! | 70       | 50 / 70       |
      | Grade Criteria 2 | Grade 2 description for students | Try harder      | 30       | 20 / 30       |

  Scenario: Confirm that marking guide definition is retained when grading method is changed
    Given I am on the "Assign 1" "assign activity" page
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 1 | 70 | Well done! |
      | Grade Criteria 2 | 20 | Great work |
    And I press "Save changes"
    And I am on the "Assign 1" "assign activity editing" page
    And I set the following fields to these values:
      | Grading method | Simple direct grading |
    And I press "Save and return to course"
    When I go to "Assign 1" advanced grading page
    Then I should not see "Assign 1 marking guide Ready for use"
    And I should not see "Grade Critera 1"
    And I should not see "Grade Critera 2"
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3- |
      | Student 1 | student1@example.com | 90  |
    And I am on the "Assign 1" "assign activity editing" page
    And I set the following fields to these values:
      | Grading method | Marking guide |
    And I press "Save and return to course"
    And I go to "Assign 1" advanced grading page
    And I should see "Assign 1 marking guide Ready for use"
    And I should see "Grade Criteria 1"
    And I should see "Grade Criteria 2"
