@gradingform @gradingform_rubric
Feature: Rubrics can be created and edited
  In order to use and refine rubrics to grade students
  As a teacher
  I need to edit previously used rubrics

  @javascript
  Scenario: I can use rubrics to grade and edit them later updating students grades
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity                          | assign                      |
      | course                            | C1                          |
      | section                           | 1                           |
      | name                              | Test assignment 1 name      |
      | intro                             | Test assignment description |
      | assignfeedback_comments_enabled   | 1                           |
      | assignfeedback_editpdf_enabled    | 1                           |
      | advancedgradingmethod_submissions | rubric                      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I go to "Test assignment 1 name" advanced grading definition page
    # Defining a rubric.
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Rubric test description |
    And I define the following rubric:
      | TMP Criterion 1 | TMP Level 11 | 11 | TMP Level 12 | 12 |
      | TMP Criterion 2 | TMP Level 21 | 21 | TMP Level 22 | 22 |
      | TMP Criterion 3 | TMP Level 31 | 31 | TMP Level 32 | 32 |
      | TMP Criterion 4 | TMP Level 41 | 41 | TMP Level 42 | 42 |
    # Checking that only the last ones are saved.
    And I define the following rubric:
      | Criterion 1 | Level 11 | 1  | Level 12 | 20 | Level 13 | 40 | Level 14  | 50  |
      | Criterion 2 | Level 21 | 10 | Level 22 | 20 | Level 23 | 30 |           |     |
      | Criterion 3 | Level 31 | 5  | Level 32 | 20 |          |    |           |     |
    And I press "Save as draft"
    And I go to "Test assignment 1 name" advanced grading definition page
    And I click on "Move down" "button" in the "Criterion 1" "table_row"
    And I press "Save rubric and make it ready"
    Then I should see "Ready for use"
    # Grading two students.
    And I navigate to "Assignment" in current page administration
    And I go to "Student 1" "Test assignment 1 name" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | 50 | Very good |
    And I press "Save changes"
    # Checking that it complains if you don't select a level for each criterion.
    And I should see "Please choose something for each criterion"
    And I grade by filling the rubric with:
      | Criterion 1 | 50 | Very good |
      | Criterion 2 | 10 | Mmmm, you can do it better |
      | Criterion 3 | 5 | Not good |
    And I complete the advanced grading form with these values:
      | Feedback comments | In general... work harder... |
    # Checking that the user grade is correct.
    And I should see "65" in the "Student 1" "table_row"
    # Updating the user grade.
    And I am on the "Test assignment 1 name" "assign activity" page
    And I go to "Student 1" "Test assignment 1 name" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | 20 | Bad, I changed my mind |
      | Criterion 2 | 10 | Mmmm, you can do it better |
      | Criterion 3 | 5 | Not good |
    #And the level with "50" points was previously selected for the rubric criterion "Criterion 1"
    #And the level with "20" points is selected for the rubric criterion "Criterion 1"
    And I save the advanced grading form
    And I should see "35" in the "Student 1" "table_row"
    And I log out
    # Viewing it as a student.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as student1
    And I should see "35" in the ".feedback" "css_element"
    And I should see "Rubric test description" in the ".feedback" "css_element"
    And I should see "In general... work harder..."
    And the level with "10" points is selected for the rubric criterion "Criterion 2"
    And the level with "20" points is selected for the rubric criterion "Criterion 1"
    And the level with "5" points is selected for the rubric criterion "Criterion 3"
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    # Editing a rubric definition without regrading students.
    And I go to "Test assignment 1 name" advanced grading definition page
    And "Save as draft" "button" should not exist
    And I click on "Move up" "button" in the "Criterion 1" "table_row"
    And I replace "Level 11" rubric level with "Level 11 edited" in "Criterion 1" criterion
    And I press "Save"
    And I should see "You are about to save changes to a rubric that has already been used for grading."
    And I set the field "menurubricregrade" to "Do not mark for regrade"
    And I press "Continue"
    And I log out
    # Check that the student still sees the grade.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as student1
    And I should see "35" in the ".feedback" "css_element"
    And the level with "20" points is selected for the rubric criterion "Criterion 1"
    And I log out
    # Editing a rubric with significant changes.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I go to "Test assignment 1 name" advanced grading definition page
    And I click on "Move down" "button" in the "Criterion 2" "table_row"
    And I replace "1" rubric level with "60" in "Criterion 1" criterion
    And I press "Save"
    And I should see "You are about to save significant changes to a rubric that has already been used for grading. The gradebook value will be unchanged, but the rubric will be hidden from students until their item is regraded."
    And I press "Continue"
    And I log out
    # Check that the student doesn't see the grade.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as student1
    And I should see "35" in the ".feedback" "css_element"
    And the level with "20" points is not selected for the rubric criterion "Criterion 1"
    And I log out
    # Regrade student.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as teacher1
    And I go to "Student 1" "Test assignment 1 name" activity advanced grading page
    And I should see "The rubric definition was changed after this student had been graded. The student can not see this rubric until you check the rubric and update the grade."
    And I save the advanced grading form
    And I log out
    # Check that the student sees the grade again.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as student1
    And I should see "31.82" in the ".feedback" "css_element"
    And the level with "20" points is not selected for the rubric criterion "Criterion 1"
    # Hide all rubric info for students
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Test assignment 1 name" advanced grading definition page
    And I set the field "Allow users to preview rubric (otherwise it will only be displayed after grading)" to ""
    And I set the field "Display rubric description during evaluation" to ""
    And I set the field "Display rubric description to those being graded" to ""
    And I set the field "Display points for each level during evaluation" to ""
    And I set the field "Display points for each level to those being graded" to ""
    And I press "Save"
    And I set the field "menurubricregrade" to "Do not mark for regrade"
    And I press "Continue"
    And I log out
    # Students should not see anything.
    And I am on the "Test assignment 1 name" "assign activity" page logged in as student1
    And I should not see "Criterion 1" in the ".submissionstatustable" "css_element"
    And I should not see "Criterion 2" in the ".submissionstatustable" "css_element"
    And I should not see "Criterion 3" in the ".submissionstatustable" "css_element"
    And I should not see "Rubric test description" in the ".feedback" "css_element"
