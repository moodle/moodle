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
    And I am on "Course 1" course homepage
    Then "Legacy course files" "link" should exist in current page administration
    And I navigate to "Legacy course files" node in "Course administration"
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
    And I am on "Course 1" course homepage
    Then "Legacy course files" "link" should exist in current page administration
    And I navigate to "Legacy course files" node in "Course administration"
    And I press "Edit legacy course files"
    And "Add..." "link" should not be visible
    And "Create folder" "link" should not be visible
