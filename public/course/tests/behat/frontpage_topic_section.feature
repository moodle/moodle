@core @core_course
Feature: Site home activities section
  In order to show a display activities in the frontpage
  As an admin
  I need to edit the frontpage section

  Background:
    Given the following config values are set as admin:
      | numsections | 1 |

  Scenario: Activities should appear in frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    When I log in as "admin"
    And I am on site homepage
    Then I should see "Frontpage assignment" in the "region-main" "region"

  @javascript
  Scenario: Section name does appears in frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I click on "Edit" "link" in the "region-main" "region"
    And I set the field "Section name" to "New section name"
    When I press "Save changes"
    And I should see "New section name" in the "region-main" "region"
    Then I turn editing mode off
    And I should see "New section name" in the "region-main" "region"

  @javascript
  Scenario: Section description appears in the frontpage
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I click on "Edit" "link" in the "region-main" "region"
    And I set the field "Description" to "New section description"
    When I press "Save changes"
    And I should see "New section description" in the "region-main" "region"
    Then I turn editing mode off
    And I should see "New section description" in the "region-main" "region"

  @javascript
  Scenario: Admin can change the activity visibility in the frontpage
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should see "Frontpage assignment" in the "region-main" "region"
    Then I open "Frontpage assignment" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    And I should see "Available but not shown on course page" in the "Frontpage assignment" "core_courseformat > Activity visibility"
    And I open "Frontpage assignment" actions menu
    And I choose "Availability > Show on course page" in the open action menu
    And I should not see "Available but not shown on course page" in the "Frontpage assignment" "activity"
    And I should not see "Hidden from students" in the "Frontpage assignment" "activity"
    And I open "Frontpage assignment" actions menu
    And I choose "Availability > Hide on course page" in the open action menu
    And I should not see "Available but not shown on course page" in the "Frontpage assignment" "activity"
    And I should see "Hidden from students" in the "Frontpage assignment" "core_courseformat > Activity visibility"

  @javascript
  Scenario: Admin can delete an activity in the frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should see "Frontpage assignment" in the "region-main" "region"
    Then I open "Frontpage assignment" actions menu
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete activity?" "dialogue"
    And I should not see "Frontpage assignment" in the "region-main" "region"

  @javascript
  Scenario: Admin can duplicate an activity in the frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should see "Frontpage assignment" in the "region-main" "region"
    Then I open "Frontpage assignment" actions menu
    And I choose "Duplicate" in the open action menu
    And I should see "Frontpage assignment (copy)" in the "region-main" "region"

  @javascript
  Scenario: Admin can move an activity lefts and right in the frontpage
    Given the following "activities" exist:
      | activity | course               | section | name                 | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign0  |
      | assign   | Acceptance test site | 1       | Frontpage assignment | Assignment description | assign1  |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should see "Frontpage assignment" in the "region-main" "region"
    When I open "Frontpage assignment" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
    And I choose "Move right" in the open action menu
    Then I open "Frontpage assignment" actions menu
    And "Move right" "link" should not be visible
    And "Move left" "link" should be visible
    And I choose "Move left" in the open action menu
    And I open "Frontpage assignment" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
