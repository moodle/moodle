@message @message_popup
Feature: Message popover preferences
  In order to modify my message preferences
  As a user
  I can navigate to the preferences page from the popover

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@asd.com    |

  @javascript
  Scenario: User navigates to preferences page
    Given I log in as "user1"
    And I open the message popover
    When I follow "Message preferences"
    Then I should see "Message preferences"
