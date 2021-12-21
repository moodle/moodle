@qtype @qtype_ddwtos
Feature: Test creating a drag and drop into text question
  As a teacher
  In order to test my students
  I need to be able to create drag and drop into text questions

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
  Scenario: Create a drag and drop into text question
    Given I add a "Drag and drop into text" question filling the form with:
      | Question name                  | Drag and drop into text 001   |
      | Question text                  | The [[1]] [[2]] on the [[3]]. |
      | General feedback               | The cat sat on the mat.       |
      | id_shuffleanswers              | 1                             |
      | id_choices_0_answer            | cat                           |
      | id_choices_1_answer            | sat                           |
      | id_choices_2_answer            | mat                           |
      | id_choices_3_answer            | dog                           |
      | id_choices_4_answer            | table                         |
      | Penalty for each incorrect try | 20%                           |
      | Hint 1                         | First hint                    |
      | Hint 2                         | Second hint                   |
    And I should see "Drag and drop into text 001"
    # Checking that the next new question form displays user preferences settings.
    When I press "Create a new question ..."
    And I set the field "item_qtype_ddwtos" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then the following fields match these values:
      | id_shuffleanswers              | 1   |
      | Penalty for each incorrect try | 20% |
