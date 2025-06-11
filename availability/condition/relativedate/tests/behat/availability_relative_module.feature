@eWallah @availability @availability_relativedate
Feature: availability_relativedate relative activities
  In order to control student access to activities
  As a teacher
  I need to set relative activity conditions which prevent student access

  Background:
    Given the following "users" exist:
      | username |
      | student1 |
    And the following config values are set as admin:
      | enableavailability       | 1 |        |
      | backup_import_activities | 0 | backup |
      | enableasyncbackup        | 0 |        |

    And the following "course" exists:
      | fullname          | Course 1             |
      | shortname         | C1                   |
      | category          | 0                    |
      | enablecompletion  | 1                    |
      | startdate         | ## -10 days 17:00 ## |
      | enddate           | ## +2 weeks 17:00 ## |
    And the following "course enrolments" exist:
      | user     | course | role     |
      | student1 | C1     | student  |
    And the following "activities" exist:
      | activity   | name    | course | idnumber | section | completion |
      | page       | Page A1 | C1     | pageA1   | 1       | 1          |
      | page       | Page A2 | C1     | pageA2   | 1       | 1          |
      | page       | Page A3 | C1     | pageA3   | 1       | 1          |
      | page       | Page A4 | C1     | pageA4   | 1       | 1          |
      | page       | Page A5 | C1     | pageA5   | 1       | 1          |
      | page       | Page B1 | C1     | pageB1   | 2       | 1          |
      | page       | Page B2 | C1     | pageB2   | 2       | 1          |
      | page       | Page B3 | C1     | pageB3   | 2       | 1          |
      | page       | Page B4 | C1     | pageB4   | 2       | 1          |
      | page       | Page B5 | C1     | pageB5   | 2       | 1          |
      | page       | Page C1 | C1     | pageC1   | 3       | 1          |
      | page       | Page C2 | C1     | pageC2   | 3       | 1          |
      | page       | Page C3 | C1     | pageC3   | 3       | 1          |
      | page       | Page C4 | C1     | pageC4   | 3       | 1          |
      | page       | Page C5 | C1     | pageC5   | 3       | 1          |
      | page       | Page D1 | C1     | pageD1   | 4       | 1          |
      | page       | Page D2 | C1     | pageD2   | 4       | 1          |
      | page       | Page D3 | C1     | pageD3   | 4       | 1          |
      | page       | Page D4 | C1     | pageD4   | 4       | 1          |
      | page       | Page D5 | C1     | pageD5   | 4       | 1          |
      | page       | Page E1 | C1     | pageE1   | 5       | 1          |
      | page       | Page E2 | C1     | pageE2   | 5       | 1          |
      | page       | Page E3 | C1     | pageE3   | 5       | 1          |
      | page       | Page E4 | C1     | pageE4   | 5       | 1          |
      | page       | Page E5 | C1     | pageE5   | 5       | 1          |
    And I make "pageA2" relative date depending on "pageA1"
    And I make "pageA3" relative date depending on "pageA2"
    And I make "pageA4" relative date depending on "pageA3"
    And I make "pageA5" relative date depending on "pageA4"
    And I make "pageB2" relative date depending on "pageB1"
    And I make "pageB3" relative date depending on "pageB2"
    And I make "pageB4" relative date depending on "pageB3"
    And I make "pageB5" relative date depending on "pageB4"
    And I make "pageC2" relative date depending on "pageC1"
    And I make "pageC3" relative date depending on "pageC2"
    And I make "pageC4" relative date depending on "pageC3"
    And I make "pageC5" relative date depending on "pageC4"
    And I make "pageD2" relative date depending on "pageD1"
    And I make "pageD3" relative date depending on "pageD2"
    And I make "pageD4" relative date depending on "pageD3"
    And I make "pageD5" relative date depending on "pageD4"
    And I make "pageE2" relative date depending on "pageE1"
    And I make "pageE3" relative date depending on "pageE2"
    And I make "pageE4" relative date depending on "pageE3"
    And I make "pageE5" relative date depending on "pageE4"
    And I log in as "admin"

  @javascript
  Scenario: Admin should see relative session restrictions
    When I navigate to "Development > Purge caches" in site administration
    And I press "Purge all caches"
    And I am on "Course 1" course homepage
    Then I should see "Not available unless: (1 hour after completion of activity"

  @javascript
  Scenario: Student should see relative session restrictions
    When I navigate to "Development > Purge caches" in site administration
    And I press "Purge all caches"
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Page A1" in the "region-main" "region"
    And I should see "1 hour after completion of activity Page A1"
    And I press "Mark as done"
    And I reload the page
    But I should not see "1 hour after completion of activity Page A1"
    And I log out
    And I trigger cron
    And I am on the "C1" "Course" page logged in as "student1"
    And I should see "1 hour after completion of activity" in the "region-main" "region"
    And I should see relativedate "## +1 hours ##"

  Scenario: Admin can duplicate a restricted activity
    When I am on "Course 1" course homepage with editing mode on
    And I duplicate "Page A2" activity
    And I duplicate "Page B2" activity
    Then I should see "Not available unless: (1 hour after completion of activity"
    And I should see "Page A2 (copy)"
    And I should see "Page B2 (copy)"

  @javascript
  Scenario: Admin can backup and restore a course with relative restricted activities
    When I am on "Course 1" course homepage
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on "Course 2" course homepage
    Then I should see "Not available unless: (1 hour after completion of activity"

  @javascript
  Scenario: Admin can backup and restore a module with relative restricted activities
    When I am on "Course 1" course homepage
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on "Course 2" course homepage
    Then I should see "Not available unless: (1 hour after completion of activity"

  Scenario: Admin can delete relatativedate restricted activities
    When I am on "Course 1" course homepage with editing mode on
    And I delete "Page A1" activity
    And I delete "Page B2" activity
    And I delete "Page C3" activity
    And I delete "Page D4" activity
    And I delete "Page E5" activity
    And I delete section "5"
    And I reload the page
    And I run all adhoc tasks
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Page A2" in the "region-main" "region"
    And I should see "1 hour after completion of activity (missing)"
    And I should not see "1 hour after completion of activity Page A1"

  @javascript
  Scenario: Admin can delete relatativedate restricted sections
    When I am on "Course 1" course homepage with editing mode on
    And I edit the section "2"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "1"
    And I set the field "relativednw" to "1"
    And I set the field "relativestart" to "7"
    And I set the field "relativecoursemodule" to "Page A1"
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I delete "Page A1" activity
    And I reload the page
    And I run all adhoc tasks
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Page A2" in the "region-main" "region"
    And I should see "1 hour after completion of activity (missing)"
    And I should see "1 hour after completion of activity Page E1"
