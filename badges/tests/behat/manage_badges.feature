@core @core_badges @javascript
Feature: Manage badges
  In order to manage badges in the system
  As an admin
  I need to be able to edit, copy, enable/disable access, delete and award badges

  Background:
    Given the following "core_badges > Badge" exists:
      | name           | Badge #1                     |
      | status         | 0                            |
      | version        | 1.0                          |
      | language       | en                           |
      | description    | Test badge description       |
      | image          | badges/tests/behat/badge.png |
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
    And I set the following fields to these values:
      | Name    | New Badge #1 |
      | Version | 1.1          |
    And I press "Save changes"
    And I click on "Back" "button"
    Then the following should exist in the "reportbuilder-table" table:
      | Name          | Badge status  |
      | New Badge #1  | Not available |
    And I follow "New Badge #1"
    And I should see "1.1"

  Scenario: Delete a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Delete" action in the "Badge #1" report row
    And I press "Delete and remove existing issued badges"
    Then I should see "There are no matching badges available for users to earn."

  Scenario Outline: Filter managed badges
    Given the following "core_badges > Badges" exist:
      | name     | status | version | image                        |
      | Badge #2 | 1      | 2.0     | badges/tests/behat/badge.png |
    And I log in as "admin"
    When I navigate to "Badges > Manage badges" in site administration
    And I click on "Filters" "button"
    And I set the following fields in the "<filter>" "core_reportbuilder > Filter" to these values:
      | <filter> operator | Is equal to |
      | <filter> value    | <value>     |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "Badge #1" in the "Badges" "table"
    And I should not see "Badge #2" in the "Badges" "table"
    Examples:
      | filter       | value         |
      | Name         | Badge #1      |
      | Version      | 1.0           |
      | Badge status | Not available |

  Scenario: Enable and disable access to a badge
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Badge #1" report row
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Enable access" action in the "Badge #1" report row
    And I should see "This will make your badge visible to users and allow them to start earning it."
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    And I should see "Access to badge 'Badge #1' enabled"
    Then the following should exist in the "reportbuilder-table" table:
      | Name      | Badge status  |
      | Badge #1  | Available     |
    And I press "Disable access" action in the "Badge #1" report row
    And I should see "Access to badge 'Badge #1' disabled"
    And the following should exist in the "reportbuilder-table" table:
      | Name      | Badge status  |
      | Badge #1  | Not available |

  Scenario: Award a badge
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
    When I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Badge #1" report row
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Enable access" action in the "Badge #1" report row
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    And I press "Award badge" action in the "Badge #1" report row
    And I set the field "potentialrecipients[]" to "Admin User (moodle@example.com),User One (user1@example.com)"
    And I press "Award badge"
    And I navigate to "Badges > Manage badges" in site administration
    Then the following should exist in the "Badges" table:
      | Name      | Badge status  | Recipients |
      | Badge #1  | Available     | 2          |
    And I click on "2" "link" in the "Badge #1" "table_row"
    And the following should exist in the "Recipients" table:
      | -1-        |
      | Admin User |
      | User One   |

  @accessibility
  Scenario: View list of badges with recipients
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | User      | One      |
      | user2    | User      | Two      |
    And the following "core_badges > Badges" exist:
      | name     | status | image                        |
      | Badge #2 | 1      | badges/tests/behat/badge.png |
      | Badge #3 | 1      | badges/tests/behat/badge.png |
    And the following "core_badges > Issued badges" exist:
      | badge    | user  |
      | Badge #1 | user1 |
      | Badge #1 | user2 |
      | Badge #2 | user1 |
    When I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    Then the following should exist in the "Badges" table:
      | Name     | Badge status  | Recipients |
      | Badge #1 | Not available | 2          |
      | Badge #2 | Available     | 1          |
      | Badge #3 | Available     | 0          |
    And the "Badges" "table" should meet accessibility standards with "best-practice" extra tests

  @_file_upload
  Scenario: Badge names are not unique anymore
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "core_badges > Badge" exists:
      | name           | Badge #2                     |
      | status         | 0                            |
      | course         | C1                           |
      | type           | 1                            |
      | version        | 1.0                          |
      | language       | en                           |
      | description    | Test badge description       |
      | image          | badges/tests/behat/badge.png |
      | imagecaption   | Test caption image           |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | name           | Badge #1                     |
      | description    | Test badge description       |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    When I press "Create badge"
    Then I should see "Criteria for this badge have not been set up yet."
    And I select "Edit details" from the "jump" singleselect
    # Set name for a site badge with existing badge name in a course is also allowed.
    And I set the field "name" to "Badge #2"
    And I press "Save changes"
    And I should see "Changes saved"
