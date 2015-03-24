@core @core_completion
Feature: Allow students to manually mark an activity as complete
  In order to let students decide when an activity is completed
  As a teacher
  I need to allow students to mark activities as completed

  @javascript
  Scenario: Mark an activity as completed
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | enablecompletion | 1 |
      | enableavailability | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    Then "Student First" user has not completed "Test forum name" activity
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I press "Mark as complete: Test forum name"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Reports" node
    And I follow "Activity completion"
    And "Student First" user has completed "Test forum name" activity
