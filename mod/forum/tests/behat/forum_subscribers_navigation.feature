@mod @mod_forum @javascript
Feature: A teacher or admin can view subscriptions tab

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

  Scenario: A teacher views view subscribers by default and views the Subscribers heading
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    When I navigate to "Subscriptions" in current page administration
    Then I should see "View subscribers" in the "//div[@class='urlselect']//option[@selected]" "xpath_element"
    And I should see "Subscribers"
    And I should see "There are no subscribers yet for this forum"

  Scenario: A teacher selects forced subscription and subscribers selector is not visible
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I navigate to "Subscriptions" in current page administration
    When I select "Forced subscription" from the "Subscription mode" singleselect
    And I should see "Everyone is now subscribed to this forum"
    Then I should not see "View subscribers"
    And I should not see "Manage subscribers"
    And I should not see "Manage subscribers"
    # Now select Optional subscription
    And I select "Optional subscription" from the "Subscription mode" singleselect
    And I should see "Everyone can now choose to be subscribed"
    And I should see "View subscribers"

  Scenario: A teacher selects forced subscription and subscribers selector is not visible
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I navigate to "Subscriptions" in current page administration
    When I select "Manage subscribers" from the "jump" singleselect
    And I should see "Manage subscribers"
    Then "Subscription mode" "select" should not exist
    And I should not see "Optional subscription"
    And I should not see "Forced subscription"
    And I should not see "Auto subscription"
    And I should not see "Subscription disabled"

  Scenario: A teacher selects reports tab and verify the heading
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I should see "There are no discussion topics yet in this forum" in the "//div[contains(@class, 'alert-info')]" "xpath_element"
    And I navigate to "Reports" in current page administration
    And I should see "Forum summary report"
    And I should see "Nothing to display" in the "//div[contains(@class, 'alert-info')]" "xpath_element"
