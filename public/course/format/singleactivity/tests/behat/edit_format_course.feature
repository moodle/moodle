@format @format_singleactivity @block_site_main_menu
Feature: Edit format course to Single Activity format
  In order to set the format course to single activity course
  As a teacher
  I need to edit the course settings and see the dropdown type activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype |
      | Course 1 | C1        | singleactivity | assign       |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | assign     | Assignment 1 | Test assignment description | C1     | assign1   | 0       |
      | forum      | Forum 1      | Test forum description      | C1     | forum1    | 0       |

  @javascript
  Scenario: Add subsections and move activities in Single activity course format
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I click on "Add content" "button" in the ".block_site_main_menu .footer" "css_element"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "New subsection" in the "Additional activities" "block"
    And I open "Forum 1" actions menu
    And I click on "Move" "link" in the "Forum 1" activity
    And I click on "New subsection" "link" in the "Move activity" "dialogue"
    And I should see "Forum 1" in the "New subsection" "section"

  Scenario: Change activity type in Single activity course format
    # Navigate to any course page, like groups, to guarantee that Course settings are displayed in boost and classic themes.
    Given I am on the "Course 1" "groups" page logged in as "teacher1"
    When I navigate to "Settings" in current page administration
    # If there is an existing activity, it will be used as main activity.
    And I set the following fields to these values:
      | Type of activity    | Forum |
    And I press "Save and display"
    Then I should see "Test forum description" in the "page-content" "region"
    # However, if there is no existing activity, the new activity form will be displayed.
    But I am on the "Course 1" "groups" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Type of activity    | Quiz |
    And I press "Save and display"
    And I should see "New Quiz"

  Scenario: Edit a format course as a teacher
    Given the following "courses" exist:
      | fullname | shortname | summary        | format |
      | Course 2 | C2        | Course summary | topics |
    And the following "course enrolments" exist:
      | user     | course | role |
      | teacher1 | C2     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 2" course homepage
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | myfirstcourse   |
      | Format            | Single activity |
    And I press "Update format"
    Then I should see "Forum" in the "Type of activity" "field"
    # Check that not all the activity types are in the dropdown.
    And I should not see "Text and media" in the "Type of activity" "field"
    And I should not see "Subsection" in the "Type of activity" "field"
    And I press "Save and display"
    And I should see "New Forum"
