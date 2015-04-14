@mod @mod_quiz
Feature: Set a quiz to be marked complete when the student passes
  In order to ensure a student has learned the material before being marked complete
  As a teacher
  I need to set a quiz to complete when the student recieves a passing grade

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
     | enablecompletion | 1 |
    And I log in as "admin"
    And I navigate to "Grade item settings" node in "Site administration > Grades"
    And I set the field "Advanced grade item options" to "hiddenuntil"
    And I press "Save changes"
    And I log out

  Scenario: student1 passes on the first try
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name        | Test quiz name        |
      | Description | Test quiz description |
      | Completion tracking | Show activity as complete when conditions are met |
      | Attempts allowed | 4 |
      | Require passing grade | 1 |
    And I add a "True/False" question to the "Test quiz name" quiz with:
      | Question name                      | First question                          |
      | Question text                      | Answer the first question               |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | True                                    |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "jump" to "Categories and items"
    And I press "Go"
    And I follow "Edit  quiz Test quiz name"
    Then I should see "Edit grade item"
    And I set the field "gradepass" to "5"
    And I press "Save changes"
    Then I should see "Categories and items"
    And I log out

    And I log in as "student1"
    And I follow "Course 1"
    And "//img[contains(@alt, 'Not completed: Test quiz name')]" "xpath_element" should exist in the "li.modtype_quiz" "css_element"
    And I follow "Test quiz name"
    And I press "Attempt quiz now"
    Then I should see "Question 1"
    And I should see "Answer the first question"
    And I set the field "False" to "1"
    And I press "Next"
    And I should see "Answer saved"
    And I press "Submit all and finish"
    And I follow "C1"
    And "//img[contains(@alt, 'Not completed: Test quiz name')]" "xpath_element" should exist in the "li.modtype_quiz" "css_element"
    And I follow "Test quiz name"
    And I press "Re-attempt quiz"
    Then I should see "Question 1"
    And I should see "Answer the first question"
    And I set the field "True" to "1"
    And I press "Next"
    And I should see "Answer saved"
    And I press "Submit all and finish"
    And I follow "C1"
    And "//img[contains(@alt, 'Completed: Test quiz name')]" "xpath_element" should exist in the "li.modtype_quiz" "css_element"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Activity completion"
    Then "//img[contains(@title,'Test quiz name') and @alt='Completed']" "xpath_element" should exist in the "Student 1" "table_row"
