@qbank @qbank_statistics
Feature: Show statistics in question bank

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | user1     | Student1 | student1@example.com |
      | student2 | user2     | Student2 | student2@example.com |
      | student3 | user3     | Student3 | student3@example.com |
      | student4 | user4     | Student4 | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
      | Test questions   | truefalse   | TF3   | Third question  |
      | Test questions   | truefalse   | TF4   | Fourth question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
      | quiz       | Quiz 2 | Quiz 2 description | C1     | quiz2    |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    | 1.0     |
      | TF2      | 1    | 1.0     |
      | TF3      | 1    | 1.0     |
      | TF4      | 1    | 1.0     |
    And quiz "Quiz 2" contains the following questions:
      | question | page | maxmark |
      | TF2      | 1    | 1.0     |
      | TF3      | 1    | 1.0     |
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | False    |
      |   3  | False    |
      |   4  | False    |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
      |   3  | True     |
      |   4  | True     |
    And user "student3" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
      |   3  | False    |
      |   4  | True     |
    And user "student4" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | True     |
      |   3  | True     |
      |   4  | False    |

  Scenario: View facility index in question bank
    Given user "student1" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True    |
      |   2  | True    |
    And user "student2" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
    When I am on the "Course 1" course page logged in as admin
    And I navigate to "Question bank" in current page administration
    And I should see "50.00%" in the "TF1" "table_row"
    And I should see "75.00%" in the "TF2" "table_row"
    And I should see "75.00%" in the "TF3" "table_row"
    And I should see "50.00%" in the "TF4" "table_row"

  Scenario: View discriminative efficiency in question bank
    Given user "student1" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | False    |
    And user "student2" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
    When I am on the "Course 1" course page logged in as admin
    And I navigate to "Question bank" in current page administration
    And I should see "50.00%" in the "TF1" "table_row"
    And I should see "75.00%" in the "TF2" "table_row"
    And I should see "75.00%" in the "TF3" "table_row"
    And I should see "50.00%" in the "TF4" "table_row"

  Scenario: View discrimination index in question bank, good questions
    Given user "student1" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | False    |
    And user "student2" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
    When I am on the "Course 1" course page logged in as admin
    And I navigate to "Question bank" in current page administration
    And I should see "Likely" in the "TF1" "table_row"
    And I should see "Unlikely" in the "TF2" "table_row"
    And I should see "Unlikely" in the "TF3" "table_row"
    And I should see "Likely" in the "TF4" "table_row"
    And I should see "Likely" in the ".alert-warning" "css_element"
    And I should see "Unlikely" in the ".alert-success" "css_element"

  Scenario: View discrimination index in question bank, bad questions
    Given user "student1" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
    And user "student2" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | True     |
    And user "student3" has attempted "Quiz 2" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    When I am on the "Course 1" course page logged in as admin
    And I navigate to "Question bank" in current page administration
    And I should see "Likely" in the "TF1" "table_row"
    And I should see "Very likely" in the "TF2" "table_row"
    And I should see "Very likely" in the "TF3" "table_row"
    And I should see "Likely" in the "TF4" "table_row"
    And I should see "Very likely" in the ".alert-danger" "css_element"
    And I should see "Likely" in the ".alert-warning" "css_element"
