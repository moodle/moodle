@availability @availability_group
Feature: availability_group
  In order to control student access to activities
  As a teacher
  I need to set group conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 3           |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name  |
      | page     | C1     | P1    |
      | page     | C1     | P2    |
      | page     | C1     | P3    |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should not exist in the "Add restriction..." "dialogue"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"

    # Back to course page but add groups.
    Given the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    # This step used to be 'And I follow "C1"', but Chrome thinks the breadcrumb
    # is not clickable, so we'll go via the home page instead.
    And I am on the "P1" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should exist in the "Add restriction..." "dialogue"

    # Page P1 any group.
    Given I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any group)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P2 with group G1.
    And I am on the "P2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G1"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P3 with group G2
    And I am on the "P3" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G2"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"

    # Add to groups and log out/in again.
    Given the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
    And I am on "Course 1" course homepage

    # P1 (any groups) and P2 should show but not P3.
    Then I should see "P1" in the "region-main" "region"
    And I should see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"

  @javascript
  Scenario: Condition display with filters
    # Teacher sets up a restriction on group G1, using multilang filter.
    Given the following "groups" exist:
      | name                                                                                        | course | idnumber |
      | <span lang="en" class="multilang">G-One</span><span lang="fr" class="multilang">G-Un</span> | C1     | GI1      |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    # The activity names filter is enabled because it triggered a bug in older versions.
    And the "activitynames" filter is "on"
    And the "activitynames" filter applies to "content and headings"
    And I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G-One"
    And I click on "Save and return to course" "button"

    # Student sees information about no access to group, with group name in correct language.
    When I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Not available unless: You belong to G-One"
    And I should not see "G-Un"

  @javascript
  Scenario: Condition using a hidden group
    Given the following "groups" exist:
      | name         | course | idnumber | visibility |
      | Hidden Group | C1     | GA       | 3          |
    And I log in as "teacher1"
    And I add a page activity to course "Course 1" section "1"
    And I expand all fieldsets

    # Page P1 any group.
    And I am on the "P1" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And "Group" "button" should exist in the "Add restriction..." "dialogue"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any group)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P2 with hidden group.
    And I am on the "P2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Hidden Group"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
    And I should not see "Hidden Group"

    # Add to groups and log out/in again.
    And the following "group members" exist:
      | user     | group |
      | student1 | GA    |
    And I am on "Course 1" course homepage

    # P1 (any groups) and P2 should show. The user should not see the hidden group mentioned anywhere.
    And I should see "P1" in the "region-main" "region"
    And I should see "P2" in the "region-main" "region"
    And I should not see "Hidden Group"
