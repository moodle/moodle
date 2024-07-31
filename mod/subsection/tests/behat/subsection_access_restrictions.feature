@mod @mod_subsection
Feature: Testing subsection_access_restrictions in mod_subsection
  In order restrict the access to subsections based on conditions
  As a teacher
  I need to set subsection conditions which prevent student access

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "groups" exist:
      | course | name | idnumber |
      | C1     | G1   | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
    And the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
    And the following "grouping groups" exist:
      | grouping | group |
      | GXI1     | GI1   |
    And I log in as "teacher1"

  Scenario: Teacher can set access restrictions to an existing subsection
    Given the following "activities" exist:
      | activity   | name        | course | idnumber    | section |
      | subsection | Subsection1 | C1     | Subsection1 | 1       |
      | data       | Subactivity | C1     | data1       | 3       |
    When I am on the "C1 > Subsection1" "course > section settings" page
    And I set the following fields to these values:
      | Access restrictions | Grouping: GX1 |
    And I press "Save changes"
    Then I should see "Not available unless: You belong to a group in GX1"
    And I log out
    And I am on the "Course 1" "course" page logged in as "student1"
    And I should see "Subsection1" in the "region-main" "region"
    And I should see "Subactivity" in the "region-main" "region"
    And I log out
    And I am on the "Course 1" "course" page logged in as "student2"
    And I should see "Not available unless: You belong to a group in GX1"
    And I should not see "Subactivity"

  Scenario: Teacher sets access restrictions to a new subsection
    When I add a subsection activity to course "Course 1" section "1" and I fill the form with:
      | Name                | Subsection2   |
      | Access restrictions | Grouping: GX1 |
    Then I should see "Not available unless: You belong to a group in GX1"
    And I log out
    And I am on the "Course 1" "course" page logged in as "student1"
    And I should see "Subsection2" in the "region-main" "region"
    And I log out
    And I am on the "Course 1" "course" page logged in as "student2"
    And I should see "Subsection2" in the "region-main" "region"
    And I should see "Not available unless: You belong to a group in GX1"
