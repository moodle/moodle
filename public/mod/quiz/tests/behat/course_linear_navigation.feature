@mod @mod_quiz
Feature: Display the course linear navigation in the quiz pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in quiz pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student  | Student   | 1        |
      | teacher  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | intro              | course | idnumber | grade |
      | quiz     | Quiz 1  | Quiz 1 description | C1     | quiz1    | 100   |
      | qbank    | Qbank 1 | Question bank 1    | C1     | qbank1   |       |
    And the following "question categories" exist:
      | contextlevel    | reference  | name            |
      | Activity module | quiz1      | Test questions  |
    And the following "questions" exist:
      | questioncategory | qtype     | name  | questiontext    |
      | Test questions   | truefalse | TF1   | First question  |
      | Test questions   | truefalse | TF2   | Second question |
      | Test questions   | truefalse | TF3   | Third question  |
      | Test questions   | truefalse | TF4   | Fourth question |
      | Test questions   | essay     | E1    | Essay question  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | E1       | 2    |

  @javascript
  Scenario: As a student I should see the course linear navigation in quiz pages that allow it
    Given I am on the "Quiz 1" "quiz activity" page logged in as "student"
    Then the course linear navigation should be visible
    But I press "Attempt quiz"
    And the course linear navigation should not be visible
    And I press "Next page"
    And the course linear navigation should not be visible
    And I press "Finish attempt ..."
    And the course linear navigation should not be visible
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    # Review attempt page.
    And the course linear navigation should not be visible
    And I follow "Finish review"
    And the course linear navigation should be visible
    And I press "Re-attempt quiz"
    And the course linear navigation should not be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in quiz pages that allow it
    Given user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    And the following config values are set as admin:
      | enablemyhome    | 1 |             |
      | enablemycourses | 1 |             |
      | unaddableblocks |   | theme_boost |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Administration" block if not present
    And I configure the "Administration" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    When I am on the "Quiz 1" "quiz activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    But I click on "Preview quiz" "button" in the "region-main" "region"
    And the course linear navigation should not be visible
    # Questions page.
    And I navigate to "Questions" in current page administration
    And the course linear navigation should not be visible
    # Results page.
    And I navigate to "Results" in current page administration
    And the course linear navigation should not be visible
    And I follow "Responses"
    And the course linear navigation should not be visible
    And I follow "Statistics"
    And the course linear navigation should not be visible
    # Manual grading.
    And I follow "Manual grading"
    And the course linear navigation should not be visible
    And I click on "Grade" "link" in the "2" "table_row"
    And the course linear navigation should not be visible
    # Comments
    And I am on the "Quiz 1 > student > Attempt 1" "mod_quiz > Attempt review" page
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And the course linear navigation should not be visible
    And I press "Save" and switch to main window
    # Question bank
    And I navigate to "Question bank" in current page administration
    And the course linear navigation should not be visible
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Preview" action for "E1" in the question bank
    And the course linear navigation should not be visible
    And I click on "Close preview" "button"
    And I choose "Edit question" action for "E1" in the question bank
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I choose "History" action for "E1" in the question bank
    And the course linear navigation should not be visible
    And I click on "Close" "link"
    And I choose "Delete" action for "E1" in the question bank
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I set the field "Question bank tertiary navigation" to "Export"
    And the course linear navigation should not be visible
    And I set the field "Question bank tertiary navigation" to "Import"
    And the course linear navigation should not be visible
    And I set the field "Question bank tertiary navigation" to "Categories"
    And the course linear navigation should not be visible
    # Overrides page.
    And I navigate to "Overrides" in current page administration
    And the course linear navigation should not be visible
    And I press "Add user override"
    And the course linear navigation should not be visible
    And I press "Cancel"
