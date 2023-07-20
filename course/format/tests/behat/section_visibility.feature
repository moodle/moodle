@core @core_courseformat
Feature: Varify section visibility interface
  In order to edit the course sections visibility
  As a teacher
  I need to be able to see the updateds visibility information

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on

  @javascript
  Scenario: Activities available but not shown on course page only apply to hidden sections.
    Given I hide section "1"
    And I open "Activity sample 1" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    And I should see "Available but not shown on course page" in the "Activity sample 1" "activity"
    When I show section "1"
    Then I should not see "Available but not shown on course page" in the "Activity sample 1" "activity"

  @javascript
  Scenario: Hide a section also hides the activities.
    When I hide section "1"
    Then I should see "Hidden from students" in the "Topic 1" "section"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I show section "1"
    And I should not see "Hidden from students" in the "Topic 1" "section"
    And I should not see "Hidden from students" in the "Activity sample 1" "activity"

  @javascript
  Scenario: Hiden activities in hidden sections stay hidden when the section is shown.
    Given I open "Activity sample 1" actions menu
    And I choose "Availability > Hide on course page" in the open action menu
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I hide section "1"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    When I show section "1"
    Then I should see "Hidden from students" in the "Activity sample 1" "activity"
