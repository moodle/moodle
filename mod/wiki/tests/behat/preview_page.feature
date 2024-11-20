@mod @mod_wiki
Feature: Edited wiki pages may be previewed before saving
  In order to avoid silly mistakes
  As a user
  I need to preview pages before saving changes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity      | wiki                  |
      | course        | C1                    |
      | name          | Test wiki name        |
      | wikimode      | collaborative         |
    And I am on the "Test wiki name" "wiki activity" page logged in as student1
    When I press "Create page"
    And I set the following fields to these values:
      | HTML format | Student page contents to be previewed |
    And I press "Preview"
    Then I expand all fieldsets
    And I should see "This is a preview. Changes have not been saved yet"
    And I should see "Student page contents to be previewed"
    And I press "Save"
    And I should see "Student page contents to be previewed"
    And I select "Edit" from the "jump" singleselect

  @javascript
  Scenario: Page contents preview before saving with Javascript enabled
    Then the field "HTML format" matches value "Student page contents to be previewed"
    And I press "Cancel"

  Scenario: Page contents preview before saving with Javascript disabled
    Then the field "HTML format" matches value "Student page contents to be previewed"
    And I press "Cancel"
