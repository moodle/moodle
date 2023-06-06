@mod @mod_lesson
Feature: In a lesson activity a student should
  be able to close the lesson and then later resume.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity    | lesson                      |
      | name        | Test lesson name            |
      | course      | C1                          |
      | idnumber    | 0001                        |
      | retake      | 1                           |

  Scenario: resume a lesson with both content then question pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | content   | Third page name       | Third page contents       |
      | Test lesson name | truefalse | True/false question 1 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 2 | Kermit is a frog          |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response | jumpto        | score |
      | First page name       | Next page     |          | Next page     | 0     |
      | Second page name      | Previous page |          | Previous page | 0     |
      | Second page name      | Next page     |          | Next page     | 0     |
      | Third page name       | Previous page |          | Previous page | 0     |
      | Third page name       | Next page     |          | Next page     | 0     |
      | True/false question 1 | True          | Correct  | Next page     | 1     |
      | True/false question 1 | False         | Wrong    | This page     | 0     |
      | True/false question 2 | True          | Correct  | Next page     | 1     |
      | True/false question 2 | False         | Wrong    | This page     | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Third page contents"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    Then I should see "Third page contents"
    # Add 1 sec delay so lesson knows differentiate 3rd and paper attempts.
    And I wait "1" seconds
    And I press "Next page"
    And I should see "Paper is made from trees."
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"

  Scenario: resume a lesson with only content pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title            | content              |
      | Test lesson name | content | First page name  | First page contents  |
      | Test lesson name | content | Second page name | Second page contents |
      | Test lesson name | content | Third page name  | Third page contents  |
      | Test lesson name | content | Fourth page name | Fourth page contents |
    And the following "mod_lesson > answers" exist:
      | page             | answer        | jumpto        |
      | First page name  | Next page     | Next page     |
      | Second page name | Previous page | Previous page |
      | Second page name | Next page     | Next page     |
      | Third page name  | Previous page | Previous page |
      | Third page name  | Next page     | Next page     |
      | Fourth page name | Previous page | Previous page |
      | Fourth page name | End of lesson | End of lesson |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Next page"
    And I should see "Third page contents"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    Then I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Fourth page contents"
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "End of lesson"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"

  Scenario: resume a lesson with both question then content pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | truefalse | True/false question 1 | Cat is an amphibian       |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | truefalse | True/false question 2 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 3 | 1+1=2                     |
      | Test lesson name | truefalse | True/false question 4 | 2+2=4                     |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | truefalse | True/false question 5 | Kermit is a frog          |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response | jumpto    | score |
      | True/false question 1 | False     | Correct  | Next page | 1     |
      | True/false question 1 | True      | Wrong    | This page | 0     |
      | First page name       | Next page |          | Next page | 0     |
      | True/false question 2 | True      | Correct  | Next page | 1     |
      | True/false question 2 | False     | Wrong    | This page | 0     |
      | True/false question 3 | True      | Correct  | Next page | 1     |
      | True/false question 3 | False     | Wrong    | This page | 0     |
      | True/false question 4 | True      | Correct  | Next page | 1     |
      | True/false question 4 | False     | Wrong    | This page | 0     |
      | Second page name      | Next page |          | Next page | 0     |
      | True/false question 5 | True      | Correct  | Next page | 1     |
      | True/false question 5 | False     | Wrong    | This page | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "1+1=2"
    And I set the following fields to these values:
      | True | 1 |
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Submit"
    And I press "Continue"
    And I should see "2+2=4"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "You have seen more than one page of this lesson already."
    Then I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "2+2=4"
    And I set the following fields to these values:
      | True | 1 |
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Submit"
    And I press "Continue"
    And I should see "Second page contents"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"

  Scenario: resume a lesson with only question pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | truefalse | True/false question 1 | Cat is an amphibian       |
      | Test lesson name | truefalse | True/false question 2 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 3 | 1+1=2                     |
      | Test lesson name | truefalse | True/false question 4 | 2+2=4                     |
      | Test lesson name | truefalse | True/false question 5 | Kermit is a frog          |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response | jumpto    | score |
      | True/false question 1 | False     | Correct  | Next page | 1     |
      | True/false question 1 | True      | Wrong    | This page | 0     |
      | True/false question 2 | True      | Correct  | Next page | 1     |
      | True/false question 2 | False     | Wrong    | This page | 0     |
      | True/false question 3 | True      | Correct  | Next page | 1     |
      | True/false question 3 | False     | Wrong    | This page | 0     |
      | True/false question 4 | True      | Correct  | Next page | 1     |
      | True/false question 4 | False     | Wrong    | This page | 0     |
      | True/false question 5 | True      | Correct  | Next page | 1     |
      | True/false question 5 | False     | Wrong    | This page | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "1+1=2"
    And I set the following fields to these values:
      | True | 1 |
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Submit"
    And I press "Continue"
    And I should see "2+2=4"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    Then I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I follow "Yes"
    And I should see "2+2=4"
    And I set the following fields to these values:
      | True | 1 |
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Submit"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
