@mod @mod_questionnaire
Feature: Questionnaires can be public, private or template
  In order to view a questionnaire
  As a user
  The type of the questionnaire affects how it is displayed.

@javascript
  Scenario: Add a template questionnaire
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | manager1 | C1 | manager |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 |
    And I log in as "manager1"
    And I am on site homepage
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Advanced settings" in current page administration
    And I should see "Content options"
    And I set the field "id_realm" to "template"
    And I press "Save and display"
    Then I should see "Template questionnaires are not viewable"

@javascript
  Scenario: Add a questionnaire from a public questionnaire
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | manager1 | C1 | manager |
      | manager1 | C2 | manager |
      | student1 | C2 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 |
    And the following config values are set as admin:
      | coursebinenable | 0 | tool_recyclebin |
    And I log in as "manager1"
    And I am on site homepage
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Check Boxes" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Min. forced responses | 1 |
      | Max. forced responses | 2 |
      | Question Text | Select one or two choices only |
      | Possible answers | One,Two,Three,Four |
# Neither of the following steps work in 3.2, since the admin options are not available on any page but "view".
    And I follow "Advanced settings"
    And I should see "Content options"
    And I set the field "id_realm" to "public"
    And I press "Save and return to course"
# Verify that a public questionnaire cannot be used in the same course.
    And I turn editing mode on
    And I add a "Questionnaire" to section "1"
    And I expand all fieldsets
    Then I should see "(No public questionnaires.)"
    And I press "Cancel"
# Verify that a public questionnaire can be used in a different course.
    And I am on site homepage
    And I am on "Course 2" course homepage
    And I add a "Questionnaire" to section "1"
    And I expand all fieldsets
    And I set the field "name" to "Questionnaire from public"
    And I click on "Test questionnaire [Course 1]" "radio"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 2" course homepage
    And I follow "Questionnaire from public"
    Then I should see "Answer the questions..."
# Verify message for public questionnaire that has been deleted.
    And I log out
    And I log in as "manager1"
    And I am on site homepage
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I delete "Test questionnaire" activity
    And I am on site homepage
    And I am on "Course 2" course homepage
    And I follow "Questionnaire from public"
    Then I should see "This questionnaire used to depend on a Public questionnaire which has been deleted."
    And I should see "It can no longer be used and should be deleted."
    And I log out
    And I log in as "student1"
    And I am on "Course 2" course homepage
    And I follow "Questionnaire from public"
    Then I should see "This questionnaire is no longer available. Ask your teacher to delete it."