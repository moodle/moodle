@mod @mod_survey
Feature: A teacher can set three types of survey activity
  In order to use verified survey instruments
  As a teacher
  I need to set survey activities and select which survey type suits my needs

  Scenario: Switching between the three survey types
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Survey" to section "1" and I fill the form with:
      | Name | Test survey name |
      | Survey type | ATTLS (20 item version) |
      | Description | Test survey description |
    And I follow "Test survey name"
    Then I should see "Attitudes Towards Thinking and Learning"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Survey type | Critical incidents |
    And I press "Save and display"
    And I should see "At what moment in class were you most engaged as a learner?"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Survey type | COLLES (Preferred and Actual) |
    And I press "Save and display"
    And I should see "In this online unit..."
    And I should see "my learning focuses on issues that interest me."
