@core @core_files
Feature: Course files
  In order to add legacy files
  As a user
  I need to upload files

  @javascript
  Scenario: Add legacy files
    Given the following "courses" exist:
      | fullname | shortname | category | legacyfiles |
      | Course 1 | C1 | 0 | 2 |
    And the following config values are set as admin:
      | legacyfilesinnewcourses | 1 |
      | legacyfilesaddallowed   | 1 |
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Legacy course files"
    And I follow "Legacy course files"
    And I press "Edit legacy course files"
    And "Add..." "link" should be visible
    And "Create folder" "link" should be visible

  @javascript
  Scenario: Add legacy file disabled
    Given the following "courses" exist:
      | fullname | shortname | category | legacyfiles |
      | Course 1 | C1 | 0 | 2 |
    And the following config values are set as admin:
      | legacyfilesinnewcourses | 1 |
      | legacyfilesaddallowed   | 0 |
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Legacy course files"
    And I follow "Legacy course files"
    And I press "Edit legacy course files"
    And "Add..." "link" should not be visible
    And "Create folder" "link" should not be visible
