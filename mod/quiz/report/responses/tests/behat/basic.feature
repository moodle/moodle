@mod @mod_quiz @quiz @quiz_reponses
Feature: Basic use of the Responses report
  In order to see how my students are progressing
  As a teacher
  I need to see all their quiz responses

  Background: Using the Responses report
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | The       | Teacher  |
      | student1 | Student   | One      |
      | student2 | Student   | Two      |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | interactive        |
    And the following "questions" exist:
      | questioncategory | qtype     | name | template |
      | Test questions   | numerical | NQ   | pi3tries |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | NQ       | 1    | 3.0     |

  @javascript
  Scenario: Report works when there are no attempts
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Results > Responses" in current page administration
    Then I should see "Attempts: 0"
    And I should see "Nothing to display"
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    And I press "Show report"
    And "Student One" row "State" column of "responses" table should contain "-"

  @javascript
  Scenario: Report works when there are attempts
    # Add an attempt
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I set the field "Answer" to "1.0"
    And I press "Check"
    And I press "Try again"
    And I set the field "Answer" to "3.0"
    And I press "Check"
    And I press "Try again"
    And I set the field "Answer" to "3.14"
    And I press "Check"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out

    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Results > Responses" in current page administration
    Then I should see "Attempts: 1"
    And I should see "Student One"
    And I should not see "Student Two"
    And I set the field "Attempts from" to "enrolled users who have, or have not, attempted the quiz"
    And I set the field "Which tries" to "All tries"
    And I press "Show report"
    And "Student OneReview attempt" row "Response 1Sort by Response 1 Ascending" column of "responses" table should contain "1.0"
    And "Student OneReview attempt" row "State" column of "responses" table should contain ""
    And "Finished" row "Grade/100.00Sort by Grade/100.00 Ascending" column of "responses" table should contain "33.33"
    And "Finished" row "Response 1Sort by Response 1 Ascending" column of "responses" table should contain "3.14"
    And "Student Two" row "State" column of "responses" table should contain "-"
    And "Student Two" row "Response 1Sort by Response 1 Ascending" column of "responses" table should contain "-"

  @javascript
  Scenario: Report does not allow strange combinations of options
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Results > Responses" in current page administration
    And the "Which tries" "select" should be enabled
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    Then the "Which tries" "select" should be disabled
