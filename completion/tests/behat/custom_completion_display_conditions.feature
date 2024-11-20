@core @core_completion
Feature: Allow teachers to edit the visibility of completion conditions in a course
  In order to show students the course completion conditions in a course
  As a teacher
  I need to be able to edit completion conditions settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity  | course | idnumber | name              | completion  | completionsubmit |
      | choice    | C1     | c1m      | Test choice manual| 1           | 0                |
      | choice    | C1     | c1a      | Test choice auto  | 2           | 1                |
  @javascript
  Scenario: Completion condition displaying for manual and auto completion
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    # The manual completion "Mark as done" criteria should displayed in the dropdown in the course homepage.
    Then "Test choice manual" should have the "Mark as done" completion condition
    And I follow "Test choice manual"
    # The manual completion toggle button should be displayed in activity view.
    And the manual completion button for "Test choice manual" should be disabled
    # Automatic completion conditions should be displayed on both activity view page and course homepage if show completion conditions is enabled.
    And I am on "Course 1" course homepage
    And "Test choice auto" should have the "Make a choice" completion condition
    And I follow "Test choice auto"
    And "Test choice auto" should have the "Make a choice" completion condition

  Scenario: Completion condition displaying setting can be disabled at course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    When I set the following fields to these values:
      | Show activity completion conditions | No |
    And I click on "Save and display" "button"
    # Automatic completion conditions should not be displayed on the course homepage if show completion conditions is disabled.
    And there should be no completion information shown for "Test choice auto"
    # Completion conditions are always shown in the module's view page.
    And I follow "Test choice auto"
    Then "Test choice auto" should have the "Make a choice" completion condition
    # The manual completion toggle button should not be displayed in the course homepage when completion is disabled.
    And I am on "Course 1" course homepage
    And the manual completion button for "Test choice manual" should not exist
    # The manual completion toggle button should always be displayed in the activity view.
    And I follow "Test choice manual"
    And the manual completion button for "Test choice manual" should be disabled

  Scenario Outline: Default showcompletionconditions value in course form on course creation
    Given I log in as "admin"
    And I navigate to "Courses > Default settings > Course default settings" in site administration
    And I set the field "Show activity completion conditions" to "<siteshowcompletion>"
    And I press "Save changes"
    When I navigate to "Courses > Add a new course" in site administration
    Then the field "showcompletionconditions" matches value "<expected>"

    Examples:
      | siteshowcompletion | expected |
      | Yes                | Yes      |
      | No                 | No       |

  Scenario Outline: Default showcompletionconditions displayed when editing a course with disabled completion tracking
    Given I log in as "admin"
    And I navigate to "Courses > Default settings > Course default settings" in site administration
    And I set the field "Show activity completion conditions" to "<siteshowcompletion>"
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the field "Enable completion tracking" to "No"
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    Then the field "Show activity completion conditions" matches value "<expected>"

    Examples:
      | siteshowcompletion  | expected  |
      | Yes                 | Yes       |
      | No                  | No        |
