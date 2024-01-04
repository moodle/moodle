@core @core_courseformat @core_course @show_editor @javascript
Feature: Bulk course section actions.
  In order to edit the course section
  As a teacher
  I need to be able to edit sections in bulk.

  Background:
    Given the following "course" exists:
      | fullname    | Course 1 |
      | shortname   | C1       |
      | category    | 0        |
      | numsections | 4        |
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

  Scenario: Bulk hide sections
    Given I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I should not see "Hidden from students" in the "Topic 3" "section"
    And I should not see "Hidden from students" in the "Topic 4" "section"
    When I click on "Select section Topic 1" "checkbox"
    And I click on "Select section Topic 2" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    Then I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should see "Hidden from students" in the "Activity sample 2" "activity"
    And I should see "Hidden from students" in the "Activity sample 3" "activity"
    And I should see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "Hidden from students" in the "Topic 1" "section"
    And I should see "Hidden from students" in the "Topic 2" "section"
    And I should not see "Hidden from students" in the "Topic 3" "section"
    And I should not see "Hidden from students" in the "Topic 4" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk show sections
    Given I click on "Select section Topic 1" "checkbox"
    And I click on "Select section Topic 3" "checkbox"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I should see "Hidden from students" in the "Topic 3" "section"
    And I should not see "Hidden from students" in the "Topic 4" "section"
    When I click on "Select section Topic 1" "checkbox"
    And I click on "Select section Topic 2" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And I click on "Sections availability" "button" in the "sticky-footer" "region"
    And I click on "Show on course page" "radio" in the "Sections availability" "dialogue"
    And I click on "Apply" "button" in the "Sections availability" "dialogue"
    Then I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I should see "Hidden from students" in the "Topic 3" "section"
    And I should not see "Hidden from students" in the "Topic 4" "section"

  Scenario: Bulk delete sections with content ask for confirmation
    Given I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should see "Topic 3" in the "region-main" "region"
    And I should see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 2" "section"
    And I should see "Activity sample 4" in the "Topic 2" "section"
    And I click on "Select section Topic 1" "checkbox"
    And I click on "Select section Topic 2" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete sections" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete selected sections?" "dialogue"
    Then I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should not see "Topic 3" in the "region-main" "region"
    And I should not see "Topic 4" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "Topic 1" "section"
    And I should not see "Activity sample 2" in the "Topic 1" "section"
    And I should not see "Activity sample 3" in the "Topic 2" "section"
    And I should not see "Activity sample 4" in the "Topic 2" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk delete sections without content does not ask for confirmation
    Given I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should see "Topic 3" in the "region-main" "region"
    And I should see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 2" "section"
    And I should see "Activity sample 4" in the "Topic 2" "section"
    And I click on "Select section Topic 3" "checkbox"
    And I click on "Select section Topic 4" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete sections" "button" in the "sticky-footer" "region"
    Then I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should not see "Topic 3" in the "region-main" "region"
    And I should not see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 2" "section"
    And I should see "Activity sample 4" in the "Topic 2" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk delete both section with content and empty section ask for confirmation
    Given I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should see "Topic 3" in the "region-main" "region"
    And I should see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 2" "section"
    And I should see "Activity sample 4" in the "Topic 2" "section"
    And I click on "Select section Topic 2" "checkbox"
    And I click on "Select section Topic 3" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Delete sections" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete selected sections?" "dialogue"
    Then I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should not see "Topic 3" in the "region-main" "region"
    And I should not see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should not see "Activity sample 3" in the "Topic 2" "section"
    And I should not see "Activity sample 4" in the "Topic 2" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk move sections after general section
    Given I set the field "Edit section name" in the "Topic 2" "section" to "Move one"
    And I set the field "Edit section name" in the "Topic 3" "section" to "Move two"
    And I click on "Select section Move one" "checkbox"
    And I click on "Select section Move two" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "General" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the topics.
    Then I should see "Activity sample 1" in the "Topic 3" "section"
    And I should see "Activity sample 2" in the "Topic 3" "section"
    And I should see "Activity sample 3" in the "Move one" "section"
    And I should see "Activity sample 4" in the "Move one" "section"
    # Check new section order.
    And "Move one" "section" should appear after "General" "section"
    And "Move two" "section" should appear after "Move one" "section"
    And "Topic 3" "section" should appear after "Move two" "section"
    And "Topic 4" "section" should appear after "Topic 3" "section"

  Scenario: Bulk move sections at the end of the course
    Given I set the field "Edit section name" in the "Topic 3" "section" to "Move me"
    And I click on "Select section Topic 2" "checkbox"
    And I click on "Select section Move me" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "Topic 4" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the topics.
    Then I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 3" "section"
    And I should see "Activity sample 4" in the "Topic 3" "section"
    # Check new section order.
    And "Topic 1" "section" should appear after "General" "section"
    And "Topic 2" "section" should appear after "Topic 1" "section"
    And "Topic 3" "section" should appear after "Topic 2" "section"
    And "Move me" "section" should appear after "Topic 3" "section"

  Scenario: Bulk move sections in the middle of the course
    Given I set the field "Edit section name" in the "Topic 4" "section" to "Move me"
    And I click on "Select section Topic 1" "checkbox"
    And I click on "Select section Move me" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move sections" "button" in the "sticky-footer" "region"
    And I click on "Topic 2" "link" in the "Move selected sections" "dialogue"
    # Check activities are moved with the topics.
    Then I should see "Activity sample 1" in the "Topic 2" "section"
    And I should see "Activity sample 2" in the "Topic 2" "section"
    And I should see "Activity sample 3" in the "Topic 1" "section"
    And I should see "Activity sample 4" in the "Topic 1" "section"
    # Check new section order.
    And "Topic 1" "section" should appear after "General" "section"
    And "Topic 2" "section" should appear after "Topic 1" "section"
    And "Move me" "section" should appear after "Topic 2" "section"
    And "Topic 4" "section" should appear after "Move me" "section"
