@core @core_group @_file_upload
Feature: Importing of groups and groupings
  In order to import groups and grouping
  As a teacher
  I need to upload a file and verify groups and groupings can be imported

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Import groups and groupings as teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import.csv" file to "Import" filemanager
    And I press "Import groups"
    And I press "Continue"
    Then I should see "group-id-1"
    And I should see "group-id-2"
    And I should see "group-id-1-duplicate"
    And I should see "group-noid-1"
    And I should see "group-noid-2"
    And I follow "Groupings"
    And I should see "Grouping-1"
    And I should see "Grouping-2"
    And I should see "Grouping-3"
    And I should see "group-id-1" in the "Grouping-1" "table_row"
    And I should see "group-id-2" in the "Grouping-2" "table_row"
    And I should see "group-noid-2" in the "Grouping-2" "table_row"
    And I should see "group-id-1-duplicate" in the "Grouping-3" "table_row"
    And I should see "group-noid-1" in the "Grouping-3" "table_row"

  @javascript
  Scenario: Import groups with idnumber when the user has proper permissions for the idnumber field
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import.csv" file to "Import" filemanager
    And I press "Import groups"
    Then I should see "Group group-id-1 added successfully"
    And I should see "Group group-id-2 added successfully"
    And I should see "group-id-1-duplicate: Group \"group-id-1\" with an idnumber of \"group-id-1\" already exists for this course"
    And I should see "Group group-id-1-duplicate added successfully"
    And I should see "Group group-noid-1 added successfully"
    And I should see "Group group-noid-2 added successfully"
    And I press "Continue"
    And I set the field "groups" to "group-id-1"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value "group-id-1"
    And I press "Cancel"
    And I set the field "groups" to "group-id-2"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value "group-id-2"
    And I press "Cancel"
    And I set the field "groups" to "group-id-1-duplicate"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-noid-1"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-noid-2"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"

  @javascript
  Scenario: Import groups with idnumber when the user does not have proper permissions for the idnumber field
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Teacher" role with:
      | moodle/course:changeidnumber | Prevent |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import.csv" file to "Import" filemanager
    And I press "Import groups"
    And I press "Continue"
    Then I set the field "groups" to "group-id-1"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-id-2"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-id-1-duplicate"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-noid-1"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
    And I set the field "groups" to "group-noid-2"
    And I press "Edit group settings"
    And the field "id_idnumber" matches value ""
    And I press "Cancel"
