@eWallah @availability @availability_relativedate
Feature: availability relative enrol start date
  In order to control student access to activities
  As a teacher
  I need to set date conditions which prevent student access
  Based on enrol start date

  Background:
    Given the following "courses" exist:
      | fullname  | shortname | category | format | startdate         | enddate           | enablecompletion |
      | Course 1  | C1        | 0        | topics | ##-10 days noon## | ##+10 days noon## | 1                |
    And selfenrolment exists in course "C1" starting " ##-2 days 17:00## "
    And selfenrolment exists in course "C1" ending " ##+10 days noon## "
    And the following "activities" exist:
      | activity   | name   | intro | course | idnumber    | section | visible |
      | page       | Page A | intro | C1     | pageA       | 1       | 1       |
    And the following "users" exist:
      | username | timezone |
      | teacher1 | 5        |
      | student1 | 5        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario Outline: Test enrol date condition
    When I am on the "pageA" "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "3"
    And I set the field "relativednw" to <relweek>
    And I set the field "relativestart" to <relstart>
    And I press "Save and return to course"
    And I should see "Not available unless" in the "region-main" "region"
    And I should see "3 <text> after <end>" in the "region-main" "region"
    And I log out

    # Log in as student1.
    When I am on the "C1" "Course" page logged in as "student1"
    # Only real dates.
    Then I should <nau1> "3 <text> after <end>" in the "region-main" "region"

    And I should see "Page A" in the "region-main" "region"
    And I should <nau2> "Not available unless" in the "region-main" "region"

    Examples:
      | relstart | relweek | text   | end                       | nau1    | nau2 |
      # 3 days after enrolment start = + 3
      | "3"      | "2"     | days   | user enrolment date       | not see | see  |
      # 3 days after enrolment ending = -5 + 3 = -2
      | "4"      | "2"     | days   | enrolment method end date | see     | see  |
