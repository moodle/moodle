@mod @mod_bigbluebuttonbn @javascript
Feature: Set role as Bigbluebuttonbn moderator
  In order to set a room moderator
  As admin
  I need to see the list of roles

  Background:
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following "course" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activity" exists:
      | course    | C1              |
      | activity  | bigbluebuttonbn |
      | name      | RoomRecordings  |

  Scenario: Set the manager role as room moderator
    Given I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I set the field "bigbluebuttonbn_participant_selection_type" to "Role"
    And I set the field "bigbluebuttonbn_participant_selection" to "Manager"
    And I click on "bigbluebuttonbn_participant_selection_add" "button"
    And I set the field "select-for-role-1" to "Moderator"
    And I press "Save and display"
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page
    Then "[name=select-for-role-1] option[value=moderator][selected]" "css_element" should exist
