@core @core_courseformat
Feature: Validate some activity editing has a non-ajax alternative
  In order to edit the course activities faster
  As a teacher
  I need to be able use some edit tools without ajax.

  Background:
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 3        |
      | initsections | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Activity settings can be accessed without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Edit settings" "link" in the "Activity sample 1" "activity"
    Then I should see "Assignment name"
    And I set the field "Assignment name" to "New name"
    And I press "Save and return to course"
    And I should see "New name"

  Scenario: Indent an activity can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should not see "Move left"
    When I click on "Move right" "link" in the "Activity sample 1" "activity"
    Then I should see "Move left"
    And I should not see "Move right"
    And I click on "Move left" "link" in the "Activity sample 1" "activity"
    And I should not see "Move left"
    And I should see "Move right"

  Scenario: Hide and show an activity can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should not see "Show" in the ".cm_action_menu" "css_element"
    When I click on "Hide" "link" in the "Activity sample 1" "activity"
    Then I should see "Show" in the ".cm_action_menu" "css_element"
    And I should not see "Hide" in the ".cm_action_menu" "css_element"
    And I click on "Show" "link" in the "Activity sample 1" "activity"
    And I should not see "Show" in the ".cm_action_menu" "css_element"
    And I should see "Hide" in the ".cm_action_menu" "css_element"

  Scenario: Activity visibility with stealth option can be changed without ajax
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "Show on course page"
    And I should see "Hide on course page"
    And I should see "Make available but don't show on course page"
    And ".activity-badges" "css_element" should not exist
    When I click on "Hide on course page" "link" in the "Activity sample 1" "activity"
    Then I should see "Hidden from students" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I should not see "Available but not shown on course page" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I click on "Make available but don't show on course page" "link" in the "Activity sample 1" "activity"
    And I should not see "Hidden from students" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I should see "Available but not shown on course page" in the "Activity sample 1" "core_courseformat > Activity visibility"
    And I click on "Show on course page" "link" in the "Activity sample 1" "activity"
    And ".activity-badges" "css_element" should not exist

  Scenario: Duplicate activity can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Duplicate" "link" in the "Activity sample 1" "activity"
    Then I should see "Activity sample 1 (copy)"

  Scenario: Delete activity can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Delete" "link" in the "Activity sample 1" "activity"
    And I should see "Delete activity?"
    And I should see "This will delete Activity sample 1 and any user data it contains"
    And I click on "Delete" "button"
    Then I should not see "Activity sample 1"

  Scenario: The activity groupmode can be changed without ajax
    Given the following "groups" exist:
      | name | course | idnumber |
      | G1   | C1     | GI1      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And "No groups" "icon" should exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 1" "activity"
    When I click on "Separate groups" "link" in the "Activity sample 1" "activity"
    And "No groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should exist in the "Activity sample 1" "activity"
    And I click on "Visible groups" "link" in the "Activity sample 1" "activity"
    And "No groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 1" "activity"
    And I click on "No groups" "link" in the "Activity sample 1" "activity"
    And "No groups" "icon" should exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 1" "activity"
