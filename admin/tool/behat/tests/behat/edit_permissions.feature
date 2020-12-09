@tool @tool_behat
Feature: Edit capabilities
  In order to extend and restrict moodle features
  As an admin or a teacher
  I need to allow/deny the existing capabilities at different levels

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  |
      | teacher1 | Teacher   | 1         |
      | tutor    | Teaching  | Assistant |
      | student  | Student   | One       |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | tutor    | C1     | teacher        |
      | student  | C1     | student        |

  Scenario: Default system capabilities modification
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability | permission |
      | block/mnet_hosts:myaddinstance | Allow |
      | moodle/site:messageanyuser | Inherit |
      | moodle/grade:managesharedforms | Prevent |
      | moodle/course:request | Prohibit |
    When I follow "Edit Teacher role"
    Then "block/mnet_hosts:myaddinstance" capability has "Allow" permission
    And "moodle/site:messageanyuser" capability has "Not set" permission
    And "moodle/grade:managesharedforms" capability has "Prevent" permission
    And "moodle/course:request" capability has "Prohibit" permission

  Scenario: Course capabilities overrides
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
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
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | I'm the name |
      | Description | I'm the introduction |
    And I follow "I'm the name"
    And I navigate to "Permissions" in current page administration
    And I override the system permissions of "Student" role with:
      | mod/forum:deleteanypost | Prohibit |
      | mod/forum:editanypost | Prevent |
      | mod/forum:addquestion | Allow |
    When I set the field "Advanced role override" to "Student (3)"
    And I click on "Go" "button" in the "region-main" "region"
    Then "mod/forum:deleteanypost" capability has "Prohibit" permission
    And "mod/forum:editanypost" capability has "Prevent" permission
    And "mod/forum:addquestion" capability has "Allow" permission

  @javascript
  Scenario: Edit permissions escapes role names correctly
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Your word for 'Teacher'             | Teacher >= editing  |
      | Your word for 'Non-editing teacher' | Teacher < "editing" |
      | Your word for 'Student'             | Studier & 'learner' |
    And I press "Save and display"
    And I navigate to course participants
    Then I should see "Teacher >= editing" in the "Teacher 1" "table_row"
    And I should see "Teacher < \"editing\"" in the "Teaching Assistant" "table_row"
    And I should see "Studier & 'learner'" in the "Student One" "table_row"
    And I navigate to "Users > Permissions" in current page administration
    And I should see "Teacher >= editing" in the "mod/forum:replypost" "table_row"
    And I should see "Teacher < \"editing\"" in the "mod/forum:replypost" "table_row"
    And I should see "Studier & 'learner'" in the "mod/forum:replypost" "table_row"
    And I follow "Prohibit"
    And "Teacher >= editing" "button" in the "Prohibit role" "dialogue" should be visible
    And "Teacher < \"editing\"" "button" in the "Prohibit role" "dialogue" should be visible
    And "Studier & 'learner'" "button" in the "Prohibit role" "dialogue" should be visible
