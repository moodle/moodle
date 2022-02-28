@availability @availability_grouping
Feature: availability_grouping
  In order to control student access to activities
  As a teacher
  I need to set grouping conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "groups" exist:
      | name | course | idnumber |
      | G1   | C1     | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
    # Basic setup.
    And the following "activities" exist:
      | activity | course | name  |
      | page     | C1     | P1    |
      | page     | C1     | P2    |

  @javascript
  Scenario: Test condition
    # Start to add a Page. If there aren't any groupings, there's no Grouping option.
    Given I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Grouping" "button" should not exist in the "Add restriction..." "dialogue"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"

    # Back to course page but add groups.
    # This step used to be 'And I follow "C1"', but Chrome thinks the breadcrumb
    # is not clickable, so we'll go via the home page instead.
    And I am on "Course 1" course homepage
    And the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
      | GX2  | C1     | GXI2     |
    And I am on the "P1" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Grouping" "button" should exist in the "Add restriction..." "dialogue"

    # Page P1 grouping GX1.
    Given I click on "Grouping" "button"
    And I set the field "Grouping" to "GX1"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P2 with grouping GX2.
    And I am on the "P2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grouping" "button"
    And I set the field "Grouping" to "GX2"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

    # Add group to grouping and log out/in again.
    And the following "grouping groups" exist:
      | grouping | group  |
      | GXI1     | GI1    |
    And I am on the "Course 1" "course" page logged in as "student1"

    # P1 should show but not B2.
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

  @javascript
  Scenario: Check grouping access restriction message on course homepage
    Given the following "groupings" exist:
      | name        | course | idnumber |
      | Grouping A  | C1     | GA      |
    And the following "grouping groups" exist:
      | grouping  | group |
      | GA        | GI1   |
    And the following "activities" exist:
      | activity  | name        | intro              | course | idnumber | groupmode | grouping |
      | assign    | Test assign | Assign description | C1     | assign1  | 1         | GA       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I open "Test assign" actions menu
    And I choose "Edit settings" in the open action menu
    And I expand all fieldsets
    And the field "groupingid" matches value "Grouping A"
    And I press "Add group/grouping access restriction"
    When I press "Save and return to course"
    Then I should see "Not available unless: You belong to a group in Grouping A"

  @javascript
  Scenario: Condition display with filters
    # Teacher sets up a restriction on group G1, using multilang filter.
    Given the following "groupings" exist:
      | name                                                                                          | course | idnumber |
      | <span lang="en" class="multilang">Gr-One</span><span lang="fr" class="multilang">Gr-Un</span> | C1     | GA       |
    And the following "activities" exist:
      | activity  | name        | intro              | course | idnumber | groupmode | grouping |
      | assign    | Test assign | Assign description | C1     | assign1  | 1         | GA       |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    # The activity names filter is enabled because it triggered a bug in older versions.
    And the "activitynames" filter is "on"
    And the "activitynames" filter applies to "content and headings"
    And I am on the "Test assign" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I press "Add group/grouping access restriction"
    And I press "Save and return to course"

    # Student sees information about no access to group, with group name in correct language.
    When I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Not available unless: You belong to a group in Gr-One"
    And I should not see "Gr-Un"
