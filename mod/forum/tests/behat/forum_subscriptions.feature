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
    And the following "activities" exist:
      | activity | course | idnumber | name            | type    |
      | forum    | C1     | forum1   | Test forum name | general |
    And the following "mod_forum > discussions" exist:
      | forum  | course | user  | name              | message           |
      | forum1 | C1     | admin | Test post subject | Test post message |
    And I log in as "admin"

  Scenario: A disallowed subscription forum cannot be subscribed to
    Given I am on the "Test forum name" "forum activity editing" page
    And I set the following fields to these values:
      | Subscription mode | Subscription disabled |
    And I press "Save and return to course"
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: A forced subscription forum cannot be subscribed to
    Given I am on the "Test forum name" "forum activity editing" page
    And I set the following fields to these values:
      | Subscription mode | Forced subscription |
    And I press "Save and return to course"
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: An optional forum can be subscribed to
    Given I am on the "Test forum name" "forum activity editing" page
    And I set the following fields to these values:
      | Subscription mode | Optional subscription |
    And I press "Save and return to course"
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should see "Subscribe to forum"
    And I should not see "Unsubscribe from forum"
    And I follow "Subscribe to forum"
    And I should see "You will be notified of new posts in the forum 'Test forum name'"
    And I should see "Unsubscribe from forum"
    And I should not see "Subscribe to forum"

  Scenario: An Automatic forum can be unsubscribed from
    Given I am on the "Test forum name" "forum activity editing" page
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should see "Unsubscribe from forum"
    And I should not see "Subscribe to forum"
    And I follow "Unsubscribe from forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to forum"
    And I should not see "Unsubscribe from forum"
