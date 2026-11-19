@core @core_courseformat
Feature: Verify activity visibility interface.
  In order to edit the course activity visibility
  As a teacher
  I need to be able to see the updateds visibility information

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | visible |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1     | sample2  | 1       | 0       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on

  @javascript
  Scenario: The actions menu does not offer show or hide when the activity card has the visibility control.
    Given "Hidden from students" "button" should exist in the "Activity sample 2" "core_courseformat > Activity visibility"
    When I open "Activity sample 2" actions menu
    Then "Edit settings" "link" should exist in the "Activity sample 2" "activity"
    And "Show" "link" should not exist in the "Activity sample 2" "activity"

  @javascript
  Scenario: The actions menu does not offer availability when the activity card has the visibility control.
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And I reload the page
    And "Available on course page" "button" should exist in the "Activity sample 1" "core_courseformat > Activity visibility"
    When I open "Activity sample 1" actions menu
    Then "Edit settings" "link" should exist in the "Activity sample 1" "activity"
    And "Availability" "link" should not exist in the "Activity sample 1" "activity"
    And "Hide" "link" should not exist in the "Activity sample 1" "activity"

  @javascript
  Scenario: Teacher can show an activity using the visibility badge.
    Given "Hidden from students" "button" should exist in the "Activity sample 2" "core_courseformat > Activity visibility"
    When I click on "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility"
    And I click on "Show on course page" "button"
    Then "Hidden from students" "button" should not exist in the "Activity sample 2" "core_courseformat > Activity visibility"

  @javascript
  Scenario: Teacher can make available but not shown an activity using the visibility badge.
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And I reload the page
    When I click on "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility"
    And I click on "Make available but don't show on course page" "button"
    Then "Hidden from students" "button" should not exist in the "Activity sample 2" "core_courseformat > Activity visibility"
    And "Available but not shown on course page" "button" should exist in the "Activity sample 2" "core_courseformat > Activity visibility"

  @javascript
  Scenario: Make available but not shown is available only when stealth activities are enabled.
    Given I click on "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility"
    And "Make available but don't show on course page" "button" should not exist
    When the following config values are set as admin:
      | allowstealth | 1 |
    And I reload the page
    And I click on "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility"
    Then "Make available but don't show on course page" "button" should exist

  @javascript
  Scenario: Teacher can hide a shown activity using the visibility dropdown.
    Given I should see "Available" in the "Activity sample 1" "core_courseformat > Activity visibility"
    When I click on "Available on course page" "button" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I click on "Hide on course page" "button"
    Then I should see "Hidden" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And "Hidden from students" "button" should exist in the "Activity sample 1" "core_courseformat > Activity visibility"

  @javascript
  Scenario: The visibility dropdown remains usable on tablet and mobile.
    Given "Available on course page" "button" in the "Activity sample 1" "core_courseformat > Activity visibility" should be visible
    And "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility" should be visible
    When I change viewport size to "800x1024"
    Then "Available on course page" "button" in the "Activity sample 1" "core_courseformat > Activity visibility" should be visible
    And "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility" should be visible
    And I change viewport size to "480x800"
    And "Available on course page" "button" in the "Activity sample 1" "core_courseformat > Activity visibility" should be visible
    And "Hidden from students" "button" in the "Activity sample 2" "core_courseformat > Activity visibility" should be visible
    # The mobile triggers are icon only.
    And I should not see "Available" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I should not see "Hidden" in the "Activity sample 2" "core_courseformat > Activity visibility"
    And I click on "Available on course page" "button" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I click on "Hide on course page" "button"
    And "Hidden from students" "button" in the "Activity sample 1" "core_courseformat > Activity visibility" should be visible

  @javascript
  Scenario: The visibility labels are only displayed in edit mode.
    Given I should see "Hidden" in the "Activity sample 2" "core_courseformat > Activity visibility"
    When I turn editing mode off
    Then "Activity sample 1" "core_courseformat > Activity visibility" should not exist
    And "Activity sample 2" "core_courseformat > Activity visibility" should exist
    And ".activity-visibility-label" "css_element" should not exist in the "Activity sample 2" "activity"

  Scenario: Hidden and stealth activities without badge content do not show an empty activity badge
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And the following "activities" exist:
      | activity | name          | course | section | visible | visibleoncoursepage |
      | forum    | Forum hidden  | C1     | 2       | 0       | 1                   |
      | forum    | Forum stealth | C1     | 2       | 1       | 0                   |
    And the following "activities" exist:
      | activity | name           | course | section | visible | visibleoncoursepage | showtype |
      | resource | File hidden    | C1     | 2       | 0       | 1                   | 0        |
      | resource | File stealth   | C1     | 2       | 1       | 0                   | 0        |
      | resource | File with type | C1     | 2       | 0       | 1                   | 1        |
    When I am on "Course 1" course homepage
    Then ".activitybadge" "css_element" should not exist in the "Forum hidden" "activity"
    And ".activitybadge" "css_element" should not exist in the "Forum stealth" "activity"
    And ".activitybadge" "css_element" should not exist in the "File hidden" "activity"
    And ".activitybadge" "css_element" should not exist in the "File stealth" "activity"
    And I should see "TXT" in the "File with type" "activity"
    And I turn editing mode off
    And ".activitybadge" "css_element" should not exist in the "Forum hidden" "activity"
    And ".activitybadge" "css_element" should not exist in the "Forum stealth" "activity"
    And ".activitybadge" "css_element" should not exist in the "File hidden" "activity"
    And ".activitybadge" "css_element" should not exist in the "File stealth" "activity"
    And I should see "TXT" in the "File with type" "activity"
