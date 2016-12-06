@mod @mod_quiz
Feature: Basic use of the Manual grading report
  In order to easily find students attempts that need manual grading
  As a teacher
  I need to use the manual grading report

  @javascript
  Scenario: Use the Manual grading report
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | T1        | Teacher1 | teacher1@example.com | T1000    |
      | student1 | S1        | Student1 | student1@example.com | S1000    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name        | Quiz 1             |
      | Description | Quiz 1 description |
    And I add a "Short answer" question to the "Quiz 1" quiz with:
      | Question name    | Short answer 001                     |
      | Question text    | Where is the capital city of France? |
      | Answer 1         | Paris                                |
      | Grade            | 100%                                 |

    # Check report shows nothing when there are no attempts.
    And I follow "Course 1"
    And I follow "Quiz 1"
    When I navigate to "Results > Manual grading" in current page administration
    Then I should see "Manual grading"
    And I should see "Quiz 1"
    And I should see "Nothing to display"
    And I follow "Also show questions that have been graded automatically"
    And I should see "Nothing to display"
    And I log out

    # Create an attempt.
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I should see "Question 1"
    And I should see "Not yet answered"
    And I should see "Where is the capital city of France?"
    And I set the field "Answer:" to "Paris"
    And I press "Finish attempt ..."
    And I should see "Answer saved"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out

    # Use the manual grading report.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I navigate to "Results > Manual grading" in current page administration
    And I should see "Manual grading"
    And I follow "Also show questions that have been graded automatically"
    And I should see "Short answer 001"
    And I should see "0" in the ".cell.c2" "css_element"
    And I should see "0" in the ".cell.c3" "css_element"

    # Adjust the mark for Student1.
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    And I set the field "Comment" to "I have adjusted your mark to 0.6"
    And I set the field "Mark" to "0.6"
    And I press "Save and go to next page"
    And I should see "All selected attempts have been graded. Returning to the list of questions."
    And I should see "0" in the ".cell.c2" "css_element"
    And I should see "1" in the ".cell.c3" "css_element"
