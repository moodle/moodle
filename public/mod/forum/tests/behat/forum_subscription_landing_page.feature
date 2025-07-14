@mod @mod_forum @javascript
Feature: A teacher or admin when changes the subscription mode should land in the subscriptions tab

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher  | Teacher   | Tom      | teacher@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher  | C1     | editingteacher |
    And the following "activity" exists:
      | course   | C1                             |
      | activity | forum                          |
      | name     | Test forum name                |

  Scenario: A teacher views subscriptions tab and changes the subscriptions mode to forced mode and lands in subscription tab
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I navigate to "Subscriptions" in current page administration
    When I select "Forced subscription" from the "Subscription mode" singleselect
    And I should see "Everyone is now subscribed to this forum"
    Then I should see "Forced subscription"
    And I should not see "View subscribers"
    And I should not see "Manage subscribers"
