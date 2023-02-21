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
    And I click on "Bulk edit" "button"
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
    And I click on "Section availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
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
    Given I click on "Select section Topic 3" "checkbox"
    And I click on "Section availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
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
    And I click on "Section availability" "button" in the "sticky-footer" "region"
    And I click on "Show on course page" "radio" in the "Availability" "dialogue"
    And I click on "Apply" "button" in the "Availability" "dialogue"
    Then I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Activity sample 3" "activity"
    And I should not see "Hidden from students" in the "Activity sample 4" "activity"
    And I should not see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I should see "Hidden from students" in the "Topic 3" "section"
    And I should not see "Hidden from students" in the "Topic 4" "section"
    And I should see "0 selected" in the "sticky-footer" "region"
