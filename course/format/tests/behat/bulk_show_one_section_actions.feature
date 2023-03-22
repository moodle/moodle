@core @core_courseformat @core_course @show_editor @javascript
Feature: Bulk course section actions one section per page.
  In order to edit the course section in one section per page setting
  As a teacher
  I need to be able to edit sections in bulk in both display modes.

  Background:
    Given the following "course" exists:
      | fullname      | Course 1 |
      | shortname     | C1       |
      | category      | 0        |
      | numsections   | 4        |
      | coursedisplay | 1        |
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

  Scenario: Bulk section edit is only available when multiple sections are displayed
    Given I click on "Select topic Topic 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    # Move to single topic page.
    When I click on "Topic 1" "link" in the "region-main" "region"
    And I click on "Bulk edit" "button"
    Then "Select topic Topic 1" "checkbox" should not exist

  Scenario: Bulk availability sections in one section per page
    Given I should not see "Hidden from students" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 2" "activity"
    And I should not see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I click on "Select topic Topic 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    And I click on "Topics availability" "button" in the "sticky-footer" "region"
    And I click on "Hide on course page" "radio" in the "Topic availability" "dialogue"
    When I click on "Apply" "button" in the "Topic availability" "dialogue"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I should see "Hidden from students" in the "Activity sample 2" "activity"
    And I should see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Topic 2" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk delete sections in one section per page
    Given I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should see "Topic 3" in the "region-main" "region"
    And I should see "Topic 4" in the "region-main" "region"
    And I should see "Activity sample 1" in the "Topic 1" "section"
    And I should see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 2" "section"
    And I should see "Activity sample 4" in the "Topic 2" "section"
    And I click on "Select topic Topic 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    When I click on "Delete topics" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Delete topic?" "dialogue"
    Then I should see "Topic 1" in the "region-main" "region"
    And I should see "Topic 2" in the "region-main" "region"
    And I should see "Topic 3" in the "region-main" "region"
    And I should not see "Topic 4" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "Topic 1" "section"
    And I should not see "Activity sample 2" in the "Topic 1" "section"
    And I should see "Activity sample 3" in the "Topic 1" "section"
    And I should see "Activity sample 4" in the "Topic 1" "section"
    And I should see "0 selected" in the "sticky-footer" "region"

  Scenario: Bulk move topics in one section per page
    Given I set the field "Edit topic name" in the "Topic 2" "section" to "Move one"
    And I set the field "Edit topic name" in the "Topic 3" "section" to "Move two"
    And I click on "Select topic Move one" "checkbox"
    And I click on "Select topic Move two" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    When I click on "Move topics" "button" in the "sticky-footer" "region"
    And I click on "General" "link" in the "Move selected topics" "dialogue"
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
