@mod @mod_moodleoverflow
Feature: A user can control their own moodleoverflow subscription preferences for a discussion
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

  Scenario: An optional moodleoverflow can have discussions subscribed to
    Given the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       | forcesubscribe |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow | 0              |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test moodleoverflow name"
    Then I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Subscribe to this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test moodleoverflow name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"

  Scenario: An automatic subscription moodleoverflow can have discussions unsubscribed from
    Given the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       | forcesubscribe |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow | 2              |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test moodleoverflow name"
    Then I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are not subscribed to this discussion. Click to subscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I click on "You are subscribed to this discussion. Click to unsubscribe." "link" in the "Test post subject one" "table_row"
    And I should see "Student One will NOT be notified of new posts in 'Test post subject one' of 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Unsubscribe from this forum"
    And I should see "Student One will NOT be notified of new posts in 'Test moodleoverflow name'"
    And I should see "Subscribe to this forum"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should exist in the "Test post subject two" "table_row"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject one" "table_row"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should exist in the "Test post subject two" "table_row"
