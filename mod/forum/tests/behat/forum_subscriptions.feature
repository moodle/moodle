@mod @mod_forum
Feature: A user can control their own subscription preferences for a forum
  In order to receive notifications for things I am interested in
  As a user
  I need to choose my forum subscriptions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student   | One      | student.one@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: A disallowed subscription forum cannot be subscribed to
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Subscription disabled |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: A forced subscription forum cannot be subscribed to
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Forced subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: An optional forum can be subscribed to
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And I should not see "Subscribe to this forum"

  Scenario: An Automatic forum can be unsubscribed from
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Auto subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should see "Unsubscribe from this forum"
    And I should not see "Subscribe to this forum"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
