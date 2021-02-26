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

  Scenario: Completion condition displaying for manual and auto completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test choice manual"
    And I should see "Mark as done"
    And I am on "Course 1" course homepage
    When I follow "Test choice auto"
    Then I should see "Make a choice" in the "[data-region=completionrequirements]" "css_element"
    # TODO MDL-70821: Check completion conditions display on course homepage.

  Scenario: Completion condition displaying setting can be disabled at course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    When I set the following fields to these values:
      | Show completion conditions | No |
    And I click on "Save and display" "button"
    And I follow "Test choice auto"
     # Completion conditions are always shown in the module's view page.
    Then I should see "Make a choice" in the "[data-region=completionrequirements]" "css_element"
    And I am on "Course 1" course homepage
    And I follow "Test choice manual"
    And I should see "Mark as done"

  Scenario: Default show completion conditions value in course form when default show completion conditions admin setting is set to No
    Given I log in as "admin"
    And I navigate to "Courses > Course default settings" in site administration
    When I set the following fields to these values:
      | Show completion conditions | No |
    And I click on "Save changes" "button"
    And I navigate to "Courses > Add a new course" in site administration
    Then the field "showcompletionconditions" matches value "No"

  Scenario: Default show completion conditions value in course form when default show completion conditions admin setting is set to Yes
    Given I log in as "admin"
    And I navigate to "Courses > Course default settings" in site administration
    When I set the following fields to these values:
      | Show completion conditions | Yes |
    And I click on "Save changes" "button"
    And I navigate to "Courses > Add a new course" in site administration
    Then the field "showcompletionconditions" matches value "Yes"
