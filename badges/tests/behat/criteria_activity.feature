@mod @mod_quiz @core @core_badges @_file_upload @javascript
Feature: Award badges based on activity completion
  In order to ensure a student has learned the material before being marked complete
  As a teacher
  I need to set a quiz to award a badge when upon completion when the student receives a passing grade, or completed_fail if they use all attempts without passing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | grade_item_advanced | hiddenuntil |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity   | name           | course | idnumber | attempts | gradepass | completion | completionattemptsexhausted | completionpass | completionusegrade |
      | quiz       | Test quiz name | C1     | quiz1    | 2        | 5.00      | 2          | 1                           | 1              | 1                  |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test quiz name"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "Answer the first question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
      | issuername | Tester of course badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Activity completion"
    And I set the field "Quiz - Test quiz name" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I should see "Recipients (0)"
    And I log out

  Scenario: Student earns a badge using activity completion, but does not get passing grade
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And the "Test quiz name" "quiz" activity with "auto" completion should be marked as not complete
    And I follow "Test quiz name"
    And I press "Re-attempt quiz"
    And I set the field "False" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge"
    And I should see "Recipients (1)"
