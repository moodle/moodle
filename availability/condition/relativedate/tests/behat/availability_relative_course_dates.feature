@eWallah @availability @availability_relativedate
Feature: availability relative course enddate
  In order to control student access to activities
  As a teacher
  I need to set date conditions which prevent student access
  Based on course start and end date

  Background:
    Given the following "courses" exist:
      | fullname  | shortname | category | format | startdate          | enddate            | enablecompletion |
      | Course 1  | C1        | 0        | topics | ##-10 days noon ## | ##+10 days noon ## | 1                |
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
  Scenario Outline: Test relative course date condition
    When I am on the "pageA" "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "2"
    And I set the field "relativednw" to <relweek>
    And I set the field "relativestart" to <relstart>
    And I press "Save and return to course"
    And I should see "Not available unless" in the "region-main" "region"
    And I should see "2 <text> <before> course <end> date" in the "region-main" "region"
    And I log out

    # Log in as student 1.
    When I am on the "C1" "Course" page logged in as "student1"
    # Only real dates.
    Then I should not see "2 <text> <before> course <end> date" in the "region-main" "region"

    And I should see "Page A" in the "region-main" "region"
    And I should <nau> "Not available unless" in the "region-main" "region"

    Examples:
      | relstart | relweek | text   | before | end   | nau     |
      # 2 weeks after course start = -10 + 14 = +4
      | "1"      | "4"     | months | after  | start | see     |
      # 2 days after course start = -10 + 2 = -8
      | "1"      | "2"     | days   | after  | start | not see |
      # 2 weeks before course start = -10 - 14 = -14
      | "6"      | "4"     | months | before | start | not see |
      # 2 days before course start = -10 - 12 = -12
      | "6"      | "2"     | days   | before | start | not see |
      # 2 weeks before course end = +10 - 14 = -4
      | "2"      | "4"     | months | before | end   | not see |
      # 2 days before course end = +10 - 2 = 8
      | "2"      | "2"     | days   | before | end   | see     |
      # 2 weeks after course end = +10 + 14 = +24
      | "5"      | "4"     | months | after  | end   | see     |
      # 2 days after course end = +10 + 2 = +12
      | "5"      | "2"     | days   | after  | end   | see     |
