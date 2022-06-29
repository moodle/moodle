@block @core_block @javascript
Feature: Add a block when main feature is disabled
  In order to add a block to my course
  As a teacher
  Some blocks should be only added to courses if the main feature they are based on is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I am on the "C1" "course" page logged in as "admin"

  Scenario Outline: The block is displayed even when main feature is disabled
    Given the following config values are set as admin:
      | <settingname1> | 1 | <settingplugin1> |
    And I turn editing mode on
    And I add the "<blockname>" block
    When the following config values are set as admin:
      | <settingname1> | 0 | <settingplugin1> |
    Then I should see "<blockname>"

    Examples:
      | blockname                  | settingname1                    | settingplugin1            |
      | Accessibility review       | enableaccessibilitytools        |                           |
      | Blog menu                  | enableblogs                     |                           |
      | Recent blog entries        | enableblogs                     |                           |
      | Comments                   | usecomments                     |                           |
      | Course completion status   | enablecompletion                |                           |
      | Global search              | enableglobalsearch              |                           |
      | Latest badges              | enablebadges                    |                           |
      | Tags                       | usetags                         |                           |
      | Learning plans             | enabled                         | core_competency           |

  Scenario Outline: The block is displayed even when main feature is disabled (2 settings)
    Given the following config values are set as admin:
      | <settingname1> | 1 |
      | <settingname2> | 1 |
    And I turn editing mode on
    And I add the "<blockname>" block
    When the following config values are set as admin:
      | <settingname1> | 0 |
      | <settingname2> | 0 |
    Then I should see "<blockname>"

    Examples:
      | blockname                  | settingname1                    | settingname2                    |
      | Blog tags                  | enableblogs                     | usetags                         |

  Scenario Outline: The block can be removed even when main feature is disabled
    Given the following config values are set as admin:
      | <settingname1> | 1 | <settingplugin1> |
    And I turn editing mode on
    And I add the "<blockname>" block
    And I open the "<blockname>" blocks action menu
    And I click on "Delete <blockname> block" "link" in the "<blockname>" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And I should see "<blockname>"
    When the following config values are set as admin:
      | <settingname1> | 0 | <settingplugin1> |
    And I open the "<blockname>" blocks action menu
    And I click on "Delete <blockname> block" "link" in the "<blockname>" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    Then I should not see "<blockname>"

    Examples:
      | blockname                  | settingname1                    | settingplugin1            |
      | Accessibility review       | enableaccessibilitytools        |                           |
      | Blog menu                  | enableblogs                     |                           |
      | Recent blog entries        | enableblogs                     |                           |
      | Comments                   | usecomments                     |                           |
      | Course completion status   | enablecompletion                |                           |
      | Global search              | enableglobalsearch              |                           |
      | Latest badges              | enablebadges                    |                           |
      | Tags                       | usetags                         |                           |
      | Learning plans             | enabled                         | core_competency           |

  Scenario Outline: The block can be removed even when main feature is disabled (2 settings)
    Given the following config values are set as admin:
      | <settingname1> | 1 |
      | <settingname2> | 1 |
    And I turn editing mode on
    And I add the "<blockname>" block
    And I open the "<blockname>" blocks action menu
    And I click on "Delete <blockname> block" "link" in the "<blockname>" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And I should see "<blockname>"
    When the following config values are set as admin:
      | <settingname1> | 0 |
      | <settingname2> | 0 |
    And I open the "<blockname>" blocks action menu
    And I click on "Delete <blockname> block" "link" in the "<blockname>" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    Then I should not see "<blockname>"

    Examples:
      | blockname                  | settingname1                    | settingname2                    |
      | Blog tags                  | enableblogs                     | usetags                         |
