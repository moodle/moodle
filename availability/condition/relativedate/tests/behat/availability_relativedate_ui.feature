@eWallah @availability @availability_relativedate @javascript
Feature: availability_relativedate ui
  As an admin
  I also need to be able to use relative dates

  Background:
    Given the following config values are set as admin:
      | enableavailability   | 1 |
    And the following "course" exists:
      | fullname          | Course 1             |
      | shortname         | C1                   |
      | category          | 0                    |
      | enablecompletion  | 1                    |
      | startdate         | ## -10 days 17:00 ## |
      | enddate           | ## +2 weeks 17:00 ## |
    And the following "activities" exist:
      | activity   | name   | intro | course | idnumber    | section | visible | completion |
      | page       | Page A | intro | C1     | pageA       | 1       | 1       | 1          |
      | page       | Page B | intro | C1     | pageB       | 1       | 1       | 1          |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And selfenrolment exists in course "C1" ending "## tomorrow 17:00 ##"

  Scenario Outline: Add relative condition ui
    Given I am on the "pageA" "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to <number>
    And I set the field "relativednw" to <dmw>
    And I set the field "relativestart" to <relstart>
    And I press "Save and return to course"
    Then I should see "<cond>"

    Examples:
      | number | dmw | relstart | cond |
      | "1"    | "0" | "1"      | 1 minute after course start date   |
      | "2"    | "0" | "1"      | 2 minutes after course start date  |
      | "3"    | "1" | "2"      | 3 hours before course end date     |
      | "4"    | "2" | "3"      | 4 days after user enrolment date   |
      | "5"    | "3" | "4"      | 5 weeks after enrolment method end |
      | "6"    | "4" | "5"      | 6 months after course end date     |
      | "7"    | "4" | "6"      | 7 months before course start date  |

  Scenario: Add relative condition ui to a section
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "1"
    And I set the field "relativednw" to "1"
    And I set the field "relativestart" to "7"
    And I set the field "relativecoursemodule" to "Page A"
    When I press "Save changes"
    Then I should see "1 hour after completion of"

  Scenario: Add relative condition ui with a module
    Given I am on the "pageB" "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "1"
    And I set the field "relativednw" to "2"
    And I set the field "relativestart" to "7"
    And I set the field "relativecoursemodule" to "Page A"
    When I press "Save and return to course"
    Then I should see "1 day after completion of activity Page A"
