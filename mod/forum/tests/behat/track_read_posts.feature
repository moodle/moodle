@mod @mod_forum
Feature: A teacher can set one of 3 possible options for tracking read forum posts
  In order to ease the forum posts follow up
  As a user
  I need to distinct the unread posts from the read ones

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | trackforums |
      | student1 | Student | 1 | student1@example.com | 1 |
      | student2 | Student | 2 | student2@example.com | 0 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |

  Scenario: Tracking forum posts off
    Given the following "activity" exists:
      | activity      | forum                           |
      | course        | C1                              |
      | idnumber      | forum1                          |
      | type          | general                         |
      | name          | Test forum name                 |
      | trackingtype  | 0                               |
    And the following "mod_forum > discussion" exists:
      | forum   | forum1            |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    When I am on the "Course 1" course page logged in as student1
    Then I should not see "1 unread post"
    And I follow "Test forum name"
    And I should not see "Track unread posts"

  Scenario: Tracking forum posts optional with user tracking on
    Given the following "activity" exists:
      | activity     | forum                  |
      | course       | C1                     |
      | idnumber     | forum1                 |
      | name         | Test forum name        |
      | type         | general                |
      | trackingtype | 1                      |
    And the following "mod_forum > discussion" exists:
      | forum   | forum1            |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    When I am on the "Course 1" course page logged in as student1
    Then I should see "1 unread post"
    And I follow "Test forum name"
    And I follow "Don't track unread posts"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"
    And I follow "Test forum name"
    And I follow "Track unread posts"
    And I wait to be redirected
    And I click on "1" "link" in the "Admin User" "table_row"
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"

  Scenario: Tracking forum posts optional with user tracking off
    Given the following "activity" exists:
      | activity     | forum                       |
      | course       | C1                          |
      | idnumber     | 00001                       |
      | name         | Test forum name             |
      | type         | general                     |
      | trackingtype | 1                           |
    And the following "mod_forum > discussion" exists:
      | forum   | 00001             |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    When I am on the "Course 1" course page logged in as student2
    Then I should not see "1 unread post"
    And I follow "Test forum name"
    And I should not see "Track unread posts"

  Scenario: Tracking forum posts forced with user tracking on
    Given the following config values are set as admin:
      | forum_allowforcedreadtracking | 1 |
    And the following "activity" exists:
      | activity     | forum                       |
      | course       | C1                          |
      | idnumber     | 00001                       |
      | name         | Test forum name             |
      | type         | general                     |
      | trackingtype | 2                           |
    And the following "mod_forum > discussion" exists:
      | forum   | 00001            |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    When I am on the "Course 1" course page logged in as student1
    Then I should see "1 unread post"
    And I am on the "Test forum name" "forum activity" page
    And I should not see "Don't track unread posts"
    And I follow "Test post subject"
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"

  Scenario: Tracking forum posts forced with user tracking off
    Given the following config values are set as admin:
      | forum_allowforcedreadtracking | 1 |
    And the following "activity" exists:
      | activity     | forum                       |
      | course       | C1                          |
      | idnumber     | 00001                       |
      | name         | Test forum name             |
      | type         | general                     |
      | trackingtype | 2                           |
    And the following "mod_forum > discussion" exists:
      | forum   | 00001             |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    When I am on the "Course 1" course page logged in as student2
    Then I should see "1 unread post"
    And I am on the "Test forum name" "forum activity" page
    And I should not see "Don't track unread posts"
    And I follow "Test post subject"
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"

  Scenario: Tracking forum posts forced (with force disabled) with user tracking on
    Given the following config values are set as admin:
      | forum_allowforcedreadtracking | 1 |
    And the following "activity" exists:
      | activity     | forum                     |
      | course       | C1                        |
      | idnumber     | 00001                     |
      | name         | Test forum name           |
      | type         | general                   |
      | trackingtype | 2                         |
    And the following "mod_forum > discussion" exists:
      | forum   | 00001             |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    And the following config values are set as admin:
      | forum_allowforcedreadtracking | 0 |
    When I am on the "Course 1" course page logged in as student1
    Then I should see "1 unread post"
    And I follow "Test forum name"
    And I follow "Don't track unread posts"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"
    And I follow "Test forum name"
    And I follow "Track unread posts"
    And I wait to be redirected
    And I click on "1" "link" in the "Admin User" "table_row"
    And I am on "Course 1" course homepage
    And I should not see "1 unread post"

  Scenario: Tracking forum posts forced (with force disabled) with user tracking off
    Given the following config values are set as admin:
      | forum_allowforcedreadtracking | 1 |
    And the following "activity" exists:
      | activity     | forum                  |
      | course       | C1                     |
      | idnumber     | 00001                  |
      | name         | Test forum name        |
      | type         | general                |
      | trackingtype | 2                      |
    And the following "mod_forum > discussion" exists:
      | forum   | 00001             |
      | course  | C1                |
      | user    | admin             |
      | name    | Test post subject |
      | message | Test post message |
    And the following config values are set as admin:
      | forum_allowforcedreadtracking | 0 |
    When I am on the "Course 1" course page logged in as student2
    Then I should not see "1 unread post"
    And I follow "Test forum name"
    And I should not see "Track unread posts"
