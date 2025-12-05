@mod @mod_subsection
Feature: Courses should not lose subsection contents when mod_subsection is disabled
  In order to disable subsections
  As an admin
  Courses needs to be usable with mod_subsection disabled

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name            | course | idnumber    | section |
      | forum      | Activity Sample | C1     | forum1      | 1       |
      | subsection | Subsection1     | C1     | Subsection1 | 1       |
      | data       | Subactivity     | C1     | data1       | 3       |
    And I disable "subsection" "mod" plugin

  Scenario: Teachers should see subsections as orphaned
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "Activity Sample" in the "Section 1" "section"
    And I should not see "Subsection1" in the "Section 1" "section"
    And I should see "Subsection1" in the "page-content" "region"
    Then I should see "Subactivity" in the "Subsection1" "section"
    And "Section 2" "section" should appear before "Subsection1" "section"
    And "Edit settings" "link" should not exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Duplicate" "link" should not exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Hide" "link" should not exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Move" "link" should not exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "View" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Delete" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Permalink" "link" should not exist in the "Subsection1" "core_courseformat > Section actions menu"
    And I should see "This section and its content are not part of the course structure" in the "Subsection1" "section"

  Scenario: Students should not see orphaned subsections
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Activity Sample" in the "page-content" "region"
    And I should not see "Subsection1" in the "page-content" "region"
    And I should not see "Subactivity" in the "page-content" "region"

  Scenario: Enabling again subsections should show the course as before
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should see "Activity Sample" in the "Section 1" "section"
    And I should not see "Subsection1" in the "Section 1" "section"
    And I should see "Subsection1" in the "page-content" "region"
    And I should see "Subactivity" in the "Subsection1" "section"
    And "Section 2" "section" should appear before "Subsection1" "section"
    And I should see "This section and its content are not part of the course structure"
    When I enable "subsection" "mod" plugin
    And I am on "Course 1" course homepage with editing mode on
    Then I should see "Activity Sample" in the "Section 1" "section"
    And I should see "Subsection1" in the "Section 1" "section"
    And I should see "Subactivity" in the "Subsection1" "section"
    And "Subsection1" "section" should appear before "Section 2" "section"
    And "Edit settings" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Duplicate" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Hide" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Move" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And "Delete" "link" should exist in the "Subsection1" "core_courseformat > Section actions menu"
    And I should not see "This section and its content are not part of the course structure"

  @javascript
  Scenario: Deleting the subsections with mod_subsection disabled should not break the course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # Perform teacher actions instead of fast steps to validate delete
    # an orphaned subsection does not break the course.
    And I click on "Edit" "button" in the "Subsection1" "core_courseformat > Section actions menu"
    When I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete section?" "dialogue"
    Then I enable "subsection" "mod" plugin
    And I am on "Course 1" course homepage
    And I should see "Activity Sample" in the "Section 1" "section"
    And I should see "Subsection1" in the "page-content" "region"
    And I should not see "Subactivity" in the "page-content" "region"

  @javascript
  Scenario: Access restrictions are restored when a subsection is deleted while mod_subsection is disabled
    Given I enable "subsection" "mod" plugin
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
    And I am on the "C1 > Subsection1" "course > section settings" page
    And I set the following fields to these values:
      | Access restrictions | Grouping: GX1 |
    And I press "Save changes"
    And I should see "Not available unless: You belong to a group in GX1"
    And I disable "subsection" "mod" plugin
    When I am on "Course 1" course homepage with editing mode on
    And I should see "This section and its content are not part of the course structure" in the "Subsection1" "section"
    And I should see "Not available unless: You belong to a group in GX1"
    And I should not see "Edit restrictions"
    And I am on the "C1 > Subsection1" "course > section" page
    And I click on "Edit" "button" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete section?" "dialogue"
    And I enable "subsection" "mod" plugin
    Then I am on "Course 1" course homepage
    And I should see "Not available unless: You belong to a group in GX1"
    And I should see "Edit restrictions"

  @javascript
  Scenario: Visibility is restored when a subsection is deleted while mod_subsection is disabled
    Given I enable "subsection" "mod" plugin
    And the following "activities" exist:
      | activity   | name        | course | idnumber    | section | visible |
      | subsection | Subsection2 | C1     | Subsection2 | 1       | 0       |
    And I log in as "teacher1"
    And I disable "subsection" "mod" plugin
    When I am on the "C1 > Subsection2" "course > section" page
    And I turn editing mode on
    And I click on "Edit" "button" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete section?" "dialogue"
    And I enable "subsection" "mod" plugin
    Then I am on "Course 1" course homepage
    And I should see "Hidden from students"

  @javascript
  Scenario: Orphaned sections should appear after any other section
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I click on "Add section" "link" in the "course-addsection" "region"
    Then I should see "New section"
    And "Section 2" "section" should appear before "New section" "section"
    And "New section" "section" should appear before "Subsection1" "section"
