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
    And I log in as "admin"

  Scenario: Tracking forum posts off
    Given the following "activity" exists:
      | activity     | forum                  |
      | course       | C1                     |
      | idnumber     | 00001                  |
      | name         | Test forum name        |
      | intro        | Test forum description |
      | section      | 1                      |
      | trackingtype | 0                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "1 unread post"
    And I follow "Test forum name"
    And I should not see "Track unread posts"

  Scenario: Tracking forum posts optional with user tracking on
    Given the following "activity" exists:
      | activity     | forum                         |
      | course       | C1                            |
      | idnumber     | 00001                         |
      | name         | Test forum name               |
      | intro        | Test forum description        |
      | section      | 1                             |
      | type         | generalforum                  |
      | trackingtype | 1                             |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
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
      | intro        | Test forum description      |
      | section      | 1                           |
      | type         | generalforum                |
      | trackingtype | 1                           |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
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
      | intro        | Test forum description      |
      | section      | 1                           |
      | type         | generalforum                |
      | trackingtype | 2                           |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "1 unread post"
    And I follow "1 unread post"
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
      | intro        | Test forum description      |
      | section      | 1                           |
      | type         | generalforum                |
      | trackingtype | 2                           |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "1 unread post"
    And I follow "1 unread post"
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
      | description  | Test forum description    |
      | section      | 1                         |
      | type         | generalforum              |
      | trackingtype | 2                         |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And the following config values are set as admin:
      | forum_allowforcedreadtracking | 0 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
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
      | description  | Test forum description |
      | section      | 1                      |
      | type         | generalforum           |
      | trackingtype | 2                      |
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And the following config values are set as admin:
      | forum_allowforcedreadtracking | 0 |
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should not see "1 unread post"
    And I follow "Test forum name"
    And I should not see "Track unread posts"
