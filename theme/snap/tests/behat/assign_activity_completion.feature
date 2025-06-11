@theme @theme_snap
Feature: View activity activity header and completion information in activities
  In order to have visibility of assignment completion requirements

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                            | assign        |
      | course                              | C1            |
      | idnumber                            | mh1           |
      | name                                | Music history |
      | section                             | 1             |
      | completion                          | 1             |
      | assignsubmission_onlinetext_enabled | 1              |
      | grade[modgrade_type]                | point         |
      | grade[modgrade_point]               | 100           |

  Scenario: View automatic completion items as a teacher
    Given I am on the "Music history" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Add requirements" to "1"
    And I set the following fields to these values:
      | completionview        | 1                                                 |
      | completionusegrade    | 1                                                 |
      | completionsubmit      | 1                                                 |
    And I press "Save and display"
    Then ".activity-header" "css_element" should exist
    And "Music history" should have the "View" completion condition
    And "Music history" should have the "Make a submission" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  @javascript
  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Add requirements" to "1"
    And I set the following fields to these values:
      | completionview        | 1                                                 |
      | completionusegrade    | 1                                                 |
      | completionsubmit      | 1                                                 |
    And I press "Save and display"
    And I log out
    And I am on the "Music history" "assign activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I am on the "Music history" "assign activity" page
    And I press "Add submission"
    Then I should not see "Music history" in the "#page-mast" "css_element"
    And I set the field "Online text" to "History of playing with drumsticks reversed"
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "assign activity" page logged in as teacher1
    And I click on "Grade" "link" in the ".tertiary-navigation" "css_element"
    And I set the field "Grade out of 100" to "33"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I log out
    When I am on the "Music history" "assign activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

    @javascript
    Scenario: Student should see the automatic completion criterias statuses of activities with completion grade
    Given the following "activities" exist:
      | activity   | name              | course | idnumber | gradepass | completion | completionusegrade |
      | quiz       | Activity sample 1 | C1     | quiz1    | 5.00      | 2          | 1                  |
      | quiz       | Activity sample 2 | C1     | quiz2    | 5.00      | 2          | 1                  |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And quiz "Activity sample 1" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Activity sample 2" contains the following questions:
      | question       | page |
      | First question | 1    |
    When I am on the "C1" "Course" page logged in as "student1"
    Then I click on "[id^='dropwdownbutton']" "css_element" in the "Activity sample 1" "activity"
    And "Receive a grade" "text" should exist in the "Activity sample 1" "activity"
    Then I click on "[id^='dropwdownbutton']" "css_element" in the "Activity sample 2" "activity"
    And "Receive a grade" "text" should exist in the "Activity sample 2" "activity"
    # Pass grade.
    And user "student1" has attempted "Activity sample 1" with responses:
      | slot | response |
      | 1    | True    |
    # Fail grade.
    And user "student1" has attempted "Activity sample 2" with responses:
      | slot | response |
      | 1    | False    |
    # After receiving a grade, the completion criteria dropdown should display "Done" instead of "To do", regardless of pass/fail.
    And I am on the "Course 1" course page
    Then "img[title='Not completed: Activity sample 1']" "css_element" should not exist in the "Activity sample 1" "activity"
    Then I click on "[id^='dropwdownbutton']" "css_element" in the "Activity sample 1" "activity"
    And "Receive a grade" "text" should exist in the "Activity sample 1" "activity"
    Then "img[title='Not completed: Activity sample 2']" "css_element" should not exist in the "Activity sample 2" "activity"
    And I click on "[id^='dropwdownbutton']" "css_element" in the "Activity sample 2" "activity"
    And "Receive a grade" "text" should exist in the "Activity sample 2" "activity"
