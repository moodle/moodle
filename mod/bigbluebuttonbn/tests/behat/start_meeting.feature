@mod @mod_bigbluebuttonbn
Feature: Test the ability to start a meeting
  In order to ensure that start meeting capabilities are respected
  As a teacher
  I need to control who can start a meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin

  Scenario Outline: Users should be able to join a session depending on the Wait for moderator to join setting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role   |
      | traverst | C1     | <role> |
    And the following "activity" exists:
      | course     | C1                  |
      | activity   | bigbluebuttonbn     |
      | name       | Room recordings     |
      | idnumber   | Room recordings     |
      | moderators | role:editingteacher |
      | wait       | <wait>              |
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "Join session" "link" <existence> exist

    Examples:
      | wait | role           | existence  |
      | 0    | student        | should     |
      | 0    | teacher        | should     |
      | 0    | editingteacher | should     |
      | 1    | student        | should not |
      | 1    | teacher        | should not |
      | 1    | editingteacher | should     |

  Scenario Outline: Users should be able to join a session if a moderator has already joined
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role   |
      | traverst | C1     | <role> |
    And the following "activity" exists:
      | course     | C1                  |
      | activity   | bigbluebuttonbn     |
      | name       | Room recordings     |
      | idnumber   | Room recordings     |
      | moderators | role:editingteacher |
      | wait       | <wait>              |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity         | Room recordings |
      | moderatorCount   | 1               |
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "Join session" "link" should exist

    Examples:
      | wait | role           |
      | 0    | student        |
      | 0    | editingteacher |
      | 1    | student        |
      | 1    | editingteacher |

  Scenario Outline: An administrator should always be allowed to join a meeting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "activity" exists:
      | course     | C1                  |
      | activity   | bigbluebuttonbn     |
      | name       | Room recordings     |
      | idnumber   | Room recordings     |
      | moderators | role:editingteacher |
      | wait       | <wait>              |
    When I am on the "Room recordings" Activity page logged in as admin
    Then "Join session" "link" should exist

    Examples:
      | wait |
      | 0    |
      | 1    |

  Scenario Outline: A student moderator should be able to start a meeting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role     |
      | traverst | C1     | student  |
    And the following "activity" exists:
      | course     | C1              |
      | activity   | bigbluebuttonbn |
      | name       | Room recordings |
      | idnumber   | Room recordings |
      | moderators | <moderators>    |
      | wait       | <wait>          |
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "Join session" "link" should exist

    Examples:
      | wait | moderators    |
      | 0    | user:traverst |
      | 1    | user:traverst |
      | 0    | role:student  |
      | 1    | role:student  |
