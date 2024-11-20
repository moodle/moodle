@mod @mod_scorm
Feature: Scorm display options
  In order to set how Scorm is displayed
  As a teacher
  I need to be able to choose from Scorm package display options

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype |
      | Course 1 | C1        | topics         |              |
      | Course 2 | C2        | singleactivity | scorm        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |

  @javascript
  Scenario Outline: Teacher can change to various Scorm package display options
    Given the following "activities" exist:
      | activity | course | name       | packagefilepath                                          | hidetoc | nav              |
      | scorm    | C1     | C1 Scorm 1 | mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip | <toc>   | <shownavigation> |
    And I am on the "C1 Scorm 1" "scorm activity" page logged in as teacher1
    When I press "Preview"
    # Confirm TOC display
    # Used css_element to check > and < button display in TOC since similar buttons also exist in navigation
    Then I <tocdisplay> see "Golf Explained - Minimum Run-time Calls"
    And "[title='Show']" "css_element" <showdisplay> exist
    And "[title='Hide']" "css_element" <hidedisplay> exist
    # Confirm TOC dropdown display
    And "scoid" "select" <tocdropdown> exist
    # Confirm the navigation display
    And "[id='scorm_nav']" "css_element" <navbar> exist

    Examples:
      | toc | shownavigation | tocdisplay | showdisplay | hidedisplay | tocdropdown | navbar     |
      | 1   |                | should not | should      | should not  | should not  | should not |
      | 2   | 1              | should     | should      | should not  | should      | should not |
      | 0   | 0              | should     | should not  | should      | should not  | should not |
      | 0   | 1              | should     | should not  | should      | should not  | should     |

  Scenario: Student can exit Scorm activity in single activity course format
    Given the following "activities" exist:
      | activity | course | name       | packagefilepath                                          | popup |
      | scorm    | C2     | C2 Scorm 1 | mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip | 0     |
    And I am on the "C2 Scorm 1" "scorm activity" page logged in as student1
    And I press "Enter"
    When I click on "Exit activity" "link"
    # Confirm that student can exit activity
    Then "Preview" "button" should exist
    And "Enter" "button" should exist
    And "Exit activity" "link" should not exist
    And I should not see "Golf Explained - Minimum Run-time Calls"
