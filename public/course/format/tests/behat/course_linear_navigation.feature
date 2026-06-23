@core @core_course @core_courseformat @format_topics @format_weeks @format_singleactivity
Feature: Display the course linear navigation
  In order to quickly navigate through the course activities in a linear way
  As a user
  I want to see the course linear navigation when the course format supports it and it is enabled in the course settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | s1       | Student   | 1        |
      | t1       | Teacher   | 1        |

  @javascript
  Scenario Outline: As a user I should see the course linear navigation when format allows it and it is enabled
    Given the following "courses" exist:
      | fullname | shortname | format    | enablelinearnav |
      | Course1  | C1        | <format>  | <linearnav>     |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | s1      | C1     | student        |
      | t1      | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name   | course |
      | page     | Page1  | C1     |
    When I am on the "Page1" "page activity" page logged in as "<user>"
    Then the course linear navigation <shouldbevisible>

    Examples:
      | format         | linearnav | user | shouldbevisible       |
      | topics         | 1         | s1   | should be visible     |
      | topics         | 1         | t1   | should be visible     |
      | topics         | 0         | s1   | should not be visible |
      | topics         | 0         | t1   | should not be visible |
      | weeks          | 1         | s1   | should be visible     |
      | weeks          | 1         | t1   | should be visible     |
      | weeks          | 0         | s1   | should not be visible |
      | weeks          | 0         | t1   | should not be visible |
      | singleactivity | 1         | s1   | should not be visible |
      | singleactivity | 1         | t1   | should not be visible |

  @javascript
  Scenario Outline: Clicking Next navigates to the next activity
    Given the following "courses" exist:
      | fullname | shortname | format   | enablelinearnav | numsections |
      | Course1  | C1        | <format> | 1               | 0           |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course |
      | page     | Page1 | C1     |
      | page     | Page2 | C1     |
    When I am on the "Page1" "page activity" page logged in as "s1"
    And I click on "Next" "link" in the "sticky-footer" "region"
    Then I should see "Page2" in the "page-header" "region"
    # The last activity in the course should redirect to the course page.
    And I click on "Next" "link" in the "sticky-footer" "region"
    And I should see "Course1" in the "page-header" "region"

    Examples:
      | format |
      | topics |
      | weeks  |

  @javascript
  Scenario Outline: Clicking Previous navigates to the previous activity
    Given the following "courses" exist:
      | fullname | shortname | format   | enablelinearnav |
      | Course1  | C1        | <format> | 1               |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course |
      | page     | Page1 | C1     |
      | page     | Page2 | C1     |
    When I am on the "Page2" "page activity" page logged in as "s1"
    And I click on "Previous" "link" in the "sticky-footer" "region"
    Then I should see "Page1" in the "page-header" "region"

    Examples:
      | format |
      | topics |
      | weeks  |

  @javascript
  Scenario: The manual completion control is relocated to the sticky footer with linear navigation
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav | enablecompletion |
      | Course1  | C1        | topics | 1               | 1                |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course | completion |
      | page     | Page1 | C1     | 1          |
    When I am on the "Page1" "page activity" page logged in as "s1"
    Then I should see "Mark as done" in the "sticky-footer" "region"
    And I should not see "Mark as done" in the "page-header" "region"
    And I should not see "Done" in the "page-header" "region"
    But I toggle the manual completion state of "Page1"
    And the manual completion button of "Page1" is displayed as "Done"
    And I should see "Done" in the "page-header" "region"
    And I toggle the manual completion state of "Page1"
    And the manual completion button of "Page1" is displayed as "Mark as done"
    # The header indicator is hidden again when the activity is marked as not complete.
    And I should not see "Done" in the "page-header" "region"

  @javascript
  Scenario: The manual completion control stays in the header when linear navigation is disabled
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav | enablecompletion |
      | Course1  | C1        | topics | 0               | 1                |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course | completion |
      | page     | Page1 | C1     | 1          |
    When I am on the "Page1" "page activity" page logged in as "s1"
    Then I should see "Mark as done" in the "page-header" "region"
    And the course linear navigation should not be visible

  @javascript
  Scenario: The automatic completion status is shown in the sticky footer with linear navigation
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav | enablecompletion |
      | Course1  | C1        | topics | 1               | 1                |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
    And the following "activities" exist:
      | activity | name  | course | completion | completionview |
      | page     | Page1 | C1     | 2          | 1              |
    When I am on the "Page1" "page activity" page logged in as "s1"
    Then I should see "Done" in the "sticky-footer" "region"
    And I should see "Done" in the "page-header" "region"
