@core @core_user
Feature: Course participants can be filtered
  In order to filter the list of course participants
  As a user
  I need to visit the course participants page and apply the appropriate filters

  Background:
    Given the following "courses" exist:
      | fullname | shortname | groupmode | startdate        |
      | Course 1 | C1        |     1     | ##5 months ago## |
      | Course 2 | C2        |     0     | ##4 months ago## |
      | Course 3 | C3        |     0     | ##3 months ago## |
    And the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | frog       | Favourite frog |
    And the following "users" exist:
      | username | firstname | lastname | email                     | idnumber | country | city   | maildisplay | profile_field_frog |
      | student1 | Student   | 1        | student1@example.com      | SID1     |         | SCITY1 | 0           | Kermit             |
      | student2 | Student   | 2        | student2@example.com      | SID2     | GB      | SCITY2 | 1           | Mr Toad            |
      | student3 | Student   | 3        | student3@example.com      | SID3     | AU      | SCITY3 | 0           |                    |
      | student4 | Student   | 4        | student4@moodle.com       | SID4     | AT      | SCITY4 | 0           |                    |
      | student5 | Trendy    | Learnson | trendy@learnson.com       | SID5     | AU      | SCITY5 | 0           |                    |
      | patricia | Patricia  | Pea      | patricia.pea1@example.org | TID1     | US      | TCITY1 | 0           |                    |
    And the following "course enrolments" exist:
      | user     | course | role           | status | timeend       |
      | student1 | C1     | student        |    0   |               |
      | student2 | C1     | student        |    1   |               |
      | student3 | C1     | student        |    0   |               |
      | student4 | C1     | student        |    0   | ##yesterday## |
      | student1 | C2     | student        |    0   |               |
      | student2 | C2     | student        |    0   |               |
      | student3 | C2     | student        |    0   |               |
      | student5 | C2     | student        |    0   |               |
      | student1 | C3     | student        |    0   |               |
      | student2 | C3     | student        |    0   |               |
      | student3 | C3     | student        |    0   |               |
      | patricia | C1     | editingteacher |    0   |               |
      | patricia | C2     | editingteacher |    0   |               |
      | patricia | C3     | editingteacher |    0   |               |
    And the following "last access times" exist:
      | user     | course | lastaccess      |
      | student1 | C1     | ##yesterday##   |
      | student1 | C2     | ##2 weeks ago## |
      | student2 | C1     | ##4 days ago##  |
      | student3 | C1     | ##2 weeks ago## |
      | student4 | C1     | ##3 weeks ago## |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group A | C3     | GA       |
      | Group B | C3     | GB       |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
      | student2 | G2    |
      | student3 | G2    |
      | student1 | GA    |
      | student2 | GA    |
      | student2 | GB    |

  @javascript
  Scenario: No filters applied
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario Outline: Filter users for a course with a single value
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "<matchtype>"
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "<filtervalue>"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should see "<expected3>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | matchtype | filtertype             | filtervalue | expected1    | expected2 | expected3    | notexpected1 | notexpected2 |
      | Any       | Groups                 | No group    | Student 1    | Student 4 | Patricia Pea | Student 2    | Student 3    |
      | All       | Groups                 | No group    | Student 1    | Student 4 | Patricia Pea | Student 2    | Student 3    |
      | None      | Groups                 | No group    | Student 2    | Student 3 |              | Student 1    | Patricia Pea |
      | Any       | Role                   | Student     | Student 1    | Student 2 | Student 3    | Patricia Pea | XX-IGNORE-XX |
      | All       | Role                   | Student     | Student 1    | Student 2 | Student 3    | Patricia Pea | XX-IGNORE-XX |
      | None      | Role                   | Student     | Patricia Pea |           |              | Student 1    | Student 2    |
      | Any       | Status                 | Active      | Student 1    | Student 3 | Patricia Pea | Student 2    | Student 4    |
      | All       | Status                 | Active      | Student 1    | Student 3 | Patricia Pea | Student 2    | Student 4    |
      | None      | Status                 | Active      | Student 2    | Student 4 |              | Student 1    | Student 3    |
      | Any       | Inactive for more than | 1 week      | Student 3    | Student 4 |              | Student 1    | Student 2    |
      | All       | Inactive for more than | 1 week      | Student 3    | Student 4 |              | Student 1    | Student 2    |
      | None      | Inactive for more than | 1 week      | Student 1    | Student 2 | Patricia Pea | Student 3    | XX-IGNORE-XX |

  @javascript
  Scenario Outline: Filter users for a course with multiple values for a single filter
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "<matchtype>"
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "<filtervalue1>,<filtervalue2>"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should see "<expected3>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | matchtype | filtertype | filtervalue1 | filtervalue2 | expected1 | expected2 | expected3 | notexpected1 | notexpected2 |
      | Any       | Groups     | Group 1      | Group 2      | Student 2 | Student 3 |           | Student 1    | XX-IGNORE-XX |
      | All       | Groups     | Group 1      | Group 2      | Student 2 |           |           | Student 1    | Student 3    |
      | None      | Groups     | Group 1      | Group 2      | Student 1 | Patricia Pea |           | Student 2    | Student 3    |

  @javascript
  Scenario Outline: Filter users which are group members in several courses
    Given I am on the "C3" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "<filtervalue>"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | filtertype | filtervalue | expected1 | expected2 | notexpected1 | notexpected2 |
      | Groups     | No group    | Student 3 |           | Student 1    | Student 2    |
      | Groups     | Group A     | Student 1 | Student 2 | Student 3    | XX-IGNORE-XX |
      | Groups     | Group B     | Student 2 |           | Student 1    | Student 3    |

  @javascript
  Scenario: In separate groups mode, a student in a single group can only view and filter by users in their own group
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Unsuspend student 2 for to improve coverage of this test.
    And I click on "Edit enrolment" "icon" in the "Student 2" "table_row"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    And I log out

    # Default view should have groups filter pre-set.
    # Match:
    #   Groups Any ["Group 2"].

    When I log in as "student3"
    And I am on "Course 1" course homepage
    And I navigate to course participants

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Group 2" in the "Filter 1" "fieldset"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Group 1" in the "Filter 1" "fieldset"

    # Testing result of removing groups filter row.
    # Match any available user.
    When I click on "Remove filter row" "button" in the "Filter 1" "fieldset"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

    # Testing result of applying groups filter manually.
    # Match:
    #   Group Any ["Group 2"].

    # Match Groups Any ["Group 2"]
    Given I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group 2"

    And I open the autocomplete suggestions list in the "Filter 1" "fieldset"
    And I should not see "Group 1" in the ".form-autocomplete-suggestions" "css_element"

    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

    # Testing result of removing groups filter by clearing all filters.
    # Match any available user.
    When I click on "Clear filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: In separate groups mode, a student in multiple groups can only view and filter by users in their own groups
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Unsuspend student 2 for to improve coverage of this test.
    And I click on "Edit enrolment" "icon" in the "Student 2" "table_row"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    And I log out

    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I navigate to course participants

    # Default view should have groups filter pre-set.
    # Match:
    #   Groups Any ["Group 1", "Group 2"].

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should see "Group 1" in the "Filter 1" "fieldset"
    And I should see "Group 2" in the "Filter 1" "fieldset"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"

    # Testing result of removing groups filter row.
    # Match any available user.
    When I click on "Remove filter row" "button" in the "Filter 1" "fieldset"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

    # Testing result of applying groups filter manually.
    # Match:
    #   Groups Any ["Group 1"].

    # Match Groups Any ["Group 1"]
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"

    And I open the autocomplete suggestions list in the "Filter 1" "fieldset"
    And I should see "Group 2" in the ".form-autocomplete-suggestions" "css_element"
    And I press the escape key

    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group 1"

    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

    # Testing result of removing groups filter by clearing all filters.
    # Match any available user.
    When I click on "Clear filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: Filter users who have no role in a course
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Remove the user role.
    And I click on "Student 1's role assignments" "link"
    And I click on ".form-autocomplete-selection [aria-selected=true]" "css_element"
    And I press the escape key
    And I click on "Save changes" "link"

    # Match:
    #   Roles All ["No roles"].

    # Match Roles All ["No roles"].
    When I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "No roles"

    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Multiple filters applied (All filterset match type)
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Match Any:
    #   Roles All ["Student"] and
    #   Status Any ["Active"].

    # Match Roles All ["Student"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Student"

    # Match Status All ["Active"].
    And I click on "Add condition" "button"
    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Active"

    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match Any:
    #   Roles All ["Student"]; and
    #   Status Any ["Active"]; and
    #   Enrolment method Any ["Manual"]; and
    #   Groups Any ["Group 2"].

    # Match enrolment method Any ["Manual"]
    When I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Enrolment methods"
    And I set the field "Type or select..." in the "Filter 3" "fieldset" to "Manual enrolments"

    # Match Groups Any ["Group 2"]
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 4" "fieldset" to "All"
    And I set the field "type" in the "Filter 4" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 4" "fieldset" to "Group 2"
    And I click on "Apply filters" "button"

    Then I should see "Student 3" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Change the active status filter to inactive.
    # Match Any:
    #   Roles All ["Student"]; and
    #   Status Any ["Inactive"]; and
    #   Enrolment method Any ["Manual"]; and
    #   Groups Any ["Group 2"].

    # Match Status All ["Inactive"].
    And I click on "Active" "autocomplete_selection"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Inactive"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Set both statuses (match any).
    # Match Any:
    #   Roles All ["Student"]; and
    #   Status Any ["Active", "Inactive"]; and
    #   Enrolment method Any ["Manual"]; and
    #   Groups Any ["Group 2"].

    # Match Status Any ["Active", "Inactive"].
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Active,Inactive"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Set both statuses (match all).
    # Match Any:
    #   Roles All ["Student"]; and
    #   Status Any ["Active", "Inactive"]; and
    #   Enrolment method Any ["Manual"]; and
    #   Groups Any ["Group 2"].

    # Match Status All ["Active", "Inactive"].
    When I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

  @javascript
  Scenario: Multiple filters applied (Any filterset match type)
    Given I log in as "patricia"
    And I am on "Course 1" course homepage
    And I navigate to course participants

    # Match Any:
    #   Roles All ["Teacher"] and
    #   Status Any ["Active"].

    # Match Roles all Roles ["Teacher"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Teacher"

    # Match Status Any ["Active"].
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Active"

    # Set filterset to match any.
    And I set the field "Match" to "Any"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Match Any:
    #   Roles All ["Teacher"] and
    #   Status None ["Active"].

    # Match Status Not ["Active"]
    When I set the field "Match" in the "Filter 2" "fieldset" to "None"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

    # Add a keyword filter.
    # Match Any:
    #   Roles All ["Teacher"]; and
    #   Status None ["Active"]; and
    #   Keyword Any ["patricia"].

    # Match Keyword Any ["patricia"].
    When I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 3" "fieldset" to "patricia"

    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

  @javascript
  Scenario: Multiple filters applied (None filterset match type)
    Given I log in as "patricia"
    And I am on "Course 1" course homepage
    And I navigate to course participants

    # Match None:
    #   Roles All ["Teacher"] and
    #   Status Any ["Active"].

    # Set the Roles to "All" ["Teacher"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Teacher"

    # Set the Status to "Any" ["Active"].
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Active"

    # Set filterset to match None.
    And I set the field "Match" to "None"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match None:
    #   Roles All ["Teacher"] and
    #   Status None ["Active"]
    # Set the Status to "None ["Active"]
    When I set the field "Match" in the "Filter 2" "fieldset" to "None"
    And I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match None:
    #   Roles All ["Teacher"] and
    #   Status None ["Active"] and
    #   Keyword Any ["3@"]
    # Set the Keyword to "Any" ["3@"]
    When I click on "Add condition" "button"
    Then I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 3" "fieldset" to "3@"

    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match None:
    #   Roles All ["Teacher"] and
    #   Status None ["Active"] and
    #   Keyword None ["3@"].

    # Set the Keyword to "None" ["3@"]
    When I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I click on "Apply filters" "button"

    Then I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Filter match by one or more keywords and modified match types
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Match:
    #   Keyword Any ["1@example"].

    # Set the Keyword to "Any" ["1@example"]
    When I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "1@example"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"

    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Match:
    #   Keyword All ["1@example"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Match:
    #   Keyword None ["1@example"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "None"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Set two keyword values.
    # Match:
    #   Keyword None ["1@example", "moodle"].
    When I set the field "Type..." to "1@example, moodle"
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Set two keyword values.
    # Match:
    #   Keyword Any ["1@example", "moodle"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

    # Match:
    #   Keyword All ["1@example", "moodle"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

  @javascript
  Scenario: Reorder users without losing filter
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    When I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Student"
    And I click on "Apply filters" "button"

    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    When I click on "Surname" "link"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Only possible to add filter rows for the number of filters available
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    When I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 3" "fieldset" to "Roles"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 4" "fieldset" to "Enrolment methods"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 5" "fieldset" to "Groups"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 6" "fieldset" to "Inactive for more than"

    Then the "Add condition" "button" should be disabled

  @javascript
  Scenario: Rendering filter options for teachers in a course that don't support groups
    Given I am on the "C2" "Course" page logged in as "patricia"
    When I navigate to course participants
    Then I should see "Roles" in the "type" "field"
    And I should see "Enrolment methods" in the "type" "field"
    But I should not see "Groups" in the "type" "field"

  @javascript
  Scenario: Rendering filter options for students who have limited privileges
    Given I am on the "C2" "Course" page logged in as "student1"
    When I navigate to course participants
    Then I should see "Roles" in the "type" "field"
    But I should not see "Status" in the "type" "field"
    And I should not see "Enrolment methods" in the "type" "field"

  @javascript
  Scenario: Filter by user identity fields
    Given the following config values are set as admin:
        | showuseridentity | idnumber,email,city,country |
    And I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Search by email (only) - should only see visible email + own.
    # Match:
    #   Keyword Any ["student1@example.com"].

    # Set the Keyword to "Any" ["student1@example.com"]
    When I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "student1@example.com"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    But I should not see "Student 2" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Search by idnumber (only).
    # Match:
    #   Keyword Any ["SID"].

    # Set the Keyword to "Any" ["SID"]
    And I click on "student1@example.com" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "SID"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"

    # Search by city (only).
    # Match:
    #   Keyword Any ["SCITY"].

    # Set the Keyword to "Any" ["SCITY"]
    And I click on "SID" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "SCITY"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"

    # Search by country text (only) - should not match.
    # Match:
    #   Keyword Any ["GB"].

    # Set the Keyword to "Any" ["GB"]
    And I click on "SCITY" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "GB"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

    # Check no match.
    # Match:
    #   Keyword Any ["NOTHING"].

    # Set the Keyword to "Any" ["NOTHING"]
    And I click on "GB" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "NOTHING"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

  @javascript @skip_chrome_zerosize
  Scenario: Filter by user identity fields when cannot see the field data
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | moodle/site:viewuseridentity | Prevent |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country |
    And I log out

    And I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Match:
    #   Keyword Any ["@example.com"].

    # Search by email (only) - should only see visible email + own.
    # Set the Keyword to "Any" ["@example.com"]
    When I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "@example."
    And I click on "Apply filters" "button"

    Then I should see "Student 2" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Search for other fields - should only see own results.

    # Match:
    #   Keyword Any ["SID"].
    # Set the Keyword to "Any" ["SID"]
    And I click on "@example." "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "SID"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

    # Match:
    #   Keyword Any ["TID"].

    # Set the Keyword to "Any" ["TID"]
    And I click on "SID" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "TID"
    And I click on "Apply filters" "button"

    Then I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

    # Match:
    #   Keyword Any ["CITY"].

    # Set the Keyword to "Any" ["CITY"]
    And I click on "TID" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "CITY"
    And I click on "Apply filters" "button"

    Then I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"

    # No data matches regardless of capabilities.
    # Match:
    #   Keyword Any ["NOTHING"].

    # Set the Keyword to "Any" ["NOTHING"]
    And I click on "CITY" "autocomplete_selection"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "NOTHING"
    And I click on "Apply filters" "button"

    Then I should see "Nothing to display"

  @javascript
  Scenario: Individual filters can be removed, which will automatically refresh the participants list
    # Match All:
    #   Roles All ["Student"]; and
    #   Keyword Any ["@example.com"].

    # Set the Roles to "All" ["Student"].
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Student"

    # Set the Keyword to "Any" ["@example.com"]
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 2" "fieldset" to "@example"

    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match:
    #   Keyword Any ["@example.com"].
    When I click on "Remove filter row" "button" in the "Filter 1" "fieldset"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"

  @javascript
  Scenario: All filters can be cleared at once
    # Match All:
    #   Roles All ["Student"]; and
    #   Keyword Any ["@example.com"].

    # Set the Roles to "All" ["Student"].
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants
    When I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Student"

    # Set the Keyword to "All" ["@example.com"].
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 2" "fieldset" to "@example"

    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match Any.
    When I click on "Clear filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Filterset match type is reset when reducing to a single filter
    # Match None:
    #   Keyword Any ["@example.com"]; and
    #   Roles All ["Teacher"].
    Given I am on the "C1" "Course" page logged in as "patricia"
    And I navigate to course participants

    # Set the Keyword to "Any" ["@example.com"]
    When I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." to "@example.com"

    # Set the Roles to "All" ["Student"].
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I set the field "type" in the "Filter 2" "fieldset" to "Roles"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Student"

    # Match none of student role and @example.com keyword.
    And I set the field "Match" to "None"
    And I click on "Apply filters" "button"

    Then I should see "Patricia Pea" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

    # Match:
    #   Keyword Any ["@example.com"].
    # When removing the pen-ultimate filter, the filterset match type is removed too.
    When I click on "Remove filter row" "button" in the "Filter 2" "fieldset"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

    # Match Any:
    #   Keyword Any ["@example.com"]; and
    #   Role All ["Student"].
    # The default filterset match (Any) should apply.
    # Set the Roles to "All" ["Student"].
    When I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I set the field "type" in the "Filter 2" "fieldset" to "Role"
    And I set the field "Type or select..." in the "Filter 2" "fieldset" to "Student"
    And I click on "Apply filters" "button"

    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Filter users by first initial
    # Match:
    #   No filters; and
    # First initial "T".
    Given I am on the "C2" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    When I click on "T" "link" in the ".firstinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    But I should not see "Patricia Pea" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

  @javascript
  Scenario: Filter users by last initial
    # Match:
    #   No filters; and
    # Last initial "L".
    Given I am on the "C2" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    When I click on "L" "link" in the ".lastinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Filter users by first and last initials
    # Match:
    #   No filters; and
    # First initial "T"; and
    # Last initial "L".
    Given I am on the "C2" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"
    When I click on "T" "link" in the ".firstinitial" "css_element"
    And I click on "L" "link" in the ".lastinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Initials filtering is always applied in addition to any other filtering
    # Match:
    #   Roles All ["Teacher"]; and
    # First initial "T".
    Given I am on the "C2" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Patricia Pea" in the "participants" "table"

    # Set the Role to "Any" ["Student"].
    When I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Role"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Student"
    And I click on "Apply filters" "button"

    # Last initial "T".
    And I click on "T" "link" in the ".firstinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    But I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia Pea" in the "participants" "table"

  @javascript
  Scenario: Filtering works correctly with custom profile fields
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_frog |
    And I am on the "C2" "Course" page logged in as "patricia"
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." to "Kermit"
    And I press enter
    And I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
