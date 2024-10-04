@core @core_course
Feature: Courses can be set up to display dates relative to the user's enrolment date
  As a course creator
  In order for me to set up courses
  I need to be able to set up courses to display dates relative to the user's enrolment date

  @javascript
  Scenario: Create a course with relative dates feature disabled
    Given the following config values are set as admin:
      | enablecourserelativedates | 0 |
    And  I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I press "Add a new course"
    And I wait until the page is ready
    Then I should not see "Relative dates mode"
    And I should not see "This cannot be changed once the course has been created."

  @javascript
  Scenario: Create a course with relative dates feature enabled
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I press "Add a new course"
    Then I should see "Relative dates mode"
    And I should see "Relative dates mode cannot be changed once the course has been created."

  Scenario: Edit courses with relative dates feature enabled
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And  I log in as "admin"
    And I create a course with:
      | Course full name    | Course 1  |
      | Course short name   | C1        |
      | Relative dates mode | Yes       |
    And I create a course with:
      | Course full name    | Course 2  |
      | Course short name   | C2        |
      | Relative dates mode | No        |
    And I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    Then the "Relative dates mode" "select" should be disabled
    And the field "Relative dates mode" matches value "Yes"
    And I am on "Course 2" course homepage
    And I navigate to "Settings" in current page administration
    And the "Relative dates mode" "select" should be disabled
    And the field "Relative dates mode" matches value "No"
