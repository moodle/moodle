@tool @tool_installaddon @javascript
Feature: Marketplace activity chooser footer
  In order to browse plugins from the activity chooser
  As a teacher
  I need to see the Marketplace footer link when installaddon is the active footer plugin

  Background:
    Given the following config values are set as admin:
      | activitychooseractivefooter | tool_installaddon |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Activity chooser footer includes Marketplace link
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    Then "Browse more activities on" "text" should exist in the "Add an activity or resource" "dialogue"
    And "Marketplace" "link" should exist in the "Add an activity or resource" "dialogue"

  Scenario: Back button works from activity help when the Marketplace footer is active
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    Then "help" "core_course > Activity chooser screen" should be visible
    When I click on "Back" "button" in the "Add an activity or resource" "dialogue"
    Then "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Marketplace" "link" should exist in the "Add an activity or resource" "dialogue"
    And "Back" "button" should not be visible
