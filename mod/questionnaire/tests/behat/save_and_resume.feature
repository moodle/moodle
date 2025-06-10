@mod @mod_questionnaire
Feature: Questionnaire responses can be saved and resumed without submitting.
  When save is used on a response, a response can be resumed with the saved responses intact.

  Background: Add a questionnaire with "Save/Resume answers" set to "Yes".
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
    And the following "activities" exist:
      | activity | name | description | course | idnumber | resume |
      | questionnaire | Questionnaire 1 | Questionnaire description | C1 | questionnaire0 | 1 |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Questionnaire 1"
    And I navigate to "Questions" in current page administration
    And I add a "Check Boxes" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Min. forced responses | 1 |
      | Max. forced responses | 2 |
      | Question Text | Select one or two choices only |
      | Possible answers | One,Two,Three,Four |
    Then I should see "[Check Boxes] (Q1)"
    And I should see "Select one or two choices only"
    And I add a "Dropdown Box" question and I fill the form with:
      | Question Name | Q2 |
      | No | n |
      | Question Text | Select one choice |
      | Possible answers | One,Two,Three,Four |
    Then I should see "[Dropdown Box] (Q2)"
    And I should see "Select one choice"
    And I log out

  @javascript
  Scenario: Student completes first question and saves response. Resumes with saved response already present.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Questionnaire 1"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Questionnaire 1"
    And I set the field "One" to "checked"
    And I set the field "Two" to "checked"
    And I set the field "Select one choice" to "Four"
    And I press "Save and exit"
    Then I should see "Your progress has been saved."
    And I should see "Resume questionnaire"

    And I am on "Course 1" course homepage
    And I follow "Questionnaire 1"
    Then I should see "Resume questionnaire"
    And I navigate to "Resume questionnaire" in current page administration
    Then I should see "Questionnaire 1"
    And I should see "Select one or two choices only"
    And the field "One" matches value "checked"
    And the field "Two" matches value "checked"
    And the field "Three" does not match value "checked"
    And the field "Four" does not match value "checked"
    And the field "Select one choice" matches value "Four"
    And I set the field "Two" to "0"
    And I set the field "Three" to "checked"
    And I press "Save and exit"
    Then I should see "Your progress has been saved."
    And I should see "Resume questionnaire"

    And I am on "Course 1" course homepage
    And I follow "Questionnaire 1"
    And I navigate to "Resume questionnaire" in current page administration
    Then I should see "Questionnaire 1"
    And I should see "Select one or two choices only"
    And the field "One" matches value "checked"
    And the field "Two" does not match value "checked"
    And the field "Three" matches value "checked"
    And the field "Four" does not match value "checked"
    And the field "Select one choice" matches value "Four"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I should not see "Resume questionnaire"

    And I am on "Course 1" course homepage
    And I follow "Questionnaire 1"
    Then I should not see "Resume questionnaire"
