@mod @mod_quiz @quiz @quiz_overview
Feature: Basic use of the Grades report
  In order to easily get an overview of quiz attempts
  As a teacher
  I need to use the Grades report

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber | profile_field_fruit |
      | teacher1 | T1        | Teacher1 | teacher1@example.com | T1000    |                     |
      | student1 | S1        | Student1 | student1@example.com | S1000    | Apple               |
      | student2 | S2        | Student2 | student2@example.com | S2000    | Banana              |
      | student3 | S3        | Student3 | student3@example.com | S3000    | Pear                |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |

  @javascript
  Scenario: Using the Grades report
    # Basic check of the Grades report
    When I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    Then I should see "Attempts: 2"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # And student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

    # Check changing the form parameters
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    And I press "Show report"
    # Note: teachers should not appear in the report.
    # Check student3's grade
    And I should see "-" in the "S3 Student3" "table_row"

    And I set the field "Attempts from" to "enrolled users who have, or have not, attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"
    # Check student3's grade
    And I should see "-" in the "S3 Student3" "table_row"

    And I set the field "Attempts from" to "all users who have attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

  @javascript
  Scenario: View custom user profile fields in the grades report
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    Then I should see "Apple" in the "S1 Student1" "table_row"
    And I should see "Banana" in the "S2 Student2" "table_row"
