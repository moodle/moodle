@tool @tool_behat
Feature: Transform date time string arguments
  In order to write tests with relative date and time
  As a user
  I need to apply some transformations to the steps arguments

  Scenario: Set date in table and check date with specific format
    Given I am on site homepage
    And the following "users" exist:
      | username  | firstname | lastname |
      | teacher1  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate       |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | ##yesterday## |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "##yesterday##l, j F Y##"
    And I log out
