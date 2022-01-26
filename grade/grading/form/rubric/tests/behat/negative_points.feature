@gradingform @gradingform_rubric @javascript
Feature: Rubrics can have levels with negative scores
  In order to use and refine rubrics to grade students
  As a teacher
  I need to be able to penalise for very wrong submissions

  Scenario: Using negative levels in rubrics
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "scales" exist:
      | name         | scale                                     |
      | Test scale 1 | Disappointing, Good, Very good, Excellent |
    And the following "activities" exist:
      | activity   | name              | intro | course | idnumber    | grade   | advancedgradingmethod_submissions |
      | assign     | Test assignment 1 | Test  | C1     | assign1     | 100     | rubric                            |
    And I change window size to "large"
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I go to "Test assignment 1" advanced grading definition page
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Rubric test description |
    And I define the following rubric:
      | Criterion 1 | Did not try | -11 | Level 12 | 25 | Level 13 | 40 | Level 14  | 50  |
      | Criterion 2 | Very bad    | -20 | Level 22 | 25 | Level 23 | 30 |           |     |
      | Criterion 3 | Level 31    | 10  | Level 32 | 20 |          |    |           |     |
    And I press "Save rubric and make it ready"
    # Grading a student.
    And I navigate to "Assignment" in current page administration
    And I go to "Student 1" "Test assignment 1" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | 25 |  |
      | Criterion 2 | 30 |  |
      | Criterion 3 | 10 |  |
    And I save the advanced grading form
    And I am on the "Test assignment 1" "assign activity" page
    And I go to "Student 2" "Test assignment 1" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | 25 |  |
      | Criterion 2 | -20 |  |
      | Criterion 3 | 10 |  |
    And I save the advanced grading form
    And I am on the "Test assignment 1" "assign activity" page
    And I go to "Student 3" "Test assignment 1" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | -11 |  |
      | Criterion 2 | -20 |  |
      | Criterion 3 | 10 |  |
    And I save the advanced grading form
    # Checking that the user grade is correct.
    And I should see "65.00" in the "student1@example.com" "table_row"
    And I should see "15.00" in the "student2@example.com" "table_row"
    And I should see "0.00" in the "student3@example.com" "table_row"
    And I should not see "-" in the "student3@example.com" "table_row"
    And I log out
