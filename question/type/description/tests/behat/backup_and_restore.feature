@qtype @qtype_description
Feature: Test duplicating a quiz containing a Description question
  As a teacher
  In order re-use my courses containing Description questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name            | template |
      | Test questions   | description | description-001 | info     |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | description-001 | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a Description question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" in current page administration
    And I click on "Edit" "link" in the "description-001" "table_row"
    Then the following fields match these values:
      | Question name                      | description-001                                                        |
      | Question text                      | Here is some information about the questions you are about to attempt. |
      | General feedback                   | And here is some more text shown only on the review page.              |
