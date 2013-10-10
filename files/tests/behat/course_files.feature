@core @core_files @_only_local
Feature: Course files
  In order to add legacy files
  As a user
  I need to upload files

  @javascript
  Scenario: Add legacy files
    Given the following "courses" exists:
      | fullname | shortname | category | legacyfiles |
      | Course 1 | C1 | 0 | 2 |
    And I log in as "admin"
    And I set the following administration settings values:
      | Legacy course files in new courses | 1 |
      | Allow adding to legacy course files | 1 |
    When I follow "Home"
    And I follow "Course 1"
    Then I should see "Legacy course files"
    And I follow "Legacy course files"
    And I press "Edit legacy course files"
    And I should see "Add..."
    And I should see "Create folder"

  @javascript
  Scenario: Add legacy file disabled
    Given the following "courses" exists:
      | fullname | shortname | category | legacyfiles |
      | Course 1 | C1 | 0 | 2 |
    And I log in as "admin"
    And I set the following administration settings values:
      | Legacy course files in new courses | 1 |
      | Allow adding to legacy course files | 0 |
    When I follow "Home"
    And I follow "Course 1"
    Then I should see "Legacy course files"
    And I follow "Legacy course files"
    And I press "Edit legacy course files"
    And I should not see "Add..."
    And I should not see "Create folder"
