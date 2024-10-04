@mod @mod_forum
Feature: A user can navigate to previous and next discussions
  In order to get go the previous discussion
  As a user
  I need to click on the previous discussion link

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
      | student2 | Student   | 2        | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | teacher1 | G1 |
      | teacher1 | G2 |
      | student1 | G1 |
      | student2 | G2 |

  Scenario: A user can navigate between discussions
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Test forum name        | C1     | forum        | 0         |
    And the following "mod_forum > discussions" exist:
      | user     | forum | name         | message           |
      | teacher1 | forum | Discussion 1 | Test post message |
      | teacher1 | forum | Discussion 2 | Test post message |
      | teacher1 | forum | Discussion 3 | Test post message |
    And I am on the "Test forum name" "forum activity" page logged in as teacher1
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
    # We need to wait a bit to guarantee that the post is created after the previous ones.
    # because there is a bug in the forum_get_discussion_neighbours() when all the discussion
    # last modified times are the same. See MDL-79247 for more details.
    And I wait "1" seconds
    And I press "Post to forum"
    And I should not see "Discussion 2"
    And I should see "Discussion 3"
    And I follow "Discussion 3"
    And I should see "Discussion 1"
    And I should see "Discussion 2"
    And I follow "Discussion 2"
    And I should not see "Discussion 1"
    And I should see "Discussion 3"

  Scenario: A user can navigate between discussions with visible groups
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Test forum name        | C1     | forum        | 2         |
    And the following "mod_forum > discussions" exist:
      | user     | forum | name                 | message           |
      | student1 | forum | Discussion 1 Group 0 | Test post message |
      | student1 | forum | Discussion 2 Group 0 | Test post message |
    And the following "mod_forum > discussions" exist:
      | user     | forum | name                 | message           | group |
      | student1 | forum | Discussion 1 Group 1 | Test post message | G1    |
      | student1 | forum | Discussion 2 Group 1 | Test post message | G1    |
      | student1 | forum | Discussion 1 Group 2 | Test post message | G2    |
      | student1 | forum | Discussion 2 Group 2 | Test post message | G2    |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I select "All participants" from the "Visible groups" singleselect
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
    And I select "Group 1" from the "Visible groups" singleselect
    And I follow "Discussion 1 Group 1"
    And I should see "Discussion 2 Group 0"
    And I should see "Discussion 2 Group 1"
    And I follow "Discussion 2 Group 1"
    And I should see "Discussion 1 Group 1"
    And I should not see "Group 2"

  Scenario: A user can navigate between discussions with separate groups
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Test forum name        | C1     | forum        | 1         |
    And the following "mod_forum > discussions" exist:
      | user     | forum | name                 | message           |
      | student1 | forum | Discussion 1 Group 0 | Test post message |
      | student1 | forum | Discussion 2 Group 0 | Test post message |
    And the following "mod_forum > discussions" exist:
      | user     | forum | name                 | message           | group |
      | student1 | forum | Discussion 1 Group 1 | Test post message | G1    |
      | student1 | forum | Discussion 2 Group 1 | Test post message | G1    |
      | student1 | forum | Discussion 1 Group 2 | Test post message | G2    |
      | student1 | forum | Discussion 2 Group 2 | Test post message | G2    |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I follow "Discussion 1 Group 1"
    Then I should see "Discussion 2 Group 0"
    And I should see "Discussion 2 Group 1"
    And I follow "Discussion 2 Group 1"
    And I should see "Discussion 1 Group 1"
    And I should not see "Group 2"
