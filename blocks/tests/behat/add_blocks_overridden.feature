@block @core_block @javascript @addablocklink
Feature: Add a block when main feature is enabled
  In order to add a block to my course
  As a teacher
  Some blocks should be only added to courses if the main feature they are based on is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I am on the "C1" "course" page logged in as "admin"

  Scenario Outline: The block can be added when main feature is enabled
    Given the following config values are set as admin:
      | <settingname1> | 1 | <settingplugin1> |
      | <settingname2> | 1 |                  |
    And I turn editing mode on
    When I click on "Add a block" "link"
    Then I should see "<blockname>"

    Examples:
      | blockname                  | settingname1                | settingname2              | settingplugin1            |
      | Accessibility review       | enableaccessibilitytools    |                           |                           |
      | Blog menu                  | enableblogs                 |                           |                           |
      | Recent blog entries        | enableblogs                 |                           |                           |
      | Blog tags                  | enableblogs                 | usetags                   |                           |
      | Comments                   | usecomments                 |                           |                           |
      | Course completion status   | enablecompletion            |                           |                           |
      | Global search              | enableglobalsearch          |                           |                           |
      | Latest badges              | enablebadges                |                           |                           |
      | Tags                       | usetags                     |                           |                           |
      | Learning plans             | enabled                     |                           | core_competency           |

  Scenario Outline: The block cannot be added when main feature is disabled
    Given the following config values are set as admin:
      | <settingname1> | 0 | <settingplugin1> |
      | <settingname2> | 0 |                  |
    And I turn editing mode on
    When I click on "Add a block" "link"
    Then I should not see "<blockname>"

    Examples:
      | blockname                  | settingname1                | settingname2              | settingplugin1            |
      | Accessibility review       | enableaccessibilitytools    |                           |                           |
      | Blog menu                  | enableblogs                 |                           |                           |
      | Recent blog entries        | enableblogs                 |                           |                           |
      | Blog tags                  | enableblogs                 | usetags                   |                           |
      | Comments                   | usecomments                 |                           |                           |
      | Course completion status   | enablecompletion            |                           |                           |
      | Global search              | enableglobalsearch          |                           |                           |
      | Latest badges              | enablebadges                |                           |                           |
      | Tags                       | usetags                     |                           |                           |
      | Learning plans             | enabled                     |                           | core_competency           |
