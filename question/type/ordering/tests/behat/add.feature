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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

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
    Then I should see "Ordering-001"
