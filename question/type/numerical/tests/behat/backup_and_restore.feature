@qtype @qtype_numerical
Feature: Test duplicating a quiz containing a Numerical question
  As a teacher
  In order re-use my courses containing Numerical questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name          | template    |
      | Test questions   | numerical | Numerical-001 | pi          |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Numerical-001 | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a Numerical question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" in current page administration
    And I choose "Edit question" action for "Numerical-001" in the question bank
    Then the following fields match these values:
      | Question name                      | Numerical-001                              |
      | Question text                      | What is pi to two d.p.?                    |
      | General feedback                   | Generalfeedback: 3.14 is the right answer. |
      | Default mark                       | 1                                          |
      | id_answer_0                        | 3.14                                       |
      | id_tolerance_0                     | 0                                          |
      | id_fraction_0                      | 100%                                       |
      | id_feedback_0                      | Very good.                                 |
      | id_answer_1                        | 3.142                                      |
      | id_tolerance_1                     | 0                                          |
      | id_fraction_1                      | None                                       |
      | id_feedback_1                      | Too accurate.                              |
      | id_answer_2                        | 3.1                                        |
      | id_tolerance_2                     | 0                                          |
      | id_fraction_2                      | None                                       |
      | id_feedback_2                      | Not accurate enough.                       |
      | id_answer_3                        | 3                                          |
      | id_tolerance_3                     | 0                                          |
      | id_fraction_3                      | None                                       |
      | id_feedback_3                      | Not accurate enough.                       |
      | id_answer_4                        | *                                          |
      | id_tolerance_4                     | 0                                          |
      | id_fraction_4                      | None                                       |
      | id_feedback_4                      | Completely wrong.                          |
