@mod @mod_data
Feature: Teachers can enable comments only if comments are enabled at site level
  In order to enable comments on entries
  As an admin
  I need to enable comments at site level

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on

  @javascript
  Scenario: Teacher can enable comments if they are enabled at site level
    Given I open the activity chooser
    And I click on "Add a new Database" "link" in the "Add an activity or resource" "dialogue"
    When I expand all fieldsets
    And "Allow comments on entries" "field" should exist
    And I set the field "Name" to "Test Database name"
    And I set the field "Allow comments on entries" to "Yes"
    And I press "Save and return to course"
    And I should see "Test Database name"

  @javascript
  Scenario: Teacher cannot enable comments if they are disabled at site level
    # Disable comments in site config.
    Given the following config values are set as admin:
      | usecomments | 0 |
    And I open the activity chooser
    And I click on "Add a new Database" "link" in the "Add an activity or resource" "dialogue"
    When I expand all fieldsets
    And I set the field "Name" to "Test Database name 2"
    And "Allow comments on entries" "field" should not exist
    Then I should see "No" in the "//*[@id=\"fitem_id_comments\"]/*[@data-fieldtype=\"selectyesno\"]" "xpath_element"
    And I press "Save and return to course"
    And I should see "Test Database name 2"
