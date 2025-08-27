@block @block_social_activities @format_social
Feature: Edit activities in social activities block
  In order to use social activities block
  As a teacher
  I need to add and edit activities there

  Background:
    Given I enable "social" "format" plugin
    And the following "course" exists:
      | fullname    | Course 1 |
      | shortname   | C1       |
      | format      | social   |
      | numsections | 0        |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher | One |
      | student1 | Student | One |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Edit name of activity in-place in social activities block
    Given the following "activities" exist:
      | activity | course | name          |
      | forum    | C1     | My forum name |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I set the field "Edit title" in the "My forum name" "activity" to "New forum name"
    Then I should not see "My forum name"
    And I should see "New forum name"
    And I follow "New forum name"
    And I should not see "My forum name"
    And I should see "New forum name"

  @javascript
  Scenario: Activities in social activities block can be made available but not visible on a course page
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | recent_activity | Course       | C1        | course-view-*   | side-pre      |
    And the following "activity" exists:
      | activity | forum         |
      | course   | C1            |
      | name     | My forum name |
      | section  | 0             |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "My forum name" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    Then I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "My forum name" actions menu
    And I choose "Edit settings" in the open action menu
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide on course page"
    And the field "Availability" matches value "Make available but don't show on course page"
    And I press "Save and return to course"
    Then I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    And I turn editing mode off
    And I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    And I log out
    # Student will not see the module on the course page but can access it from other reports and blocks:
    When I am on the "Course 1" course page logged in as student1
    Then I should not see "My forum name" in the "Social activities" "block"
    And I click on "My forum name" "link" in the "Recent activity" "block"
    And I should see "My forum name" in the ".breadcrumb" "css_element"

  @javascript
  Scenario: The move activity modal allow to move activities in the social activities block
    And the following "activities" exist:
      | activity | course | section | name                   |
      | forum    | C1     | 0       | My forum name          |
      | forum    | C1     | 0       | Other forum name       |
      | forum    | C1     | 0       | Yet another forum name |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "My forum name" in the "Social activities" "block"
    And I should see "Other forum name" in the "Social activities" "block"
    And I should see "Yet another forum name" in the "Social activities" "block"
    And "Other forum name" "activity" should appear after "My forum name" "activity"
    And "Yet another forum name" "activity" should appear after "Other forum name" "activity"
    When I open "My forum name" actions menu
    And I click on "Move" "link" in the "My forum name" activity
    And I should see "My forum name" in the "Move activity" "dialogue"
    And I should see "Other forum name" in the "Move activity" "dialogue"
    And I should see "Yet another forum name" in the "Move activity" "dialogue"
    And I should see "Social activities" in the "Move activity" "dialogue"
    And I click on "Yet another forum name" "link" in the "Move activity" "dialogue"
    Then I should see "My forum name" in the "Social activities" "block"
    And I should see "Other forum name" in the "Social activities" "block"
    And I should see "Yet another forum name" in the "Social activities" "block"
    And "Yet another forum name" "activity" should appear after "Other forum name" "activity"
    And "My forum name" "activity" should appear after "Yet another forum name" "activity"

  @javascript
  Scenario: Teacher can delete an activity in the social activities block
    Given the following "activity" exists:
      | activity | forum         |
      | course   | C1            |
      | name     | My forum name |
      | section  | 0             |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "My forum name" actions menu
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete activity?" "dialogue"
    Then I should not see "My forum name" in the "Social activities" "block"

  @javascript
  Scenario: Teacher can duplicate an activity in the social activities block
    Given the following "activity" exists:
      | activity | forum         |
      | course   | C1            |
      | name     | My forum name |
      | section  | 0             |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "My forum name" actions menu
    And I choose "Duplicate" in the open action menu
    Then I should see "My forum name" in the "Social activities" "block"
    And I should see "My forum name (copy)" in the "Social activities" "block"
    And "My forum name (copy)" "activity" should appear after "My forum name" "activity"

  @javascript
  Scenario: Teacher can move right and left an activity in the social activities block
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "Social forum" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
    And I choose "Move right" in the open action menu
    Then I open "Social forum" actions menu
    And "Move right" "link" should not be visible
    And "Move left" "link" should be visible
    And I choose "Move left" in the open action menu
    And I open "Social forum" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible

  @javascript
  Scenario: Social activities block can have subsections
    Given the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Assignment name |
      | section  | 0               |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add content" "button" in the ".block_social_activities .footer" "css_element"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "New subsection" in the "Social activities" "block"
    And I open "Assignment name" actions menu
    And I click on "Move" "link" in the "Assignment name" activity
    And I click on "New subsection" "link" in the "Move activity" "dialogue"
    Then  I should see "Assignment name" in the "New subsection" "section"
