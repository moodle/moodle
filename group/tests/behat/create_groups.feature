@core @core_group
Feature: Organize students into groups
  In order to organize course activities in groups
  As a teacher
  I need to group students

  @javascript
  Scenario: Assign students to groups
    Given the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student0 | Student | 0 | student0@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student0 | C1 | student |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I press "Create group"
    And I fill the moodle form with:
      | Group name | Group 1 |
    And I press "Save changes"
    And I press "Create group"
    And I fill the moodle form with:
      | Group name | Group 2 |
    And I press "Save changes"
    When I add "student0" user to "Group 1" group
    And I add "student1" user to "Group 1" group
    And I add "student2" user to "Group 2" group
    And I add "student3" user to "Group 2" group
    Then I select "Group 1 (2)" from "groups"
    And I wait "5" seconds
    And the "members" select box should contain "Student 0"
    And the "members" select box should contain "Student 1"
    And the "members" select box should not contain "Student 2"
    And I select "Group 2 (2)" from "groups"
    And I wait "5" seconds
    And the "members" select box should contain "Student 2"
    And the "members" select box should contain "Student 3"
    And the "members" select box should not contain "Student 0"
    And I follow "Participants"
    And I select "Group 1" from "Separate groups"
    And I should see "Student 0"
    And I should see "Student 1"
    And I should not see "Student 2"
    And I select "Group 2" from "Separate groups"
    And I should see "Student 2"
    And I should see "Student 3"
    And I should not see "Student 0"

  @javascript
  Scenario: Create groups and groupings without the 'moodle/course:changeidnumber' capability
    Given the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | moodle/course:changeidnumber | Prevent |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    When I press "Create group"
    Then the "idnumber" "field" should be readonly
    And I fill the moodle form with:
      | Group name | The greatest group that never existed |
    And I press "Save changes"
    And I should see "The greatest group that never existed"
    And I follow "Groupings"
    And I press "Create grouping"
    And the "idnumber" "field" should be readonly
    And I fill the moodle form with:
      | Grouping name | Not the greatest grouping, but it's ok! |
    And I press "Save changes"
    And I should see "Not the greatest grouping, but it's ok!"
