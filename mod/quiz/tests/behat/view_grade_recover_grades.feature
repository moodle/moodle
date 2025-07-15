@mod @mod_quiz
Feature: Testing view quiz grade feedback with recover grades setting
  As a user
  I want the quiz grade and completion status to reflect the recover grades setting used when re-enrolling a user

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | One      | teacher@example.com |
      | student  | Student   | One      | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student  | C1     | student        |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber | completion | completionusegrade |
      | quiz       | Quiz 1  | Quiz 1 description | C1     | quiz1    | 2          | 1                  |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |

  Scenario Outline: Teachers can view grade feedback and score regardless of recover grades setting
    Given the following config values are set as admin:
      | recovergradesdefault  | <recovergradesetting>  |
    And user "teacher" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher"
    # Grade feedback and grade score should be the same. Recover grades setting should not affect the score
    Then I should see "Highest grade: 100.00 / 100.00"
    And I should see "100.00 out of 100.00" in the "Grade" "table_row"

    Examples:
      | recovergradesetting |
      | 0                   |
      | 1                   |

  Scenario Outline: View quiz grade feedback and score with recover grades settings
    Given the following config values are set as admin:
      | recovergradesdefault  | <recovergradesetting>  |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    # Grade feedback and grade score should be the same. Recover grades setting should not affect users who are not unenrolled
    Then I should see "Highest grade: 100.00 / 100.00"
    And I should see "100.00 out of 100.00" in the "Grade" "table_row"
    And I should see "Done: Receive a grade" in the "[data-region='completion-info']" "css_element"

    Examples:
      | recovergradesetting |
      | 0                   |
      | 1                   |

  @javascript
  Scenario Outline: View quiz after unenrolling and re-enrolling user
    Given the following config values are set as admin:
      | recovergradesdefault  | <recovergradesetting> |
    And I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "Student One" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    And the following "course enrolments" exist:
      | user     | course | role     |
      | student  | C1     | student  |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    Then I should see "<overallgradefeedback>" in the "[id='feedback']" "css_element"
    And I should see "100.00 out of 100.00" in the "Grade" "table_row"
    And I should see "<mycompletionstatus>" in the "[data-region='completion-info']" "css_element"
    # Re-attempt the quiz
    And I press "Re-attempt quiz"
    And I should see "First question"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "Finish attempt ..." "button" in the "region-main" "region"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I follow "Finish review"
    And I should see "Highest grade: 100.00 / 100.00" in the "[id='feedback']" "css_element"

    Examples:
      | recovergradesetting | overallgradefeedback                   | mycompletionstatus     |
      | 0                   | Highest grade: Not yet graded / 100.00 | To do: Receive a grade |
      | 1                   | Highest grade: 100.00 / 100.00         | Done: Receive a grade  |
