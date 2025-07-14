@qtype @qtype_ordering
Feature: Test creating an Ordering question
  As a teacher
  In order to test my students
  I need to be able to create an Ordering question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"

  @javascript
  Scenario: Create an Ordering question with 6 draggable items
    When I add a "Ordering" question filling the form with:
      | Question name                      | Ordering-001                     |
      | Question text                      | Put the words in correct order.  |
      | General feedback                   | One two three four five six      |
      | id_answer_0                        | one                              |
      | id_answer_1                        | two                              |
      | id_answer_2                        | three                            |
      | id_answer_3                        | four                             |
      | id_answer_4                        | five                             |
      | id_answer_5                        | six                              |
      | For any correct response           | Your answer is correct           |
      | For any partially correct response | Your answer is partially correct |
      | For any incorrect response         | Your answer is incorrect         |
      | Hint 1                             | This is your first hint          |
      | Hint 2                             | This is your second hint         |
      | hintoptions[0]                     | 1                                |
      | hintshownumcorrect[1]              | 1                                |
      | shownumcorrect                     | 1                                |
    Then I should see "Ordering-001"

  Scenario: Ordering questions are created with only the number of hints that are defined
    Given the following config values are set as admin:
      | behaviour | interactive | question_preview |
    When I add a "Ordering" question filling the form with:
      | Question name                      | Ordering with one hint           |
      | Question text                      | Put the words in correct order.  |
      | id_answer_0                        | one                              |
      | id_answer_1                        | two                              |
      | id_answer_2                        | three                            |
      | Hint 1                             | This is the first and only hint. |
      | hintoptions[0]                     | 1                                |
    And I should see "Ordering with one hint"
    When I am on the "Ordering with one hint" "core_question > preview" page
    Then I should see "Tries remaining: 2"
