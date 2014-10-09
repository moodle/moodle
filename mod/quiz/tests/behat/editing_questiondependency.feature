
@mod @mod_quiz @questiondependency
Feature: Edit quiz page - pagination
  In order to build a quiz laid out in pages with n question(s) on each page, where n >=1.
  I need to be able to add and remove question dependency on any qualified question
  in quiz editing page.
  
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |

    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"

  @javascript
  Scenario: There is no dependency setting on question in the quiz.
  I can add or remove dependency to a given question by clicking on  "Add question dependency"
  or "Remove question dependency" icons. Then I can attempt the quiz and see the effect of the
  dependency settings.

    Then I should see "Editing quiz: Quiz 1"

    # Add the first true false question.
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF 001                          |
      | Question text                      | Answer the TF 001 question              |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I should see "TF 001"

    # Add the second true false question.
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF 002                          |
      | Question text                      | Answer the TF 002 question              |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I should see "TF 001"
    #And I should not see "Add question dependency" "link" in the "TF 001" "table_row"
    And I should see "TF 002"
    And I follow "Add question dependency"

    # Add the third true false question.
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF 003                          |
      | Question text                      | Answer the TF 003 question              |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I should see "TF 001"
    And I should see "TF 002"
    And I should see "TF 003"

    # Attempt the quiz
    And I follow "Quiz 1"
    When I press "Preview quiz now"
    And I should see "You have to complete the previous question first, then you would be able to see the content of this question."

    # Back to the quiz editing page
    And I follow "Quiz 1"
    When I follow "Edit quiz"
    Then I should see "Editing quiz: Quiz 1"
    And I follow "Remove question dependency"
    And I follow "Quiz 1"
    When I press "Continue the last preview"
    And I press "Start a new preview"
    And I should not see "You have to complete the previous question first, then you would be able to see the content of this question."