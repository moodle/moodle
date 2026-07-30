@core @core_notes @javascript
Feature: Admin notes management link in site administration
  In order to manage user notes site-wide after the Classic theme is retired
  As an administrator or manager
  I need a "Manage user notes" link under Site administration > Users > Accounts

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | manager1  | Manager   | 1        | manager1@example.com  |
      | student1  | Student   | 1        | student1@example.com  |
    And the following "system role assigns" exist:
      | user     | role    |
      | manager1 | manager |

  Scenario: Admin can see and use the Manage user notes link
    Given I log in as "admin"
    When I navigate to "Users > Accounts > Manage user notes" in site administration
    Then the url should match "notes/index\.php\?filtertype=course&filterselect=0"
    And I should see "Site notes"

  Scenario: Manager can see and use the Manage user notes link
    Given I log in as "manager1"
    When I navigate to "Users > Accounts > Manage user notes" in site administration
    Then the url should match "notes/index\.php\?filtertype=course&filterselect=0"
    And I should see "Site notes"

  Scenario: Manager with moodle/notes:view prohibited cannot see the Manage user notes link
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | restrict1  | Restrict  | 1        | restrict1@example.com  |
    And the following "system role assigns" exist:
      | user      | role    |
      | restrict1 | manager |
    And the following "role capabilities" exist:
      | role    | moodle/notes:view |
      | manager | prohibit          |
    When I log in as "restrict1"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then "Manage user notes" "link" should not exist in current page administration

  Scenario: Notes feature disabled hides the Manage user notes link
    Given the following config values are set as admin:
      | enablenotes | 0 |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then "Manage user notes" "link" should not exist in current page administration
