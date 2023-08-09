@core @core_group @_file_upload
Feature: Importing of groups and groupings
  In order to import groups and grouping
  As a teacher
  I need to upload a file and verify groups and groupings can be imported

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |

  @javascript
  Scenario: Import groups and groupings as teacher
    Given I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import.csv" file to "Import" filemanager
    And I press "Import groups"
    And I press "Continue"
    Then I should see "group-id-1"
    And I should see "group-id-2"
    And I should see "group-id-1-duplicate"
    And I should see "group-noid-1"
    And I should see "group-noid-2"
    # Group messaging should have been enabled for group-id-1.
    And I set the field "groups" to "group-id-1"
    And I press "Edit group settings"
    And I should see "Yes" in the "Group messaging" "select"
    And I press "Cancel"
     # Group messaging should not have been enabled for group-id-2.
    And I set the field "groups" to "group-id-2"
    And I press "Edit group settings"
    And I should see "No" in the "Group messaging" "select"
    And I press "Cancel"
    # Check groupings
    And I set the field "Participants tertiary navigation" to "Groupings"
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
    And I am on the "Course 1" "groups" page
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
    And I am on the "Course 1" "permissions" page
    And I override the system permissions of "Teacher" role with:
      | moodle/course:changeidnumber | Prevent |
    And I log out
    And I log in as "teacher1"
    And I am on the "Course 1" "groups" page
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

  @javascript
  Scenario: Import groups into multiple courses as a teacher
    Given I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import_multicourse.csv" file to "Import" filemanager
    And I press "Import groups"
    Then I should see "Group group7 added successfully"
    And I should see "Unknown course named \"C-non-existing\""
    And I should see "Group group8 added successfully"
    And I should not see "group-will-not-be-created"
    And I should see "Group group9 added successfully"
    And I should see "Group group10 added successfully"
    And I press "Continue"
    And I should see "group10"
    And I should see "group7"
    And I should see "group8"
    And I should not see "group9"
    And I should not see "group-will-not-be-created"
    And I am on the "Course 2" "groups" page
    And I should see "group9"
    And I should not see "group-will-not-be-created"
    And I should not see "group7"
    And I should not see "group8"
    And I should not see "group10"
    And I log out

  @javascript
  Scenario: Import groups with custom field
    Given the following "custom field categories" exist:
      | name                   | component  | area     | itemid |
      | Category for group1    | core_group | group    | 0      |
      | Category for grouping1 | core_group | grouping | 0      |
    And the following "custom fields" exist:
      | name        | category               | type | shortname      |
      | Test Field1 | Category for group1    | text | groupfield1    |
      | Test Field2 | Category for grouping1 | text | groupingfield1 |
    And I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    And I press "Import groups"
    When I upload "group/tests/fixtures/groups_import_with_customfield.csv" file to "Import" filemanager
    And I press "Import groups"
    Then I should see "Group Group1 added successfully"
    And I should see "Group Group2 added successfully"
    And I should see "Grouping Grouping1 added successfully"
    And I press "Continue"
    And I set the field "groups" to "Group1 (0)"
    And I press "Edit group settings"
    And the field "Test Field1" matches value "Group1-Custom"
    And I press "Cancel"
    And I set the field "groups" to "Group2 (0)"
    And I press "Edit group settings"
    And the field "Test Field1" matches value "Group2-Custom"
    And I press "Cancel"
    And I am on the "Course 1" "groupings" page
    Then I should see "Grouping1"
    And I click on "Edit" "link" in the "Grouping1" "table_row"
    And the field "Test Field2" matches value "Grouping1-Custom"
