@mod @mod_adaptivequiz
Feature: View students results in adaptive quiz
  In order to control what results students have on attempting adaptive quizzes
  As a teacher
  I need an access to attempts reporting

  Background:
    Given the following "users" exist:
      | username | firstname | lastname    | email                       |
      | teacher1 | John      | The Teacher | johntheteacher@example.com  |
      | student1 | Peter     | The Student | peterthestudent@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name                    |
      | Course       | C1        | Adaptive Quiz Questions |
    And the following "questions" exist:
      | questioncategory        | qtype     | name | questiontext    | answer |
      | Adaptive Quiz Questions | truefalse | TF1  | First question  | True   |
      | Adaptive Quiz Questions | truefalse | TF2  | Second question | True   |
    And the following "core_question > Tags" exist:
      | question  | tag    |
      | TF1       | adpq_2 |
      | TF2       | adpq_3 |
    And the following "activity" exists:
      | activity          | adaptivequiz            |
      | idnumber          | adaptivequiz1           |
      | course            | C1                      |
      | name              | Adaptive Quiz           |
      | startinglevel     | 2                       |
      | lowestlevel       | 1                       |
      | highestlevel      | 10                      |
      | minimumquestions  | 2                       |
      | maximumquestions  | 20                      |
      | standarderror     | 5                       |
      | questionpoolnamed | Adaptive Quiz Questions |
    And I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I log out

  @javascript
  Scenario: Attempts report
    When I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    Then I should see "Attempts report"
    And "Peter The Student" "table_row" should exist
    And "Peter The Student" row "Number of attempts" column of "usersattemptstable" table should contain "1"

  @javascript
  Scenario: Deleted user should not appear in the attempts report
    Given the following "user" exists:
      | username     | student2                    |
      | firstname    | Henry                       |
      | lastname     | The Student                 |
      | email        | henrythestudent@example.com |
    And the following "course enrolment" exists:
      | user         | student2 |
      | course       | C1       |
      | role         | student  |
    And I am on the "adaptivequiz1" "Activity" page logged in as "student2"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I log out
    And I log in as "admin"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "Henry The Student"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I press "Yes"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    Then "Henry The Student" "table_row" should not exist

  @javascript
  Scenario: Individual user attempts report
    When I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I click on "1" "link" in the "Peter The Student" "table_row"
    Then I should see "Adaptive Quiz - individual user attempts report for Peter The Student"
    And "Completed" "table_row" should exist
    And "Completed" row "Reason for stopping attempt" column of "individualuserattemptstable" table should contain "Unable to fetch a question for level 5"
    And "Completed" row "Sum of questions attempted" column of "individualuserattemptstable" table should contain "2"

  @javascript
  Scenario: View attempt summary
    When I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I click on "1" "link" in the "Peter The Student" "table_row"
    And I click on "Review attempt" "link" in the "Completed" "table_row"
    Then I should see "Peter The Student (peterthestudent@example.com)" in the "User" "table_row"

  @javascript
  Scenario: View attempt questions details
    When I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I click on "1" "link" in the "Peter The Student" "table_row"
    And I click on "Review attempt" "link" in the "Completed" "table_row"
    And I click on "Questions Details" "link"
    # Info on the first question
    Then I should see "Correct" in the "[id^=question-][id$=-1] .info .state" "css_element"
    # Info on the second question
    And I should see "Correct" in the "[id^=question-][id$=-2] .info .state" "css_element"
