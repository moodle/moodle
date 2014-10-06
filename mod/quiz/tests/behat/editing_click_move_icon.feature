@mod @mod_quiz
Feature: Edit quiz page - drag-and-drop
  In order to change the layout of a quiz I built
  As a teacher
  I need to be able to drag and drop questions to reorder them.

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
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question A |
      | Question text | Answer me  |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question B   |
      | Question text | Answer again |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question C |
      | Question text | And again  |
    And I click on the "Add" page break icon after question "Question B"

  @javascript
  Scenario: Re-order questions by clicking on the move icon.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"

    When I move "Question A" to "After Question 2" in the quiz by clicking the move icon
    Then I should see "Question B" on quiz page "1"
    And I should see "Question A" on quiz page "1"
    And I should see "Question B" before "Question A" on the edit quiz page
    And I should see "Question C" on quiz page "2"

    When I move "Question A" to "After Page 2" in the quiz by clicking the move icon
    Then I should see "Question B" on quiz page "1"
    And I should see "Question A" on quiz page "2"
    And I should see "Question C" on quiz page "2"
    And I should see "Question A" before "Question C" on the edit quiz page

    When I move "Question B" to "After Question 2" in the quiz by clicking the move icon
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "1"
    And I should see "Question A" before "Question B" on the edit quiz page
    And I should see "Question B" before "Question C" on the edit quiz page

    When I move "Question B" to "After Page 1" in the quiz by clicking the move icon
    Then I should see "Question B" on quiz page "1"
    And I should see "Question A" on quiz page "1"
    And I should see "Question C" on quiz page "1"
    And I should see "Question B" before "Question A" on the edit quiz page
    And I should see "Question A" before "Question C" on the edit quiz page

    When I click on the "Add" page break icon after question "Question A"
    When I open the "Page 2" add to quiz menu
    And I follow "a new question" in the open menu
    And I set the field "qtype_qtype_description" to "1"
    And I press "submitbutton"
    Then I should see "Adding a description"
    And I set the following fields to these values:
      | Question name | Question D  |
      | Question text | Useful info |
    And I press "id_submitbutton"
    Then I should see "Question B" on quiz page "1"
    And I should see "Question A" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Question D" on quiz page "2"
    And I should see "Question B" before "Question A" on the edit quiz page
    And I should see "Question C" before "Question D" on the edit quiz page

    And "Question B" should have number "1" on the edit quiz page
    And "Question A" should have number "2" on the edit quiz page
    And "Question C" should have number "3" on the edit quiz page
    And "Question D" should have number "i" on the edit quiz page

    When I move "Question D" to "After Question 2" in the quiz by clicking the move icon
    Then I should see "Question B" on quiz page "1"
    And I should see "Question D" on quiz page "1"
    And I should see "Question A" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Question B" before "Question A" on the edit quiz page
    And I should see "Question A" before "Question D" on the edit quiz page

    And "Question B" should have number "1" on the edit quiz page
    And "Question D" should have number "i" on the edit quiz page
    And "Question A" should have number "2" on the edit quiz page
    And "Question C" should have number "3" on the edit quiz page
