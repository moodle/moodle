@core @core_courseformat @core_course @format_weeks @show_editor @javascript
Feature: Weeks format bulk activity actions.
  In order to edit the course weeks
  As a teacher
  I need to be able to edit weeks in bulk.

  Background:
    Given the following "course" exists:
      | fullname    | Course 1  |
      | shortname   | C1        |
      | category    | 0         |
      | numsections | 4         |
      | format      | weeks     |
      | startdate   | 957139200 |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1     | sample2  | 1       |
      | assign   | Activity sample 3 | Test assignment description | C1     | sample3  | 2       |
      | assign   | Activity sample 4 | Test assignment description | C1     | sample4  | 2       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk hide weeks
    Given I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "1 May - 7 May" "section"
    And I should not see "Hidden from students" in the "8 May - 14 May" "section"
    And I should not see "Hidden from students" in the "15 May - 21 May" "section"
    And I should not see "Hidden from students" in the "22 May - 28 May" "section"
    When I click on "Select section 1 May - 7 May" "checkbox"
    And I click on "Select section 8 May - 14 May" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    Then I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should see "Hidden from students" in the "Activity sample 2" "activity"
    And I should see "Hidden from students" in the "Activity sample 3" "activity"
    And I should see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "Hidden from students" in the "1 May - 7 May" "section"
    And I should see "Hidden from students" in the "8 May - 14 May" "section"
    And I should not see "Hidden from students" in the "15 May - 21 May" "section"
    And I should not see "Hidden from students" in the "22 May - 28 May" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk show weeks
    Given I click on "Select section 1 May - 7 May" "checkbox"
    And I click on "Select section 15 May - 21 May" "checkbox"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "Hidden from students" in the "1 May - 7 May" "section"
    And I should not see "Hidden from students" in the "8 May - 14 May" "section"
    And I should see "Hidden from students" in the "15 May - 21 May" "section"
    And I should not see "Hidden from students" in the "22 May - 28 May" "section"
    When I click on "Select section 1 May - 7 May" "checkbox"
    And I click on "Select section 8 May - 14 May" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Show on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    Then I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "1 May - 7 May" "section"
    And I should not see "Hidden from students" in the "8 May - 14 May" "section"
    And I should see "Hidden from students" in the "15 May - 21 May" "section"
    And I should not see "Hidden from students" in the "22 May - 28 May" "section"

  Scenario: Delete a single week using bulk action
    Given I should see "1 May - 7 May" in the "region-main" "region"
    And I should see "8 May - 14 May" in the "region-main" "region"
    And I should see "15 May - 21 May" in the "region-main" "region"
    And I should see "22 May - 28 May" in the "region-main" "region"
    And I should see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should see "Activity sample 2" in the "1 May - 7 May" "section"
    And I should see "Activity sample 3" in the "8 May - 14 May" "section"
    And I should see "Activity sample 4" in the "8 May - 14 May" "section"
    And I click on "Select section 1 May - 7 May" "checkbox"
    And I click on "Select section 8 May - 14 May" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete sections" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete selected sections?" "dialogue"
    Then I should see "1 May - 7 May" in the "region-main" "region"
    And I should see "8 May - 14 May" in the "region-main" "region"
    And I should not see "15 May - 21 May" in the "region-main" "region"
    And I should not see "22 May - 28 May" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should not see "Activity sample 2" in the "1 May - 7 May" "section"
    And I should not see "Activity sample 3" in the "8 May - 14 May" "section"
    And I should not see "Activity sample 4" in the "8 May - 14 May" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Delete several weeks in bulk
    Given I should see "1 May - 7 May" in the "region-main" "region"
    And I should see "8 May - 14 May" in the "region-main" "region"
    And I should see "15 May - 21 May" in the "region-main" "region"
    And I should see "22 May - 28 May" in the "region-main" "region"
    And I should see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should see "Activity sample 2" in the "1 May - 7 May" "section"
    And I should see "Activity sample 3" in the "8 May - 14 May" "section"
    And I should see "Activity sample 4" in the "8 May - 14 May" "section"
    And I click on "Select section 8 May - 14 May" "checkbox"
    And I click on "Select section 15 May - 21 May" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete sections" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete selected sections?" "dialogue"
    Then I should see "1 May - 7 May" in the "region-main" "region"
    And I should see "8 May - 14 May" in the "region-main" "region"
    And I should not see "15 May - 21 May" in the "region-main" "region"
    And I should not see "22 May - 28 May" in the "region-main" "region"
    And I should see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should see "Activity sample 2" in the "1 May - 7 May" "section"
    And I should not see "Activity sample 3" in the "8 May - 14 May" "section"
    And I should not see "Activity sample 4" in the "8 May - 14 May" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk move sections after general section
    Given I set the field "Edit section name" in the "8 May - 14 May" "section" to "Move one"
    And I set the field "Edit section name" in the "15 May - 21 May" "section" to "Move two"
    And I click on "Select section Move one" "checkbox"
    And I click on "Select section Move two" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "General" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the weeks.
    Then I should see "Activity sample 1" in the "15 May - 21 May" "section"
    And I should see "Activity sample 2" in the "15 May - 21 May" "section"
    And I should see "Activity sample 3" in the "Move one" "section"
    And I should see "Activity sample 4" in the "Move one" "section"
    # Check new section order.
    And "Move one" "section" should appear after "General" "section"
    And "Move two" "section" should appear after "Move one" "section"
    And "15 May - 21 May" "section" should appear after "Move two" "section"
    And "22 May - 28 May" "section" should appear after "15 May - 21 May" "section"

  Scenario: Bulk move sections at the end of the course
    Given I set the field "Edit section name" in the "15 May - 21 May" "section" to "Move me"
    And I click on "Select section 8 May - 14 May" "checkbox"
    And I click on "Select section Move me" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "22 May - 28 May" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the weeks.
    Then I should see "Activity sample 1" in the "1 May - 7 May" "section"
    And I should see "Activity sample 2" in the "1 May - 7 May" "section"
    And I should see "Activity sample 3" in the "15 May - 21 May" "section"
    And I should see "Activity sample 4" in the "15 May - 21 May" "section"
    # Check new section order.
    And "1 May - 7 May" "section" should appear after "General" "section"
    And "8 May - 14 May" "section" should appear after "1 May - 7 May" "section"
    And "15 May - 21 May" "section" should appear after "8 May - 14 May" "section"
    And "Move me" "section" should appear after "15 May - 21 May" "section"

  Scenario: Bulk move sections in the middle of the course
    Given I set the field "Edit section name" in the "22 May - 28 May" "section" to "Move me"
    And I click on "Select section 1 May - 7 May" "checkbox"
    And I click on "Select section Move me" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "8 May - 14 May" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the weeks.
    Then I should see "Activity sample 1" in the "8 May - 14 May" "section"
    And I should see "Activity sample 2" in the "8 May - 14 May" "section"
    And I should see "Activity sample 3" in the "1 May - 7 May" "section"
    And I should see "Activity sample 4" in the "1 May - 7 May" "section"
    # Check new section order.
    And "1 May - 7 May" "section" should appear after "General" "section"
    And "8 May - 14 May" "section" should appear after "1 May - 7 May" "section"
    And "Move me" "section" should appear after "8 May - 14 May" "section"
    And "22 May - 28 May" "section" should appear after "Move me" "section"
