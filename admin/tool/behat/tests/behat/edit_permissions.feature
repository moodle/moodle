@tool @tool_behat
Feature: Edit capabilities
  In order to extend and restrict moodle features
  As an admin or a teacher
  I need to allow/deny the existing capabilities at different levels

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Default system capabilities modification
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability | permission |
      | block/mnet_hosts:myaddinstance | Allow |
      | moodle/community:add | Inherit |
      | moodle/grade:managesharedforms | Prevent |
      | moodle/course:request | Prohibit |
    When I follow "Edit Teacher role"
    Then "block/mnet_hosts:myaddinstance" capability has "Allow" permission
    And "moodle/community:add" capability has "Not set" permission
    And "moodle/grade:managesharedforms" capability has "Prevent" permission
    And "moodle/course:request" capability has "Prohibit" permission

  Scenario: Course capabilities overrides
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Permissions"
    And I override the system permissions of "Student" role with:
      | mod/forum:deleteanypost | Prohibit |
      | mod/forum:editanypost | Prevent |
      | mod/forum:addquestion | Allow |
    When I set the field "Advanced role override" to "Student (3)"
    And I press "Go"
    Then "mod/forum:deleteanypost" capability has "Prohibit" permission
    And "mod/forum:editanypost" capability has "Prevent" permission
    And "mod/forum:addquestion" capability has "Allow" permission

  Scenario: Module capabilities overrides
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | I'm the name |
      | Description | I'm the introduction |
    And I follow "I'm the name"
    And I follow "Permissions"
    And I override the system permissions of "Student" role with:
      | mod/forum:deleteanypost | Prohibit |
      | mod/forum:editanypost | Prevent |
      | mod/forum:addquestion | Allow |
    When I set the field "Advanced role override" to "Student (3)"
    And I press "Go"
    Then "mod/forum:deleteanypost" capability has "Prohibit" permission
    And "mod/forum:editanypost" capability has "Prevent" permission
    And "mod/forum:addquestion" capability has "Allow" permission
