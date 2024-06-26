@mod @mod_feedback
Feature: Managing feedback questions
  In order to manage feedback questions
  As a teacher
  I need to be able to create, edit and delete feedback questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                         | course | idnumber    |
      | feedback   | Learning experience course 1 | C1     | feedback1   |

  Scenario: Teacher can create a new feedback question
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I add a "Short text answer" question to the feedback with:
      | Question         | Is it me you're looking for? |
      | Label            | q1                           |
    Then I should see "(q1) Is it me you're looking for?"

  @javascript
  Scenario: Teacher can edit feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    And I add a "Short text answer" question to the feedback with:
      | Question         | Is it me you're looking for? |
      | Label            | q1                           |
    When I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And I set the field "Question" to "Can you see it in my eyes?"
    And I press "Save changes to question"
    Then I should see "(q1) Can you see it in my eyes?"
    And I should not see "(q1) Is it me you're looking for?"

  @javascript
  Scenario: Teacher can edit and save as new feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    And I add a "Short text answer" question to the feedback with:
      | Question         | Is it me you're looking for? |
      | Label            | q1                           |
    When I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And I set the field "Question" to "You can se it in my eyes?"
    And I press "Save as new question"
    Then I should see "(q1) Is it me you're looking for?"
    And I should see "(q1) You can se it in my eyes?"

  @javascript
  Scenario: Teacher can delete feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    And I add a "Short text answer" question to the feedback with:
      | Question         | Is it me you're looking for? |
      | Label            | q1                           |
    When I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Delete question" in the open action menu
    And I click on "Yes" "button" in the "Confirmation" "dialogue"
    Then I should not see "(q1) Is it me you're looking for?"

  @javascript
  Scenario: Teacher can mark as required feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    And I add a "Short text answer" question to the feedback with:
      | Question         | Is it me you're looking for? |
      | Label            | q1                           |
      | Required         | 0                            |
    When I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Set as required" in the open action menu
    And I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    Then the field "Required" matches value "1"
    And I press "Cancel"
    And I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Set as not required" in the open action menu
    And I open the action menu in "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And the field "Required" matches value "0"
