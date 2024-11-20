@core @core_message @javascript
Feature: Message admin settings
  In order to manage the messaging system
  As an admin
  I need to be able to enabled/disabled site-wide messaging system

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |

  Scenario: enable site messaging
    Given the following config values are set as admin:
      | messaging | 1 |
    When I log in as "admin"
    Then "Toggle messaging drawer" "icon" should exist
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "User One"
    And I follow "User One"
    And "Add to contacts" "link" should exist
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And the "With selected users..." select box should contain "Send a message"

  Scenario: disable site messaging
    Given the following config values are set as admin:
      | messaging | 0 |
    When I log in as "admin"
    Then "Toggle messaging drawer" "icon" should not exist
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "User One"
    And I follow "User One"
    And "Add to contacts" "link" should not exist
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And the "With selected users..." select box should not contain "Send a message"
