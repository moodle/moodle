@mod @mod_forum
Feature: A user can control their own subscription preferences for a discussion
  In order to receive notifications for things I am interested in
  As a user
  I need to choose my discussion subscriptions

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
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: An optional forum can have discussions subscribed to
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    Then I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"

  Scenario: An automatic subscription forum can have discussions unsubscribed from
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Auto subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    Then I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"

  Scenario: A user does not lose their preferences when a forum is switch from optional to automatic
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test forum name"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    When I follow "Unsubscribe from this forum"
    Then I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"

  Scenario: A user does not lose their preferences when a forum is switch from optional to automatic
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test forum name"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    When I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    Then I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"

  Scenario: An optional forum prompts a user to subscribe to a discussion when posting unless they have already chosen not to subscribe
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Subscribe to this forum"
    And I reply "Test post subject one" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 1 |
    And I reply "Test post subject two" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 0 |
    And I follow "Test forum name"
    Then "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Test post subject one"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "Send me notifications of new posts in this discussion"
    And I follow "Test forum name"
    And I follow "Test post subject two"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "I don't want to be notified of new posts in this discussion"

  Scenario: An automatic forum prompts a user to subscribe to a discussion when posting unless they have already chosen not to subscribe
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Auto subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I should see "Unsubscribe from this forum"
    And I reply "Test post subject one" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 1 |
    And I reply "Test post subject two" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 0 |
    And I follow "Test forum name"
    Then "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Test post subject one"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "Send me notifications of new posts in this discussion"
    And I follow "Test forum name"
    And I follow "Test post subject two"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "I don't want to be notified of new posts in this discussion"

 Scenario: A guest should not be able to subscribe to a discussion
   Given I am on site homepage
   And I add a "Forum" to section "1" and I fill the form with:
     | Forum name        | Test forum name |
     | Forum type        | Standard forum for general use |
     | Description       | Test forum description |
   And I add a new discussion to "Test forum name" forum with:
     | Subject | Test post subject one |
     | Message | Test post message one |
   And I log out
   When I log in as "guest"
   And I follow "Test forum name"
   Then "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject one" "table_row"
   And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject one" "table_row"
   And I follow "Test post subject one"
   And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist
   And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist

 Scenario: A user who is not logged in should not be able to subscribe to a discussion
   Given I am on site homepage
   And I add a "Forum" to section "1" and I fill the form with:
     | Forum name        | Test forum name |
     | Forum type        | Standard forum for general use |
     | Description       | Test forum description |
   And I add a new discussion to "Test forum name" forum with:
     | Subject | Test post subject one |
     | Message | Test post message one |
   And I log out
   When I follow "Test forum name"
   Then "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject one" "table_row"
   And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject one" "table_row"
   And I follow "Test post subject one"
   And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist
   And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist

  Scenario: A user can toggle their subscription preferences when viewing a discussion
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Test forum name |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Optional subscription |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    When I follow "Test post subject one"
    Then I should see "Subscribe to this forum"
    And I should see "Subscribe to this discussion"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I follow "Test post subject one"
    And I should see "Unsubscribe from this forum"
    And I should see "Unsubscribe from this discussion"
    And I follow "Unsubscribe from this discussion"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I follow "Test post subject one"
    And I should see "Unsubscribe from this forum"
    And I should see "Subscribe to this discussion"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I follow "Test post subject one"
    And I should see "Subscribe to this forum"
    And I should see "Subscribe to this discussion"
    And I follow "Subscribe to this discussion"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I should see "Subscribe to this forum"
    And I should see "Unsubscribe from this discussion"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I follow "Test post subject one"
    And I should see "Unsubscribe from this forum"
    And I should see "Unsubscribe from this discussion"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I follow "Test post subject one"
    And I should see "Subscribe to this forum"
    And I should see "Subscribe to this discussion"
