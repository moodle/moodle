@qtype @qtype_shortanswer
Feature: Test creating a Short answer question
  As a teacher
  In order to test my students
  I need to be able to create a Short answer question

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
  Scenario: Create a Short answer question
    Given I add a "Short answer" question filling the form with:
      | Question name        | shortanswer-001                           |
      | Question text        | What is the national langauge in France?  |
      | General feedback     | The national langauge in France is French |
      | Default mark         | 1                                         |
      | Case sensitivity     | Yes, case must match                      |
      | id_answer_0          | French                                    |
      | id_fraction_0        | 100%                                      |
      | id_feedback_0        | Well done. French is correct.             |
      | id_answer_1          | *                                         |
      | id_fraction_1        | None                                      |
      | id_feedback_1        | Your answer is incorrect.                 |
    And I should see "shortanswer-001"
    # Checking that the next new question form displays user preferences settings.
    When I press "Create a new question ..."
    And I set the field "Short answer" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then the following fields match these values:
      | Case sensitivity | Yes, case must match |
