@mod @mod_forum
Feature: A user can navigate to previous and next discussions
  In order to get go the previous discussion
  As a user
  I need to click on the previous discussion link

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "admin"
    And I follow "Course 1"
    And I navigate to "Groups" node in "Users"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 2 |
    And I press "Save changes"
    And I add "Student 1" user to "Group 1" group members
    And I add "Student 2" user to "Group 2" group members
    And I am on homepage
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: A user can navigate between discussions
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 3 |
      | Message | Test post message |
    When I follow "Discussion 3"
    Then I should not see "Discussion 1"
    And I should see "Discussion 2"
    And I follow "Discussion 2"
    And I should see "Discussion 1"
    And I should see "Discussion 3"
    And I follow "Discussion 1"
    And I should see "Discussion 2"
    And I should not see "Discussion 3"
    And I follow "Reply"
    And I set the following fields to these values:
      | Message | Answer to discussion |
    And I press "Post to forum"
    And I should not see "Discussion 2"
    And I should see "Discussion 3"
    And I follow "Discussion 3"
    And I should see "Discussion 1"
    And I should see "Discussion 2"
    And I follow "Discussion 2"
    And I should not see "Discussion 1"
    And I should see "Discussion 3"

  @javascript
  Scenario: A user can navigate between discussions with visible groups
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Group mode | Visible groups |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 0 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 0 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 1 |
      | Message | Test post message |
      | Group   | Group 1 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 1 |
      | Message | Test post message |
      | Group   | Group 1 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 2 |
      | Message | Test post message |
      | Group   | Group 2 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 2 |
      | Message | Test post message |
      | Group   | Group 2 |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I set the field "Visible groups" to "All participants"
    And I follow "Discussion 1 Group 0"
    Then I should see "Discussion 2 Group 0"
    And I should not see "Group 1"
    And I should not see "Group 2"
    And I follow "Discussion 2 Group 0"
    And I should see "Discussion 1 Group 0"
    And I should see "Discussion 1 Group 1"
    And I follow "Discussion 1 Group 1"
    And I should see "Discussion 2 Group 0"
    And I should see "Discussion 2 Group 1"
    And I follow "Test forum name"
    And I follow "Discussion 1 Group 2"
    And I should see "Discussion 2 Group 1"
    And I should see "Discussion 2 Group 2"
    And I follow "Test forum name"
    And I set the field "Visible groups" to "Group 1"
    And I follow "Discussion 1 Group 1"
    Then I should see "Discussion 2 Group 0"
    And I should see "Discussion 2 Group 1"
    And I follow "Discussion 2 Group 1"
    And I should see "Discussion 1 Group 1"
    And I should not see "Group 2"

  @javascript
  Scenario: A user can navigate between discussions with separate groups
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Group mode | Separate groups |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 0 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 0 |
      | Message | Test post message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 1 |
      | Message | Test post message |
      | Group   | Group 1 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 1 |
      | Message | Test post message |
      | Group   | Group 1 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 Group 2 |
      | Message | Test post message |
      | Group   | Group 2 |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 Group 2 |
      | Message | Test post message |
      | Group   | Group 2 |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test forum name"
    And I follow "Discussion 1 Group 1"
    Then I should see "Discussion 2 Group 0"
    And I should see "Discussion 2 Group 1"
    And I follow "Discussion 2 Group 1"
    And I should see "Discussion 1 Group 1"
    And I should not see "Group 2"
