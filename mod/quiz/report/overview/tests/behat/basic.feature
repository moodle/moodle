@mod @mod_quiz
Feature: Basic use of the Grades report
  In order to easily get an overview of quiz attempts
  As a teacher
  I need to use the Grades report

  @javascript
  Scenario: Using the Grades report
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | T1        | Teacher1 | teacher1@example.com | T1000    |
      | student1 | S1        | Student1 | student1@example.com | S1000    |
      | student2 | S2        | Student2 | student2@example.com | S2000    |
      | student3 | S3        | Student3 | student3@example.com | S3000    |
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

    # Add some attempts
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out

    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out

    # Basic check of the Grades report
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I navigate to "Results > Grades" in current page administration
    Then I should see "Attempts: 2"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # And student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

    # Check changing the form parameters
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    And I press "Show report"
    # Check teacher1's grade
    And I should see "-" in the "T1 Teacher1" "table_row"
    # Check student3's grade
    And I should see "-" in the "S3 Student3" "table_row"

    And I set the field "Attempts from" to "enrolled users who have, or have not, attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"
    # Check teacher1's grade
    And I should see "-" in the "T1 Teacher1" "table_row"

    And I set the field "Attempts from" to "all users who have attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

    # Check regrade and delete attempts.
    And I set the field with xpath "//tr[contains(normalize-space(.), 'student1@example.com')]//input[@type='checkbox']" to "1"
    And I press "Regrade selected attempts"
    And I press "Continue"
    And I should see "student1@example.com"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'student1@example.com')]//input[@type='checkbox']" to "1"
    And I press "Delete selected attempts"
    And I press "Yes"
    And I should not see "student1@example.com"
