@core @core_message @javascript
Feature: Message admin settings
  In order to manage the messaging system
  As an admin
  I need to be able to enabled/disabled site-wide messaging system

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@example.com |

  Scenario: enable site messaging
    Given the following config values are set as admin:
      | messaging | 1 |
    When I log in as "admin"
    Then "Toggle messaging drawer" "icon" should exist
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "User One"
    And I follow "User One"
    And "Add to contacts" "link" should exist

  Scenario: disable site messaging
    Given the following config values are set as admin:
      | messaging | 0 |
    When I log in as "admin"
    Then "Toggle messaging drawer" "icon" should not exist
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "User One"
    And I follow "User One"
    And "Add to contacts" "link" should not exist
