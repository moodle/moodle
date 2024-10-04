@tool @tool_analytics
Feature: Manager can obtain prediction models insights
  In order to view prediction models insights
  As a manager
  I should be able to obtain the prediction models insights

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Student   | One      | s1@example.com |
      | m1       | Manager   | One      | m1@example.com |
      | m2       | Manager   | Two      | m2@example.com |
      | t1       | Teacher   | One      | t1@example.com |
    And the following "role assigns" exist:
      | user | role    | contextlevel | reference |
      | m1   | manager | System       |           |
      | m2   | manager | System       |           |
    # To ensure that course start date is in the future, set course start date to "tomorrow".
    And the following "courses" exist:
      | fullname | shortname | startdate    |
      | Course 1 | C1        | ##tomorrow## |
      | Course 2 | C2        | ##tomorrow## |
      | Course 3 | C3        | ##tomorrow## |
    And the following "course enrolments" exist:
      | user | course | role           |
      | s1   | C1     | student        |
      | s1   | C2     | student        |
      | s1   | C3     | student        |
      | t1   | C3     | editingteacher |
    # Disable "Analytics processes execution via command line only".
    And the following config values are set as admin:
      | onlycli         | 0 | analytics |
      | enableanalytics | 1 |           |
    And a Python Machine Learning backend server is configured
    And I change the Python Machine Learning backend to use external server

  @javascript
  Scenario: Manager can obtain prediction models insights
    Given I log in as "m1"
    And I navigate to "Analytics > Analytics models" in site administration
    And I click on "Actions" "link" in the "Courses at risk of not starting" "table_row"
    And I click on "Execute scheduled analysis" "link" in the "Courses at risk of not starting" "table_row"
    And I press "Continue"
    When I select "All predictions" from the "contextid" singleselect
    # Confirm that only courses without teachers, Course 1 and Course 2, are listed.
    Then "Course 1" "text" should exist
    And "Course 2" "text" should exist
    And "Course 3" "text" should not exist
    And I click on "Select Course 1 for bulk action" "checkbox"
    And I press "Not applicable"
    And I press "Confirm"
    # After m1 marks Course 1 as Not applicable, only Course 2 should be listed in the predictions for manager 1.
    And "Course 1" "text" should not exist
    And "Course 2" "text" should exist
    And I log in as "m2"
    And I click on ".popover-region-notifications" "css_element"
    # Notification of new insight exists.
    And "Upcoming courses have no teachers or students" "text" should exist
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I click on "View insight" "link"
    # Course 1, previously marked as Not applicable by manager 1, is still listed for manager 2.
    And "Course 1" "text" should exist
    And I click on "View details" "link" in the "Course 1" "table_row"
    # Prediction details and indicators are displayed for the selected course.
    And the following should exist in the "insights-list" table:
      | Description |
      | Course 1    |
    And the following should exist in the "prediction-calculations" table:
      |            -1-       | -2- |
      | Teacher availability | No  |
      | Student enrolments   | Yes |
    # Date of prediction analysis execution.
    And "##today##%A, %d %B %Y##" "text" should exist in the "Time predicted" "table_row"
    And I click on "Select Course 1 for bulk action" "checkbox"
    And I press "Accept"
    And I press "Confirm"
    # Verify that you end up on the page listing predictions for 'No teaching'.
    And "Courses at risk of not starting" "text" should exist
    And "The following courses due to start in the upcoming days are at risk of not starting because they don't have teachers or students enrolled." "text" should exist
    And the following should not exist in the "insights-list" table:
      | Description |
      | Course 1    |
