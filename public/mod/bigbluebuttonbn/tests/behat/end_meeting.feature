@mod @mod_bigbluebuttonbn
Feature: Test the ability to end a meeting
  In order to ensure that end meeting capabilities are respected
  As a teacher
  I need to control who can end a meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin

  Scenario Outline: Only a BigBlueButton moderator can end a session
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
      | course     | C1              |
      | activity   | bigbluebuttonbn |
      | name       | Room recordings |
      | idnumber   | Room recordings |
      | moderators | <moderators>    |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity         | Room recordings |
    When I am on the "Room recordings" Activity page logged in as traverst
    Then "End session" "link" <existence> exist

    Examples:
      # Note: If the teacher is not listed as a moderator in the activity roles, then will not have permission to end the
      # session.
      | moderators    | role           | existence  |
      |               | editingteacher | should not |
      |               | teacher        | should not |
      |               | student        | should not |
      | role:teacher  | student        | should not |
      | role:teacher  | teacher        | should     |
      | role:student  | student        | should     |
      | user:traverst | student        | should     |

  Scenario: An administrator can always end a meeting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "activity" exists:
      | course     | C1              |
      | activity   | bigbluebuttonbn |
      | name       | Room recordings |
      | idnumber   | Room recordings |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity         | Room recordings |
    When I am on the "Room recordings" Activity page logged in as admin
    Then "End session" "link" should exist
