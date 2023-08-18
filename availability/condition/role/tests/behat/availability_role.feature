@availability @availability_role @javascript
Feature: availability_role
  In order to control student access to activities
  As a teacher
  I need to set role conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
      | student2 |
      | manager1 |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Add role condition for the teacher role to a page activity and try to view it as teacher (who will see it)
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Teacher"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    When I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then I should see "P1" in the "region-main" "region"

  Scenario: Add role condition for the teacher role to a page activity and try to view it as student (who will not see it)
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Teacher"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "P1" in the "region-main" "region"

  Scenario: Add role condition for the teacher role to a page activity and try to view it as manager (who will see it although he isn't a teacher)
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Teacher"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    When I log out
    And I log in as "manager1"
    And I am on "Course 1" course homepage
    Then I should see "P1" in the "region-main" "region"

  Scenario: Add role condition for the guest role to a page activity and try to view it with a fully enrolled and a guest-enrolled student
    Given the following config values are set as admin:
      | config                   | value | plugin            |
      | setting_supportguestrole | YES   | availability_role |
    And I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Guest"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "P1" in the "region-main" "region"
    When I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "P1" in the "region-main" "region"

  Scenario: Add role condition for the non-logged-in role to a page activity and try to view it with a logged-in student and a not-logged-in user
    Given the following "roles" exist:
      | name    | shortname | description | archetype |
      | Visitor | visitor   | Visitor     | guest     |
    And the following config values are set as admin:
      | config                         | value | plugin            |
      | setting_supportnotloggedinrole | YES   | availability_role |
    And the following config values are set as admin:
      | config                         | value |
      | guestloginbutton               | 1     |
    And I log in as "admin"
    And I navigate to "Users > Permissions > User policies" in site administration
    And I set the field "Role for visitors" to "Visitor (visitor)"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Visitor"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see "P1" in the "region-main" "region"
    When I log out
    And I log in as "guest"
    And I am on "Course 1" course homepage
    Then I should see "P1" in the "region-main" "region"
