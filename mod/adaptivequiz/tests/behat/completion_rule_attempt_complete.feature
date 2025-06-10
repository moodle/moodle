@mod @mod_adaptivequiz
Feature: Set activity as completed when at least one attempt is completed
  In order to control whether the activity has been complete by students
  As a teacher
  I need the activity to be marked as completed for a student when they have made at least one attempt on the adaptive quiz

  Background:
    Given the following "users" exist:
      | username | firstname | lastname    | email                       |
      | teacher1 | John      | The Teacher | johntheteacher@example.com  |
      | student1 | Peter     | The Student | peterthestudent@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
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
      | question | tag    |
      | TF1      | adpq_2 |
      | TF2      | adpq_3 |
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

  @javascript
  Scenario: Teacher sets the completion rule and student completes an attempt in Moodle version 4.2 or lower
    Given the site is running Moodle version 4.2.7 or lower
    And I am on the "Adaptive Quiz" "adaptivequiz activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Completion tracking | Show activity as complete when conditions are met |
    And I click on "completionattemptcompleted" "checkbox"
    And I click on "Save and return to course" "button"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I log out
    And I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    Then "Adaptive Quiz" should have the "Complete an attempt" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Completed" "icon" should exist in the "Peter The Student" "table_row"

  @javascript
  Scenario: Teacher sets the completion rule and student completes an attempt in Moodle version 4.3 or higher
    Given the site is running Moodle version 4.3 or higher
    And I am on the "Adaptive Quiz" "adaptivequiz activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Add requirements           | 1 |
      | completionattemptcompleted | 1 |
    And I click on "Save and return to course" "button"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I log out
    And I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    Then "Adaptive Quiz" should have the "Complete an attempt" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Completed" "icon" should exist in the "Peter The Student" "table_row"
