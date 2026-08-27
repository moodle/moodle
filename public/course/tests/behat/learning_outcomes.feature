@core_course @javascript
Feature: Course learning outcomes page visibility
  In order to understand how outcomes map to activities
  As a teacher or student
  I can view learning outcomes with activity visibility represented correctly.

  Background:
    Given the following "course" exists:
      | fullname    | Course 1 |
      | shortname   | C1       |
      | category    | 0        |
      | numsections | 3        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    # Learning outcomes without scales.
    And the following "grade outcomes" exist:
      | fullname                       | shortname | course |
      | Outcome A - Always visible     | outa      | C1     |
      | Outcome B - Hidden activity    | outb      | C1     |
      | Outcome C - Date restricted    | outc      | C1     |
      | Outcome D - Hidden section     | outd      | C1     |
      | Outcome E - Restricted section | oute      | C1     |
    # Learning outcome with a scale.
    And the following "scales" exist:
      | name    | scale            |
      | Scale 1 | Needs work, Good |
    And the following "grade outcomes" exist:
      | fullname                       | shortname | course | scale   |
      | Outcome F - Visible with scale | outf      | C1     | Scale 1 |
    And the following "activities" exist:
      | activity | course | section | name                          | idnumber                      | visible | availability                                                           | showavailability |
      | assign   | C1     | 1       | Visible assignment            | visible-assignment            | 1       |                                                                        |                  |
      | assign   | C1     | 1       | Hidden assignment             | hidden-assignment             | 0       |                                                                        |                  |
      | assign   | C1     | 1       | Restricted assignment         | restricted-activity           | 1       | {"op":"&","c":[{"type":"date","d":"<","t":1393977600}],"showc":[true]} | 1                |
      | assign   | C1     | 2       | Hidden section assignment     | hidden-section-assignment     | 1       |                                                                        |                  |
      | assign   | C1     | 3       | Restricted section assignment | restricted-section-assignment | 1       |                                                                        |                  |
      | assign   | C1     | 1       | Visible assignment with scale | visible-assignment-with-scale | 1       |                                                                        |                  |
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I hide section "2"
    And I edit the section "3"
    And I expand all fieldsets
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1" "availability_date > Date Restriction" to "2025"
    And I set the field "Direction" in the "1" "availability_date > Date Restriction" to "until"
    And I press "Save changes"
    And I map the following outcomes to activities:
      | outcome | activity idnumber             |
      | outa    | visible-assignment            |
      | outb    | hidden-assignment             |
      | outc    | restricted-activity           |
      | outd    | hidden-section-assignment     |
      | oute    | restricted-section-assignment |
      | outf    | visible-assignment-with-scale |

  Scenario: Teacher sees all mapped activities and visibility information
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    When I navigate to "Learning outcomes" in current page administration
    Then I should see "Outcome A - Always visible"
    And I should see "Outcome B - Hidden activity"
    And I should see "Outcome C - Date restricted"
    And I should see "Outcome D - Hidden section"
    And I should see "Outcome E - Restricted section"
    And I should see "Outcome F - Visible with scale"
    # Teacher can always see assignment and link.
    And I should see "Visible assignment"
    And ".activity-instance a" "css_element" should exist in the "Visible assignment" "activity"
    And I should see "Hidden assignment"
    And ".activity-instance a" "css_element" should exist in the "Hidden assignment" "activity"
    And I should see "Restricted assignment"
    And ".activity-instance a" "css_element" should exist in the "Restricted assignment" "activity"
    And I should see "Hidden section assignment"
    And ".activity-instance a" "css_element" should exist in the "Hidden section assignment" "activity"
    And I should see "Restricted section assignment"
    And ".activity-instance a" "css_element" should exist in the "Restricted section assignment" "activity"
    And I should see "Visible assignment with scale"
    And ".activity-instance a" "css_element" should exist in the "Visible assignment with scale" "activity"

  Scenario: Student sees only student-visible activities and related availability message
    Given I am on the "Course 1" "Course" page logged in as "student1"
    When I navigate to "Learning outcomes" in current page administration
    Then I should see "Outcome A - Always visible"
    And I should see "Outcome B - Hidden activity"
    And I should see "Outcome C - Date restricted"
    And I should see "Outcome D - Hidden section"
    And I should see "Outcome E - Restricted section"
    And I should see "Outcome F - Visible with scale"
    # Student can see assignment and link.
    And I should see "Visible assignment"
    And ".activity-instance a" "css_element" should exist in the "Visible assignment" "activity"
    # Student cannot see assignment.
    And I should not see "Hidden assignment" in the "region-main" "region"
    # Student can see assignment, but not the link.
    And I should see "Restricted assignment" in the "region-main" "region"
    And ".activity-instance a" "css_element" should not exist in the "Restricted assignment" "activity"
    # Student cannot see the hidden section assignment.
    And I should not see "Hidden section assignment" in the "region-main" "region"
    # Student cannot see the restricted section assignment.
    And I should not see "Restricted section assignment" in the "region-main" "region"
    # Student can see the assignment with scale.
    And I should see "Visible assignment with scale"
    And ".activity-instance a" "css_element" should exist in the "Visible assignment with scale" "activity"

  Scenario: Verify breadcrumbs for learning outcomes in course context
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "Learning outcomes" in current page administration
    And I click on "Manage learning outcomes" "button"
    When I click on "Add a new learning outcome" "button"
    Then "Add a learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I set the field "Full name" to "Test outcome for breadcrumb"
    And I set the field "Short name" to "test_breadcrumb"
    And I press "Save changes"
    And I click on "Edit" "link"
    And "Edit learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "text" should exist in the ".breadcrumb" "css_element"
    And "Learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Delete" "link"
    And "Delete learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "text" should exist in the ".breadcrumb" "css_element"
    And "Learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Import" "link"
    And "Import learning outcomes" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "text" should exist in the ".breadcrumb" "css_element"
    And "Learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Learning outcomes" "link" in the ".breadcrumb" "css_element"
    And I should see "The following learning outcomes describe what you will learn"
