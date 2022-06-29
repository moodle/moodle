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

  Scenario: A disallowed subscription forum cannot be subscribed to
    Given the following "activity" exists:
      | activity         | forum                  |
      | course           | C1                     |
      | idnumber         | forum1                 |
      | name             | Test forum name        |
      | intro            | Test forum description |
      | type             | general                |
      | section          | 1                      |
    And I am on "Course 1" course homepage
    Given I follow "Test forum name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Subscription mode | Subscription disabled |
    And I press "Save and return to course"
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
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | section        | 1                      |
    And I am on "Course 1" course homepage
    Given I follow "Test forum name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Subscription mode | Forced subscription |
    And I press "Save and return to course"
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
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | section        | 1                      |
    And I am on "Course 1" course homepage
    Given I follow "Test forum name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Subscription mode | Optional subscription |
    And I press "Save and return to course"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should see "Subscribe to forum"
    And I should not see "Unsubscribe from forum"
    And I follow "Subscribe to forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I should see "Unsubscribe from forum"
    And I should not see "Subscribe to forum"

  Scenario: An Automatic forum can be unsubscribed from
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | section        | 1                      |
    And I am on "Course 1" course homepage
    Given I follow "Test forum name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should see "Unsubscribe from forum"
    And I should not see "Subscribe to forum"
    And I follow "Unsubscribe from forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to forum"
    And I should not see "Unsubscribe from forum"
