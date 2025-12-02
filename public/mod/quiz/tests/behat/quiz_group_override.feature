@mod @mod_quiz
Feature: Quiz group override
  In order to grant a group special access to a quiz
  As a teacher
  I need to create an override for that group.

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname  | email                |
      | teacher1 | Terry 1    | Teacher 1 | teacher1@example.com |
      | student1 | Sam 1      | Student 1 | student1@example.com |
      | teacher2 | Terry 2    | Teacher 2 | teacher2@example.com |
      | student2 | Sam 2      | Student 2 | student2@example.com |
      | teacher3 | Terry 3    | Teacher 3 | teacher3@example.com |
      | student3 | Sam 3      | Student 3 | student3@example.com |
      | helper   | Exam       | Helper    | helper@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | teacher2 | C1     | editingteacher |
      | student2 | C1     | student        |
      | teacher3 | C1     | editingteacher |
      | student3 | C1     | student        |
      | helper   | C1     | teacher        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group 3 | C1     | G3       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | teacher1 | G1    |
      | teacher1 | G3    |
      | student2 | G2    |
      | teacher2 | G2    |
      | teacher2 | G3    |
      | student3 | G3    |
      | helper   | G1    |
      | helper   | G2    |
      | helper   | G3    |
    And the following "activities" exist:
      | activity | name      | intro                 | course | idnumber | groupmode |
      | quiz     | Test quiz | Test quiz description | C1     | quiz1    | 1         |

  Scenario: Override Group 1 as teacher of Group 1
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher1"
    And I press "Add group override"
    Then the "Override group" select box should contain "Group 1"
    And the "Override group" select box should not contain "Group 2"

  Scenario: Add button disabled if there are no groups
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher3"
    Then I should see "There are no groups in this course."
    And the "Add group override" "button" should be disabled

  Scenario: A teacher can create an override
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher1"
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group   | Group 1 |
      | Attempts allowed | 2       |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override group   | Group 3 |
      | Attempts allowed | 2       |
    And I press "Save"
    Then "Group 1" "table_row" should exist
    # Check all column headers are present.
    And I should see "Group" in the "Overrides" "table_row"
    And I should see "Action" in the "Overrides" "table_row"

  Scenario: A teacher with accessallgroups permission should see all group overrides
    Given the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher1"
    Then I should see "Settings overrides exist (Groups: 2)"
    And I follow "Groups: 2"
    And "Group 1" "table_row" should exist
    And "Group 2" "table_row" should exist

  Scenario: A teacher without accessallgroups permission should only see the group overrides within his/her groups, when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher1"
    Then I should see "Settings overrides exist (Groups: 1) for your groups"
    And I follow "Groups: 1"
    Then "Group 1" "table_row" should exist
    And "Group 2" "table_row" should not exist

  Scenario: A non-editing teacher can see the overrides, but not change them
    Given the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "helper"
    Then "Group 1" "table_row" should exist
    And "Group 2" "table_row" should exist
    And "Add group override" "button" should not exist
    And "Edit" "link" should not exist in the "Group 1" "table_row"
    And "Copy" "link" should not exist in the "Group 1" "table_row"
    And "Delete" "link" should not exist in the "Group 1" "table_row"

  Scenario: "Not visible" groups should not be available for group overrides
    Given the following "groups" exist:
      | name                                 | course | idnumber | visibility | participation |
      | Visible to everyone/Participation         | C1     | VP       | 0          | 1             |
      | Only visible to members/Participation     | C1     | MP       | 1          | 1             |
      | Only see own membership                   | C1     | O        | 2          | 0             |
      | Not visible                          | C1     | N        | 3          | 0             |
      | Visible to everyone/Non-Participation     | C1     | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | C1     | MN       | 1          | 0             |
    When I am on the "quiz1" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    Then I should see "Visible to everyone/Participation" in the "Override group" "select"
    And I should see "Visible to everyone/Non-Participation" in the "Override group" "select"
    And I should see "Only visible to members" in the "Override group" "select"
    And I should see "Only visible to members/Non-Participation" in the "Override group" "select"
    And I should see "Only see own membership" in the "Override group" "select"
    And I should not see "Not visible" in the "Override group" "select"

  @javascript
  Scenario: Quiz activity group overrides are displayed on the timeline block
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And I am on the "Test quiz" "quiz activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | timeopen[enabled]  | 1            |
      | timeclose[enabled] | 1            |
      | timeopen           | ##today##    |
      | timeclose          | ##tomorrow## |
    And I press "Save and display"
    When I log in as "student1"
    Then I should see "##tomorrow##%A, %d %B %Y##" in the "Timeline" "block"
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | timeopen     | timeclose          |
      | Test quiz | G1    | ##tomorrow## | ##tomorrow +1day## |
    And I reload the page
    And I should see "##tomorrow +1day##%A, %d %B %Y##" in the "Timeline" "block"
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | timeopen           | timeclose           |
      | Test quiz | G2    | ##tomorrow +1day## | ##tomorrow +3days## |
    And I reload the page
    And I should see "##tomorrow +3days##%A, %d %B %Y##" in the "Timeline" "block"

  @javascript
  Scenario: Quiz activity user override is displayed even if group override exists on the timeline block
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And I am on the "Test quiz" "quiz activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | timeopen[enabled]  | 1            |
      | timeclose[enabled] | 1            |
      | timeopen           | ##today##    |
      | timeclose          | ##tomorrow## |
    And I press "Save and display"
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | timeopen           | timeclose           |
      | Test quiz | G1    | ##tomorrow##       | ##tomorrow +1day##  |
      | Test quiz | G2    | ##tomorrow +1day## | ##tomorrow +3days## |
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Sam 1 Student 1   |
      | timeopen      | ##tomorrow##      |
      | timeclose     | ##tomorrow noon## |
    And I press "Save"
    When I log in as "student1"
    Then I should see "##tomorrow noon##%A, %d %B %Y##" in the "Timeline" "block"

  @javascript
  Scenario: Quiz activity override are not visible on timeline block when student is unenrolled
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | timeopen           | timeclose           |
      | Test quiz | G2    | ##tomorrow +1day## | ##tomorrow +3days## |
    And I am on the "Test quiz" "quiz activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Sam 1 Student 1   |
      | timeopen      | ##tomorrow##      |
      | timeclose     | ##tomorrow noon## |
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "student1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    When I log in as "student1"
    Then "Test quiz" "link" should not exist in the "Timeline" "block"
    And I should not see "##tomorrow noon##%A, %d %B %Y##" in the "Timeline" "block"
