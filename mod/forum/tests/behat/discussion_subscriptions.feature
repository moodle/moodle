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

  @javascript
  Scenario: An optional forum can have discussions subscribed to
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 0                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I can subscribe to this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can subscribe to this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can subscribe to this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can subscribe to this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I follow "Subscribe to forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I can unsubscribe from this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I unsubscribe from this forum
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I can subscribe to this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"

  @javascript
  Scenario: An automatic subscription forum can have discussions unsubscribed from
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 2                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I can unsubscribe from this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can unsubscribe from this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can unsubscribe from this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can unsubscribe from this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I unsubscribe from this forum
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I can subscribe to this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I subscribe to this forum
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And I can unsubscribe from this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"

  @javascript
  Scenario: A user does not lose their preferences when a forum is switch from optional to automatic
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 0                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I can subscribe to this forum
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject one" "table_row"
    And I can subscribe to this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as admin
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I can unsubscribe from this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    When I unsubscribe from this forum
    Then I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And I can subscribe to this forum
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"

  Scenario: An optional forum prompts a user to subscribe to a discussion when posting unless they have already chosen not to subscribe
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 0                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I should see "Subscribe to forum"
    And I reply "Test post subject one" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 1 |
    And I reply "Test post subject two" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
      | Discussion subscription | 0 |
    And I am on the "Test forum name" "forum activity" page
    Then "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I follow "Test post subject one"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "Send me notifications of new posts in this discussion"
    And I follow "Test forum name"
    And I follow "Test post subject two"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "I don't want to be notified of new posts in this discussion"

  Scenario: An automatic forum prompts a user to subscribe to a discussion when posting unless they have already chosen not to subscribe
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 2                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    # added for this scenario
    And the following "users" exist:
      | username | firstname | lastname | email                   |
      | student2 | Student   | Two      | student.two@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | C1     | student |
    And I log out
    When I am on the "Test forum name" "forum activity" page logged in as student2
    And I should see "Unsubscribe from forum"
    And I reply "Test post subject one" post from "Test forum name" forum with:
      | Subject                 | Reply 1 to discussion 1               |
      | Message                 | Discussion contents 1, second message |
      | Discussion subscription | 1                                     |
    And I reply "Test post subject two" post from "Test forum name" forum with:
      | Subject                 | Reply 1 to discussion 1               |
      | Message                 | Discussion contents 1, second message |
      | Discussion subscription | 0                                     |
    And I am on the "Test forum name" "forum activity" page
    Then "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    And I follow "Test post subject one"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "Send me notifications of new posts in this discussion"
    And I am on the "Test forum name" "forum activity" page
    And I follow "Test post subject two"
    And I follow "Reply"
    And the field "Discussion subscription" matches value "I don't want to be notified of new posts in this discussion"

  Scenario: A guest should not be able to subscribe to a discussion
    Given the following "activities" exist:
      | activity    | name            | intro                  | course               | section | idnumber  | type    |
      | forum       | Test forum name | Test forum description | Acceptance test site | 1       | forum1    | general |
    And I am on site homepage
    And I turn editing mode on
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I log out
    When I log in as "guest"
    And I follow "Test forum name"
    Then "Subscribe to this discussion" "checkbox" should not exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should not exist in the "Test post subject one" "table_row"
    And I follow "Test post subject one"
    And "Subscribe to this discussion" "checkbox" should not exist
    And "Unsubscribe from this discussion" "checkbox" should not exist

  Scenario: A user who is not logged in should not be able to subscribe to a discussion
    Given the following "activities" exist:
      | activity    | name            | intro                  | course               | section | idnumber  | type    |
      | forum       | Test forum name | Test forum description | Acceptance test site | 1       | forum1    | general |
    And I am on site homepage
    And I turn editing mode on
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I log out
    When I follow "Test forum name"
    Then "Subscribe to this discussion" "checkbox" should not exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should not exist in the "Test post subject one" "table_row"
    And I follow "Test post subject one"
    And "Subscribe to this discussion" "checkbox" should not exist
    And "Unsubscribe from this discussion" "checkbox" should not exist

  Scenario: A user can toggle their subscription preferences when viewing a discussion
    Given the following "activity" exists:
      | activity       | forum                  |
      | course         | C1                     |
      | idnumber       | forum1                 |
      | name           | Test forum name        |
      | intro          | Test forum description |
      | type           | general                |
      | forcesubscribe | 0                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I log out
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to forum" "link" should exist
    And I follow "Test post subject one"
    And "You are not subscribed to this discussion. Click to subscribe" "link" should exist
    And I follow "Test forum name"
    And I follow "Subscribe to forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And "Unsubscribe from forum" "link" should exist
    And I follow "Test post subject one"
    And "You are subscribed to this discussion. Click to unsubscribe" "link" should exist
    And I follow "You are subscribed to this discussion. Click to unsubscribe"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And I follow "Test post subject one"
    And "You are not subscribed to this discussion. Click to subscribe" "link" should exist
    And I follow "Test forum name"
    And I follow "Unsubscribe from forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And "Subscribe to forum" "link" should exist
    And I follow "Test post subject one"
    And "You are not subscribed to this discussion. Click to subscribe" "link" should exist
    And I follow "You are not subscribed to this discussion. Click to subscribe"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test forum name'"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And I follow "Subscribe to forum"
    And I should see "Student One will be notified of new posts in 'Test forum name'"
    And "Unsubscribe from forum" "link" should exist
    And I follow "Test post subject one"
    And "You are subscribed to this discussion. Click to unsubscribe" "link" should exist
    And I follow "Test forum name"
    And I follow "Unsubscribe from forum"
    And I should see "Student One will NOT be notified of new posts in 'Test forum name'"
    And "Subscribe to forum" "link" should exist
    And I follow "Test post subject one"
    And "You are not subscribed to this discussion. Click to subscribe" "link" should exist
