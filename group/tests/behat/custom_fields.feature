@core @core_group
Feature: Custom profile fields in groups
  In order to organize participants into groups
  As a teacher
  I need to be able to view and search on custom profile fields

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name    | param2 |
      | text     | species   | Species | 255    |
    And the following "users" exist:
      | username | firstname | lastname | profile_field_species | email              |
      | user1    | Robin     | Hood     | fox                   | email1@example.org |
      | user2    | Little    | John     | bear                  | email2@example.org |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | manager |
      | user2 | C1     | manager |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Canines | C1     | G1       |
    And the following "group members" exist:
      | user  | group |
      | user1 | G1    |
    Given the following config values are set as admin:
      | showuseridentity | username,profile_field_species |

  @javascript
  Scenario: Check the custom profile fields show up and can be searched on
    When I am logged in as "admin"
    And I am on the "Course 1" "groups" page

    # Check the Overview page.
    And I select "Overview" from the "jump" singleselect
    And "Robin Hood (user1, fox)" "text" should exist in the "Canines" "table_row"
    And "Little John (user2, bear)" "text" should exist in the "No group" "table_row"

    # Check the groups page.
    And I select "Groups" from the "jump" singleselect
    And I set the field "groups" to "Canines"
    And I should see "Robin Hood (user1, fox)"
    And I should not see "Little John (user2, bear)"

    # Check the members page.
    And I press "Add/remove users"
    And I should see "Robin Hood (user1, fox)"
    And I should see "Little John (user2, bear)"

    And I set the field "addselect" to "Little John (user2, bear)"
    And I press "Add"
    And I should see "Robin Hood (user1, fox)"
    And I should see "Little John (user2, bear)"

    And I set the field "Search" in the "#existingcell" "css_element" to "fox"
    And I wait "1" seconds
    And I should see "Robin Hood (user1, fox)"
    And I should not see "Little John (user2, bear)"

    And I set the field "Search" in the "#existingcell" "css_element" to ""
    And I wait "1" seconds
    And I set the field "removeselect" to "Little John (user2, bear)"
    And I press "Remove"
    And I set the field "removeselect" to "Robin Hood (user1, fox)"
    And I press "Remove"
    And I should see "Robin Hood (user1, fox)"
    And I should see "Little John (user2, bear)"

    And I set the field "Search" in the "#potentialcell" "css_element" to "bear"
    And I wait "1" seconds
    And I should see "Little John (user2, bear)"
    And I should not see "Robin Hood (user1, fox)"
