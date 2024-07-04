@core @core_analytics @javascript
Feature: Manage analytics models
  In order to manage analytics models
  As a manager
  I need to create and use a model

  Background:
    # Turn off the course welcome message, so we can easily test other messages.
    Given the following config values are set as admin:
      | onlycli                  | 0 | analytics    |
      | sendcoursewelcomemessage | 0 | enrol_manual |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
      | student0 | Student   | 0        | student0@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
      | student5 | Student   | 5        | student5@example.com |
      | student6 | Student   | 6        | student6@example.com |
    And the following "system role assigns" exist:
      | user     | course               | role      |
      | manager1 | Acceptance test site | manager   |
    And the following "courses" exist:
      | fullname | shortname | category | enddate         | startdate        | enablecompletion |
      | Course 1 | C1        | 0        | ## yesterday ## | ## 2 days ago ## | 1                |
      | Course 2 | C2        | 0        | ## yesterday ## | ## 2 days ago ## | 1                |
      | Course 3 | C3        | 0        | ## tomorrow  ## | ## 2 days ago ## | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           | timeend         | timestart         |
      | teacher1 | C1     | editingteacher | ## 1 day ago ## | ## 2 days ago ##  |
      | student0 | C1     | student        | ## 1 day ago ## | ## 2 days ago ##  |
      | student1 | C1     | student        | ## 1 day ago ## | ## 2 days ago ##  |
      | student2 | C1     | student        | ## 1 day ago ## | ## 2 days ago ##  |
      | teacher1 | C2     | editingteacher | ## 1 day ago ## | ## 2 days ago ##  |
      | student3 | C2     | student        | ## 1 day ago ## | ## 2 days ago ##  |
      | student4 | C2     | student        | ## 1 day ago ## | ## 2 days ago ##  |
      | teacher1 | C3     | editingteacher | 0               | ## 2 days ago ##  |
      | manager1 | C3     | manager        | 0               | ## 2 days ago ##  |
      | student5 | C3     | student        | 0               | ## 2 days ago ##  |
      | student6 | C3     | student        | 0               | ## 2 days ago ##  |
    And the following "activities" exist:
      | activity   | name      | intro   | course | idnumber    | section | completion | completionview |
      | assign     | assign1   | A1 desc | C1     | assign1     | 0       | 2          | 1              |
      | assign     | assign2   | A2 desc | C2     | assign2     | 0       | 2          | 1              |
      | assign     | assign3   | A3 desc | C3     | assign3     | 0       | 2          | 1              |
    And the following "analytics model" exist:
      | target                                   | indicators                                 | timesplitting                               | enabled |
      | \core_course\analytics\target\course_completion | \core\analytics\indicator\any_write_action,\core\analytics\indicator\read_actions |  \core\analytics\time_splitting\single_range | true    |
    And I log in as "manager1"
    And I navigate to "Analytics > Analytics models" in site administration

  Scenario: Create a model
    When I open the action menu in ".top-nav" "css_element"
    And I choose "Create model" in the open action menu
    And I set the field "Enabled" to "Enable"
    And I select "__core_course__analytics__target__course_completion" from the "target" singleselect
    And I set the field "Indicators" to "Read actions amount, Any write action in the course"
    And I select "__core__analytics__time_splitting__single_range" from the "timesplitting" singleselect
    And I press "Save changes"
    Then I should see "No predictions available yet" in the "Students at risk of not meeting the course completion conditions" "table_row"

  Scenario: Evaluate a model
    Given I am on "Course 1" course homepage
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Assignment - assign1 | 1 |
    And I click on "Save changes" "button"
    And I am on "Course 2" course homepage
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Assignment - assign2 | 1 |
    And I click on "Save changes" "button"
    And I am on "Course 3" course homepage
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Assignment - assign3 | 1 |
    And I click on "Save changes" "button"
    And I am on site homepage
    And I navigate to "Analytics > Analytics models" in site administration
    And I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Evaluate" in the open action menu
    And I press "Evaluate"
    And I should see "Evaluate model"
    And I press "Continue"
    # Evaluation log
    And I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Evaluation log" in the open action menu
    And I should see "Configuration"
    And I click on "View" "link"
    And I should see "Log extra info"
    And I click on "Close" "button" in the "Log extra info" "dialogue"
    And I navigate to "Analytics > Analytics models" in site administration
    # Execute scheduled analysis
    And I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Execute scheduled analysis" in the open action menu
    And I should see "Training results"
    And I press "Continue"
    # Check notifications
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Students at risk in Course 3 course"
    When I am on site homepage
    And I navigate to "Analytics > Analytics models" in site administration
    # View predictions
    When I select "C3" from the "contextid" singleselect
    And I click on "View prediction details" "icon" in the "Student 6" "table_row"
    And I should see "Prediction details"
    And I should see "Any write action"
    And I should see "Read actions amount"
    And I click on "Select Student 6 for bulk action" "checkbox" in the "Student 6" "table_row"
    And I click on "Accept" "button"
    And I wait until "Confirm" "button" exists
    And I click on "Confirm" "button" in the "Accept" "dialogue"
    And I click on "View prediction details" "icon" in the "Student 5" "table_row"
    And I click on "Select Student 5 for bulk action" "checkbox" in the "Student 5" "table_row"
    And I click on "Not applicable" "button"
    And I click on "Confirm" "button" in the "Not applicable" "dialogue"
    And I should see "No insights reported"
    # Clear predictions
    When I am on site homepage
    And I navigate to "Analytics > Analytics models" in site administration
    And I should see "No insights reported" in the "Students at risk of not meeting the course completion conditions" "table_row"
    And I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Clear predictions" in the open action menu
    And I press "Clear predictions"
    Then I should see "No predictions available yet" in the "Students at risk of not meeting the course completion conditions" "table_row"

  Scenario: Edit a model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Edit" in the open action menu
    And I click on "Read actions amount" "text" in the ".form-autocomplete-selection" "css_element"
    And I press "Save changes"
    And I should not see "Read actions amount"

  Scenario: Disable a model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Disable" in the open action menu
    Then I should see "Disabled model" in the "Students at risk of not meeting the course completion conditions" "table_row"

  Scenario: Export model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Export" in the open action menu
    And I click on "Actions" "link" in the "Students at risk of not meeting the course completion conditions" "table_row"
    And following "Export" should download a file that:
       | Contains file in zip | model-config.json |

  Scenario: Check invalid site elements
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Invalid site elements" in the open action menu
    Then I should see "Invalid analysable elements"

  Scenario: Delete model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete" "dialogue"
    Then I should not see "Students at risk of not meeting the course completion conditions"
