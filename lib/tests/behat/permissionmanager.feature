@core @javascript
Feature: Override permissions on a context
  In order to extend and restrict moodle features
  As an admin or a teacher
  I need to allow/deny the existing capabilities at different levels

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email          |
      | teacher1  | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | C1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |

  Scenario: Default system capabilities modification
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    When I click on "Allow" "icon" in the "mod/forum:addnews" "table_row"
    And I press "Student"
    Then "Add announcementsmod/forum:addnews" row "Roles with permission" column of "permissions" table should contain "Student"
    When I reload the page
    And I click on "Delete Student role" "link" in the "mod/forum:addnews" "table_row"
    And I click on "Remove" "button" in the "Confirm role change" "dialogue"
    Then "Add announcementsmod/forum:addnews" row "Roles with permission" column of "permissions" table should not contain "Student"
    When I reload the page
    And I click on "Prohibit" "icon" in the "mod/forum:addnews" "table_row"
    And I press "Student"
    Then "Add announcementsmod/forum:addnews" row "Prohibited" column of "permissions" table should contain "Student"

  Scenario: Module capabilities overrides
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name  | Forum 1 |
    And I follow "Forum 1"
    And I navigate to "Permissions" in current page administration
    When I click on "Allow" "icon" in the "mod/forum:addnews" "table_row"
    And I press "Student"
    Then "Add announcementsmod/forum:addnews" row "Roles with permission" column of "permissions" table should contain "Student"
    When I reload the page
    And I click on "Delete Student role" "link" in the "mod/forum:addnews" "table_row"
    And I click on "Remove" "button" in the "Confirm role change" "dialogue"
    Then "Add announcementsmod/forum:addnews" row "Roles with permission" column of "permissions" table should not contain "Student"
    When I reload the page
    And I click on "Prohibit" "icon" in the "mod/forum:addnews" "table_row"
    And I press "Student"
    Then "Add announcementsmod/forum:addnews" row "Prohibited" column of "permissions" table should contain "Student"
