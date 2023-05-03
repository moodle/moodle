@block @block_recentlyaccesseditems @javascript
Feature: The recently accessed items block allows users to easily access their most recently visited items
  In order to access the most recent items accessed
  As a user
  I can use the recently accessed items block in my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And the following "activity" exists:
      | course   | C1              |
      | activity | forum           |
      | idnumber | Test forum name |
      | name     | Test forum name |
    And I log in as "student1"

  Scenario: User has not accessed any item
    Then I should see "No recent items" in the "Recently accessed items" "block"

  Scenario: User has accessed some items
    Given I change window size to "large"
    When I am on the "Test forum name" "forum activity" page
    And I follow "Dashboard"
    Then I should see "Test forum name" in the "Recently accessed items" "block"
    And I should not see "Show more items" in the "Recently accessed items" "block"

  Scenario: User has accessed more than 3 items
    Given the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     |
      | book       | Test book name         |                               | C1     | book1       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     |
      | data       | Test database name     | Test database description     | C1     | data1       |
    And I change window size to "large"
    And I am on the "Test forum name" "forum activity" page
    And I am on the "Test database name" "data activity" page
    And I am on the "Test assignment name" "assign activity" page
    And I am on the "Test book name" "book activity" page
    And I am on the "Test choice name" "choice activity" page
    When I follow "Dashboard"
    Then I should see "Show more items" in the "Recently accessed items" "block"
    And I should not see "Test forum name" in the "Recently accessed items" "block"
    And I click on "Show more items" "button" in the "Recently accessed items" "block"
    And I should see "Test forum name" in the "Recently accessed items" "block"
    And I turn editing mode on
    And I am on homepage
    And I configure the "Recently accessed items" block
    And I set the following fields to these values:
      | Region | content |
    And I press "Save changes"
    And I turn editing mode off
    And I should not see "Show more items" in the "Recently accessed items" "block"
