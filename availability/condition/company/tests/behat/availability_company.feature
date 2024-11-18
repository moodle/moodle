@availability @availability_company
Feature: availability_company
  In order to control student access to activities
  As a teacher
  I need to set company conditions which prevent student access

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

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Start to add a Page. If there aren't any companys, there's no Group option.
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should not exist in the "Add restriction..." "dialogue"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"

    # Back to course page but add companys.
    Given the following "companys" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    # This step used to be 'And I follow "C1"', but Chrome thinks the breadcrumb
    # is not clickable, so we'll go via the home page instead.
    And I am on "Course 1" course homepage
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should exist in the "Add restriction..." "dialogue"

    # Page P1 any company.
    Given I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any company)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"

    # Page P2 with company G1.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G1"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P3 with company G2
    And I add a "Page" to section "3"
    And I set the following fields to these values:
      | Name         | P3 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G2"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"

    # Add to companys and log out/in again.
    Given the following "company members" exist:
      | user     | company |
      | student1 | GI1   |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # P1 (any companys) and P2 should show but not P3.
    Then I should see "P1" in the "region-main" "region"
    And I should see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"

  @javascript
  Scenario: Condition display with filters
    # Teacher sets up a restriction on company G1, using multilang filter.
    Given the following "companys" exist:
      | name                                                                                        | course | idnumber |
      | <span lang="en" class="multilang">G-One</span><span lang="fr" class="multilang">G-Un</span> | C1     | GI1      |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    # The activity names filter is enabled because it triggered a bug in older versions.
    And the "activitynames" filter is "on"
    And the "activitynames" filter applies to "content and headings"
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G-One"
    And I click on "Save and return to course" "button"
    And I log out

    # Student sees information about no access to company, with company name in correct language.
    When I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Not available unless: You belong to G-One"
    And I should not see "G-Un"
