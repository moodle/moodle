@ou @ou_vle @qtype @qtype_ddwtos
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
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript
  Scenario: Create a drag and drop into text question
    When I add a "Drag and drop into text" question filling the form with:
      | Question name             | Drag and drop into text 001   |
      | Question text             | The [[1]] [[2]] on the [[3]]. |
      | General feedback          | The cat sat on the mat.       |
      | id_choices_0_answer       | cat                           |
      | id_choices_1_answer       | sat                           |
      | id_choices_2_answer       | mat                           |
      | id_choices_3_answer       | dog                           |
      | id_choices_4_answer       | table                         |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Drag and drop into text 001"
