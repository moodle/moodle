@mod @mod_h5pactivity @core_5hp
Feature: Control H5P activity availability for students
  In order to restrict student access to H5P activity
  As a teacher
  I need to control the availability of the H5P activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity    | course | name     |
      | h5pactivity | C1     | H5P Test |

  @javascript
  Scenario Outline: Restrict H5P activity access by date
    Given I am on the "H5P Test" "h5pactivity activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | Direction | from   |
      | x[day]    | 1      |
      | x[month]  | 1      |
      | x[year]   | <year> |
    And I press "Save and return to course"
    When I am on the "Course 1" course page logged in as student1
    Then I <fromvisibility> see "Available from"

    Examples:
      | year                | fromvisibility |
      | ## -1 year ## %Y ## | should not     |
      | ## +1 year ## %Y ## | should         |
