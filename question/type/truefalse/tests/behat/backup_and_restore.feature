@qtype @qtype_truefalse
Feature: Test duplicating a quiz containing a True/False question
  As a teacher
  In order re-use my courses containing True/False questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | template |
      | Test questions   | truefalse | true-false-001 | true     |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | true-false-001 | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"

  @javascript
  Scenario: Backup and restore a course containing a True/False question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" node in "Course administration"
    And I click on "Edit" "link" in the "true-false-001" "table_row"
    Then the following fields match these values:
      | Question name                      | true-false-001                  |
      | Question text                      | The answer is true.             |
      | Default mark                       | 1                               |
      | General feedback                   | You should have selected true.  |
      | Correct answer                     | True                            |
      | Feedback for the response 'True'.  | This is the right answer.       |
      | Feedback for the response 'False'. | This is the wrong answer.       |
