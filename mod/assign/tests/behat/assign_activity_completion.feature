@mod @mod_assign @core_completion
Feature: View activity completion in the assignment activity
  In order to have visibility of assignment completion requirements
  As a student
  I need to be able to view my assignment completion progress

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
      | activity                 | assign        |
      | course                   | C1            |
      | idnumber                 | mh1           |
      | name                     | Music history |
      | section                  | 1             |
      | completion               | 1             |
      | grade[modgrade_type]     | point         |
      | grade[modgrade_point]    | 100           |
    And the following "activity" exists:
      | activity                            | assign          |
      | course                              | C1              |
      | idnumber                            | mh2             |
      | name                                | Music history 2 |
      | section                             | 1               |
      | assignsubmission_onlinetext_enabled | 1               |
      | attemptreopenmethod                 | manual          |
      | maxattempts                         | -1              |
      | completion                          | 2               |
      | completionsubmit                    | 1               |
      | grade[modgrade_type]                | point           |
      | grade[modgrade_point]               | 100             |

  @javascript
  Scenario: The manual completion button will be shown on the course page if the Show activity completion conditions is set to Yes
    Given I am on the "Course 1" course page logged in as teacher1
    # Teacher view.
    And "Music history" should have the "Mark as done" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Music history" should exist
    And the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

  @javascript
  Scenario: The manual completion button will not be shown on the course page if the Show activity completion conditions is set to No
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "No"
    And I press "Save and display"
    # Teacher view.
    And "Completion" "button" should not exist in the "Music history" "activity"
    And I log out
    # Student view.
    When I am on the "Course 1" course page logged in as "student1"
    Then the manual completion button for "Music history" should not exist
    And I am on the "Music history" "assign activity" page
    And the manual completion button for "Music history" should exist

  @javascript
  Scenario: Use manual completion from the activity page
    Given I am on the "Music history" "assign activity" page logged in as teacher1
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    And I am on the "Music history" "assign activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

  Scenario: View automatic completion items as a teacher
    Given I am on the "Music history" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Add requirements         | 1                  |
      | View the activity   | 1                                                 |
      | completionusegrade  | 1                                                 |
      | completionsubmit    | 1                                                 |
    And I press "Save and display"
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Make a submission" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  @javascript
  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | assignsubmission_onlinetext_enabled | 1                                                 |
      | Add requirements         | 1                  |
      | View the activity                   | 1                                                 |
      | completionusegrade                  | 1                                                 |
      | completionsubmit                    | 1                                                 |
    And I press "Save and display"
    And I log out
    And I am on the "Music history" "assign activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I am on the "Music history" "assign activity" page
    And I press "Add submission"
    And I set the field "Online text" to "History of playing with drumsticks reversed"
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Vinnie Student1" "table_row"
    And I set the field "Grade out of 100" to "33"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And I log out
    When I am on the "Music history" "assign activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make a submission" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Automatic completion items should reset when a new attempt is manually given.
    Given I am on the "Music history 2" "assign activity" page logged in as student1
    And the "Make a submission" completion condition of "Music history 2" is displayed as "todo"
    And I press "Add submission"
    And I set the field "Online text" to "History of playing with drumsticks reversed"
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And the "Make a submission" completion condition of "Music history 2" is displayed as "done"
    And I log out
    And I am on the "Music history 2" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Vinnie Student1" "table_row"
    And I set the field "Grade out of 100" to "33"
    And I set the field "Notify student" to "0"
    And I set the field "Allow another attempt" to "Yes"
    And I press "Save changes"
    And I log out
    When I am on the "Music history 2" "assign activity" page logged in as student1
    And I should see "Reopened"
    And "Add a new attempt based on previous submission" "button" should exist
    Then the "Make a submission" completion condition of "Music history 2" is displayed as "todo"
