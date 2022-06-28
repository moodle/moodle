@mod @mod_moodleoverflow
Feature: A user can control their own subscription preferences for a moodleoverflow
  In order to receive notifications for things I am interested in
  As a user
  I need to choose my moodleoverflow subscriptions

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

  Scenario: A disallowed subscription moodleoverflow cannot be subscribed to
    Given the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       | forcesubscribe |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow | 3              |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test moodleoverflow name"
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: A forced subscription moodleoverflow cannot be subscribed to
    Given the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       | forcesubscribe |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow | 1              |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test moodleoverflow name"
    Then I should not see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And "You are subscribed to this discussion. Click to unsubscribe." "link" should not exist in the "Test post subject" "table_row"
    And "You are not subscribed to this discussion. Click to subscribe." "link" should not exist in the "Test post subject" "table_row"

  Scenario: An optional moodleoverflow can be subscribed to
    Given the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       | forcesubscribe |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow | 0              |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test moodleoverflow name"
    Then I should see "Subscribe to this forum"
    And I should not see "Unsubscribe from this forum"
    And I follow "Subscribe to this forum"
    And I should see "Student One will be notified of new posts in 'Test moodleoverflow name'"
    And I should see "Unsubscribe from this forum"
    And I should not see "Subscribe to this forum"
