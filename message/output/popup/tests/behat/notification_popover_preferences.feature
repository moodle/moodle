@message @message_popup
Feature: Notification popover preferences
  In order to modify my notification preferences
  As a user
  I can navigate to the preferences page from the popover

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |

  @javascript
  Scenario: User navigates to preferences page
    Given I log in as "user1"
    And I open the notification popover
    When I follow "Notification preferences"
    Then I should see "Notification preferences"
