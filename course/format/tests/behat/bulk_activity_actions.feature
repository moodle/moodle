@core @core_courseformat @core_course @show_editor @javascript
Feature: Bulk course activity actions.
  In order to edit the course activities
  As a teacher
  I need to be able to edit activities in bulk.

  Background:
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 4        |
      | initsections | 1        |
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
    And the following config values are set as admin:
      | allowstealth | 1 |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk hiding activities
    Given I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
    Then I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk showing activities
    Given the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | visible |
      | assign   | Activity sample 5 | Test assignment description | C1     | sample5  | 1       | 0       |
      | assign   | Activity sample 6 | Test assignment description | C1     | sample6  | 2       | 0       |
    And I reload the page
    And I click on "Bulk actions" "button"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "Hidden from students" in the "Activity sample 5" "activity"
    And I should see "Hidden from students" in the "Activity sample 6" "activity"
    And I click on "Select activity Activity sample 4" "checkbox"
    And I click on "Select activity Activity sample 5" "checkbox"
    And I click on "Select activity Activity sample 6" "checkbox"
    And I should see "3 selected" in the "sticky-footer" "region"
    When I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I click on "Show on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
    Then I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "Activity sample 5" "activity"
    And I should not see "Hidden from students" in the "Activity sample 6" "activity"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk stealth is only available if the site has stealth enabled
    Given I click on "Select activity Activity sample 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    And I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I should see "Make available" in the "Availability" "dialogue"
    When the following config values are set as admin:
      | allowstealth | 0 |
    And I reload the page
    And I click on "Bulk actions" "button"
    Then I click on "Select activity Activity sample 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    And I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I should not see "Make available" in the "Availability" "dialogue"

  Scenario: Bulk stealth activities
    Given I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Available but not shown on course page" in the "Activity sample 3" "activity"
    When I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And I click on "Activity availability" "button" in the "sticky-footer" "region"
    And I click on "Make available but don't show on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
    Then I should see "Available but not shown on course page" in the "Activity sample 1" "activity"
    And I should see "Available but not shown on course page" in the "Activity sample 3" "activity"

  Scenario: Bulk duplicate activities
    Given I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Duplicate activities" "button" in the "sticky-footer" "region"
    Then I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 1 (copy)" in the "Section 1" "section"
    And "Activity sample 1 (copy)" "activity" should appear after "Activity sample 1" "activity"
    And I should see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 3 (copy)" in the "Section 2" "section"
    And "Activity sample 3 (copy)" "activity" should appear after "Activity sample 3" "activity"

  Scenario: Bulk delete activities
    Given I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete activities" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete selected activities?" "dialogue"
    Then I should not see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should not see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk move activities after a specific activity
    Given I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move activities" "button" in the "sticky-footer" "region"
    And I click on "Activity sample 2" "link" in the "Move selected activities" "dialogue"
    And I should see "0 selected" in the "sticky-footer" "region"
    # Check activities are moved to the right sections.
    Then I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should see "Activity sample 3" in the "Section 1" "section"
    And I should not see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    # Check new activities order.
    And "Activity sample 1" "activity" should appear after "Activity sample 2" "activity"
    And "Activity sample 3" "activity" should appear after "Activity sample 1" "activity"
    And "Activity sample 4" "activity" should appear after "Activity sample 3" "activity"

  Scenario: Bulk move activities after a specific section header
    Given I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move activities" "button" in the "sticky-footer" "region"
    And I click on "Section 3" "link" in the "Move selected activities" "dialogue"
    And I should see "0 selected" in the "sticky-footer" "region"
    # Check activities are moved to the right sections.
    Then I should see "Activity sample 1" in the "Section 3" "section"
    Then I should not see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I should see "Activity sample 3" in the "Section 3" "section"
    And I should not see "Activity sample 3" in the "Section 2" "section"
    And I should see "Activity sample 4" in the "Section 2" "section"
    # Check new activities order.
    And "Activity sample 4" "activity" should appear after "Activity sample 2" "activity"
    And "Activity sample 1" "activity" should appear after "Activity sample 4" "activity"
    And "Activity sample 3" "activity" should appear after "Activity sample 1" "activity"
