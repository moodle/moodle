@core @core_group
Feature: Automatic updating of groups and groupings
  In order to check the expected results occur when updating groups and groupings in different scenarios
  As a teacher
  I need to create groups and groupings under different scenarios and check that the expected result occurs when attempting to update them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group (without ID) |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group (with ID) |
      | Group ID number | An ID |
    And I press "Save changes"
    And I follow "Groupings"
    And I press "Create grouping"
    And I set the following fields to these values:
      | Grouping name | Grouping (without ID) |
    And I press "Save changes"
    And I press "Create grouping"
    And I set the following fields to these values:
      | Grouping name | Grouping (with ID) |
      | Grouping ID number | An ID |
    And I press "Save changes"
    And I follow "Groups"

  @javascript
  Scenario: Update groups and groupings with ID numbers
    Given I set the field "groups" to "Group (with ID)"
    And I press "Edit group settings"
    And the field "idnumber" matches value "An ID"
    And I set the following fields to these values:
      | Group name | Group (with ID) (updated) |
      | Group ID number | An ID (updated) |
    When I press "Save changes"
    Then I should see "Group (with ID) (updated)"
    And I set the field "groups" to "Group (with ID) (updated)"
    And I press "Edit group settings"
    And the field "idnumber" matches value "An ID (updated)"
    And I press "Save changes"
    And I follow "Groupings"
    And I click on "Edit" "link" in the "Grouping (with ID)" "table_row"
    And the field "idnumber" matches value "An ID"
    And I set the following fields to these values:
      | Grouping name | Grouping (with ID) (updated) |
      | Grouping ID number | An ID (updated) |
    And I press "Save changes"
    And I should see "Grouping (with ID) (updated)"
    And I click on "Edit" "link" in the "Grouping (with ID) (updated)" "table_row"
    And the field "idnumber" matches value "An ID (updated)"

  @javascript @skip_chrome_zerosize
  Scenario: Update groups and groupings with ID numbers without the 'moodle/course:changeidnumber' capability
    Given I log out
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | moodle/course:changeidnumber | Prevent |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I set the field "groups" to "Group (with ID)"
    When I press "Edit group settings"
    Then the "idnumber" "field" should be readonly
    And the field "idnumber" matches value "An ID"
    And I set the following fields to these values:
      | Group name | Group (with ID) (updated) |
    And I press "Save changes"
    And I should see "Group (with ID) (updated)"
    And I set the field "groups" to "Group (with ID) (updated)"
    And I press "Edit group settings"
    And the "idnumber" "field" should be readonly
    And the field "idnumber" matches value "An ID"
    And I press "Save changes"
    And I follow "Groupings"
    And I click on "Edit" "link" in the "Grouping (with ID)" "table_row"
    And the "idnumber" "field" should be readonly
    And the field "idnumber" matches value "An ID"
    And I set the following fields to these values:
      | Grouping name | Grouping (with ID) (updated) |
    And I press "Save changes"
    And I should see "Grouping (with ID) (updated)"
    And I click on "Edit" "link" in the "Grouping (with ID) (updated)" "table_row"
    And the "idnumber" "field" should be readonly
    And the field "idnumber" matches value "An ID"

  @javascript
  Scenario: Update groups with enrolment key
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 2 | C2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C2 | editingteacher |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I set the field "groups" to "Group (with ID)"
    And I press "Edit group settings"
    And I set the following fields to these values:
      | Enrolment key | badpasswd |
    When I press "Save changes"
    Then I should see "Passwords must have at least 1 digit(s)"
    And I set the following fields to these values:
      | Enrolment key | Abcdef-1 |
    And I press "Save changes"
    And I set the field "groups" to "Group (with ID)"
    And I press "Edit group settings"
    And I press "Save changes"
    And I should not see "This enrolment key is already used for another group."
    And I set the field "groups" to "Group (without ID)"
    And I press "Edit group settings"
    And I set the following fields to these values:
      | Enrolment key | Abcdef-1 |
    And I press "Save changes"
    And I should see "This enrolment key is already used for another group."
    And I set the following fields to these values:
      | Enrolment key | Abcdef-2 |
    And I press "Save changes"
    And I should not see "This enrolment key is already used for another group."
    And I am on "Course 2" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group A |
    And I press "Save changes"
    And I should not see "This enrolment key is already used for another group."
    And I set the field "groups" to "Group A"
    And I press "Edit group settings"
    And I set the following fields to these values:
      | Enrolment key | Abcdef-1 |
    And I press "Save changes"
    And I should not see "This enrolment key is already used for another group."
