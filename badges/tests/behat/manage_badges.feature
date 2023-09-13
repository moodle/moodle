@core @core_badges @javascript
Feature: Manage badges
  In order to manage badges in the system
  As an admin
  I need to be able to edit, copy, enable/disable access, delete and award badges

  Background:
    Given the following "core_badges > Badge" exists:
      | name           | Badge #1                     |
      | status         | 0                            |
      | version        | 1                            |
      | language       | en                           |
      | description    | Test badge description       |
      | image          | badges/tests/behat/badge.png |
      | imageauthorurl | http://author.example.com    |
      | imagecaption   | Test caption image           |

  Scenario: Copy a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Copy" action in the "Badge #1" report row
    And I should see "Copy of Badge #1"
    And I press "Save changes"
    And I click on "Back" "button"
    Then the following should exist in the "reportbuilder-table" table:
      | Name             | Badge status  |
      | Badge #1         | Not available |
      | Copy of Badge #1 | Not available |

  Scenario: Edit a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Badge #1" report row
    And I set the field "Name" to "New Badge #1"
    And I press "Save changes"
    And I click on "Back" "button"
    Then the following should exist in the "reportbuilder-table" table:
      | Name          | Badge status  |
      | New Badge #1  | Not available |

  Scenario: Delete a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Delete" action in the "Badge #1" report row
    And I press "Delete and remove existing issued badges"
    Then I should see "There are currently no badges available for users to earn"

  Scenario: Enable and disable access to a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Badge #1" report row
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I navigate to "Badges > Manage badges" in site administration
    And I open the action menu in "Badge #1" "table_row"
    And I choose "Enable access" in the open action menu
    And I should see "Changes in badge access"
    And I press "Continue"
    And I should see "Access to the badges was successfully enabled"
    Then the following should exist in the "reportbuilder-table" table:
      | Name      | Badge status  |
      | Badge #1  | Available     |
    And I open the action menu in "Badge #1" "table_row"
    And I choose "Disable access" in the open action menu
    And I should see "Access to the badges was successfully disabled"
    And the following should exist in the "reportbuilder-table" table:
      | Name      | Badge status  |
      | Badge #1  | Not available |

  Scenario: Award a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Badge #1" report row
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I navigate to "Badges > Manage badges" in site administration
    And I open the action menu in "Badge #1" "table_row"
    And I choose "Enable access" in the open action menu
    And I press "Continue"
    And I open the action menu in "Badge #1" "table_row"
    And I choose "Award badge" in the open action menu
    And I set the field "potentialrecipients[]" to "Admin User (moodle@example.com)"
    And I press "Award badge"
    And I navigate to "Badges > Manage badges" in site administration
    And the following should exist in the "reportbuilder-table" table:
      | Name      | Badge status  | Recipients |
      | Badge #1  | Available     | 1          |
