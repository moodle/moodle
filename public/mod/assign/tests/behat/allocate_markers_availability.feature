@mod @mod_assign @javascript
Feature: Marker allocation rules
  In order to control how markers are assigned and edited for student submissions
  As a teacher
  I need marker allocation rules to be enforced consistently

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
      | student3 | Student   | Three    | student3@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | teacher2 | Teacher   | Two      | teacher2@example.com |
      | teacher3 | Teacher   | Three    | teacher3@example.com |
      | teacher4 | Teacher   | Four     | teacher4@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | teacher3 | C1     | editingteacher |
      | teacher4 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                            | assign       |
      | course                              | C1           |
      | idnumber                            | A1           |
      | name                                | Assignment 1 |
      | assignsubmission_onlinetext_enabled | 1            |
      | markingworkflow                     | 1            |
      | markingallocation                   | 1            |
      | markercount                         | 2            |
      | optionalmarkercount                 | 3            |
      | grade[modgrade_type]                | point        |
      | grade[modgrade_point]               | 100          |
    And the following "mod_assign > submissions" exist:
      | assign        | user      | onlinetext           | workflowstate   |
      | Assignment 1  | student1  | student1 submission  | notmarked       |
      | Assignment 1  | student2  | student2 submission  | inreview        |
      | Assignment 1  | student3  | student3 submission  | readyforrelease |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   | enabled |
      | Assignment 1 | student1 | teacher1 |         |
      | Assignment 1 | student1 | teacher2 |         |
      | Assignment 1 | student1 | teacher3 | 1       |
      | Assignment 1 | student1 | teacher4 | 1       |
      | Assignment 1 | student2 | teacher1 |         |
      | Assignment 1 | student2 | teacher2 |         |
      | Assignment 1 | student2 | teacher3 | 1       |
      | Assignment 1 | student2 | teacher4 | 1       |
      | Assignment 1 | student3 | teacher1 |         |
      | Assignment 1 | student3 | teacher2 |         |
      | Assignment 1 | student3 | teacher3 | 1       |
      | Assignment 1 | student3 | teacher4 | 1       |
    And the following "mod_assign > marks" exist:
      | assign       | user     | marker   | mark | workflowstate  |
      | Assignment 1 | student1 | teacher1 | 1    | readyforreview |
      | Assignment 1 | student1 | teacher3 | 2    | readyforreview |
      | Assignment 1 | student2 | teacher1 | 3    | readyforreview |
      | Assignment 1 | student2 | teacher3 | 4    | readyforreview |
      | Assignment 1 | student3 | teacher1 | 5    | readyforreview |
      | Assignment 1 | student3 | teacher3 | 6    | readyforreview |

  Scenario: Marker allocation rules are enforced on the Allocate Markers page
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "More" "button" in the "sticky-footer" "region"
    And I click on "Allocate marker" "link" in the "sticky-footer" "region"
    And I click on "Allocate marker" "button" in the ".modal-footer" "css_element"
    # The batch allocate marker page can't check individual rules.
    # Instead, confirm that markers are unchanged when the user can't allocate a marker.
    When I press "Save changes"
    # This attempts to clear all allocations, but if any conditions aren't met then none should change.
    Then the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Marker 3      | Marker 4     |
      | Student One   | Teacher One | Teacher Two | Teacher Three | Teacher Four |
      | Student Two   | Teacher One | Teacher Two | Teacher Three | Teacher Four |
      | Student Three | Teacher One | Teacher Two | Teacher Three | Teacher Four |

  Scenario: Marker allocation rules are only enforced when there are changes on the Quick Grading page
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "More" "button" in the "sticky-footer" "region"
    And I click on "Allocate marker" "link" in the "sticky-footer" "region"
    And I click on "Allocate marker" "button" in the ".modal-footer" "css_element"
    When I select "Teacher One" from the "Allocated marker 1" singleselect
    And I select "Teacher Four" from the "Allocated marker 2" singleselect
    And I click on "Enable" "checkbox" in the "Allocated marker 3" "form_row"
    And I select "Teacher Three" from the "Allocated marker 3" singleselect
    And I click on "Enable" "checkbox" in the "Allocated marker 4" "form_row"
    And I select "Teacher Two" from the "Allocated marker 4" singleselect
    And I press "Save changes"
    # Marker 1 and 3 haven't changed, so changes to marker 2 and 4 are allowed when both can be allocated.
    Then the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2     | Marker 3      | Marker 4     |
      | Student One   | Teacher One | Teacher Four | Teacher Three | Teacher Two  |
      | Student Two   | Teacher One | Teacher Two  | Teacher Three | Teacher Four |
      | Student Three | Teacher One | Teacher Two  | Teacher Three | Teacher Four |

  Scenario: Marker allocation rules are enforced on the Quick Grading page
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    When I click on "Quick grading" "checkbox"
    Then I should not see "Allocated marker 1" in the "Student One" "table_row"
    And I should see "Allocated marker 2" in the "Student One" "table_row"
    And I should not see "Allocated marker 3" in the "Student One" "table_row"
    And I should see "Allocated marker 4" in the "Student One" "table_row"
    And I should see "Allocated marker 5" in the "Student One" "table_row"

    And I should not see "Allocated marker 1" in the "Student Two" "table_row"
    And I should not see "Allocated marker 2" in the "Student Two" "table_row"
    And I should not see "Allocated marker 3" in the "Student Two" "table_row"
    And I should see "Allocated marker 4" in the "Student Two" "table_row"
    And I should see "Allocated marker 5" in the "Student Two" "table_row"

    And I should not see "Allocated marker 1" in the "Student Three" "table_row"
    And I should not see "Allocated marker 2" in the "Student Three" "table_row"
    And I should not see "Allocated marker 3" in the "Student Three" "table_row"
    And I should not see "Allocated marker 4" in the "Student Three" "table_row"
    And I should not see "Allocated marker 5" in the "Student Three" "table_row"

  Scenario: Marker allocation rules are enforced on the Grader page
    Given I am on the "A1" "assign activity" page logged in as teacher1
    When I go to "Student One" "Assignment 1" activity advanced grading page
    Then the "Marker 1" "select" should be disabled
    And the "Marker 2" "select" should be enabled
    And the "allocatedmarkerenabled[3]" "checkbox" should be disabled
    And the "Marker 3" "select" should be disabled
    And the "allocatedmarkerenabled[4]" "checkbox" should be enabled
    And the "Marker 4" "select" should be enabled
    And the "allocatedmarkerenabled[5]" "checkbox" should be enabled
    And the "Marker 5" "select" should be disabled

    When I go to "Student Two" "Assignment 1" activity advanced grading page
    Then the "Marker 1" "select" should be disabled
    And the "Marker 2" "select" should be disabled
    And the "allocatedmarkerenabled[3]" "checkbox" should be disabled
    And the "Marker 3" "select" should be disabled
    And the "allocatedmarkerenabled[4]" "checkbox" should be enabled
    And the "Marker 4" "select" should be enabled
    And the "allocatedmarkerenabled[5]" "checkbox" should be enabled
    And the "Marker 5" "select" should be disabled

    When I go to "Student Three" "Assignment 1" activity advanced grading page
    Then the "Marker 1" "select" should be disabled
    And the "Marker 2" "select" should be disabled
    And the "allocatedmarkerenabled[3]" "checkbox" should be disabled
    And the "Marker 3" "select" should be disabled
    And the "allocatedmarkerenabled[4]" "checkbox" should be disabled
    And the "Marker 4" "select" should be disabled
    And the "allocatedmarkerenabled[5]" "checkbox" should be disabled
    And the "Marker 5" "select" should be disabled
