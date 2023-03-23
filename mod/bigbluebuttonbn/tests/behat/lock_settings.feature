@mod @mod_bigbluebuttonbn
Feature: Test that the meeting has the right lock setting.
  In order to ensure that a meeting creator can lock settings in a meeting
  As a teacher
  I set up locked feature for a given meeting and this should be reflected in the meeting.

  Background:
    Given a BigBlueButton mock server is configured
    And I accept dpa and enable bigbluebuttonbn plugin

  @javascript
  Scenario Outline: Teacher should be able to set the right lock feature in a given meeting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | traverst | C1     | editingteacher |
    And the following "activity" exists:
      | course            | C1              |
      | activity          | bigbluebuttonbn |
      | name              | Room recordings |
      | idnumber          | Room recordings |
      | <locksettingname> | <value>         |
    When I am on the "Room recordings" Activity page logged in as traverst
    And I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    Then I should see "<bbbsettingvalue>" in the "lockSettings" "mod_bigbluebuttonbn > Meeting field"

    Examples:
      | locksettingname    | value | bbbsettingvalue              |
      | disablecam         | 1     | disableCam : enabled         |
      | disablemic         | 1     | disableMic : enabled         |
      | disableprivatechat | 1     | disablePrivateChat : enabled |
      | disablepublicchat  | 1     | disablePublicChat : enabled  |
      | disablenote        | 1     | disableNote : enabled        |
      | hideuserlist       | 1     | hideUserList : enabled       |

  @javascript
  Scenario: If any lock Setting is enabled, then the LockOnJoin should be enabled
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | traverst | C1     | editingteacher |
    And the following "activity" exists:
      | course     | C1              |
      | activity   | bigbluebuttonbn |
      | name       | Room recordings |
      | idnumber   | Room recordings |
      | disablecam | 1               |
    When I am on the "Room recordings" Activity page logged in as traverst
    And I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    Then I should see "disableCam : enabled" in the "lockSettings" "mod_bigbluebuttonbn > Meeting field"
    Then I should see "lockOnJoin : enabled" in the "lockSettings" "mod_bigbluebuttonbn > Meeting field"
