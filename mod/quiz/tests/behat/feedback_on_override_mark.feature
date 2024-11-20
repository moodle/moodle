@mod @mod_quiz @quiz
Feature: Teachers see correct answers when overriding marks
  As a teacher
  In order to correct errors
  I must be able to override the grades that Moodle gives.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student0@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name   | questiontext                   | generalfeedback |
      | Test questions   | shortanswer | Kermit | What kind of animal is Kermit? |                 |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question  | page |
      | Kermit    | 1    |
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    And I set the field "Answer" to "lion"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out

  @javascript @_switch_window @_bug_phantomjs
  Scenario: Override marking of a short answer question attempt.
    When I am on the "Quiz 1 > student1 > Attempt 1" "mod_quiz > Attempt review" page logged in as "teacher1"
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window

    Then I should see "The correct answer is: frog"
