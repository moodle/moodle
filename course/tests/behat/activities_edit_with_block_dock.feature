@core @core_course
Feature: Open the edit menu when a block is docked
  In order to edit an activity with a block docked
  As a teacher
  I need to open the action menu

  @javascript
  Scenario: Open the action menu with a block docked
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
    And I dock "Navigation" block
    When I open "Test glossary name" actions menu
    Then "Test glossary name" actions menu should be open
    And I reload the page
    When I open "Test glossary name" actions menu
    Then "Test glossary name" actions menu should be open
