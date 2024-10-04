@core @core_admin
Feature: Set admin settings value
  In order to set admin settings value
  As an admin
  I need to set admin setting value and verify it is applied

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course fullname | C_shortname | 0 |
    And I log in as "admin"
    And I am on site homepage
    And I should see "Course fullname"
    And I should not see "C_shortname Course fullname"

  Scenario: set admin value with full name
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And I am on site homepage
    Then I should see "C_shortname Course fullname"

  Scenario: set admin value with short name
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And I am on site homepage
    Then I should see "C_shortname Course fullname"
