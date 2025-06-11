@eWallah @availability @availability_relativedate @javascript
Feature: availability_relativedate ui none
  As an admin
  I cannot use relative modules if none is enabled

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
      | page       | Page A | intro | C1     | pageA       | 1       | 1       | 0          |
      | page       | Page B | intro | C1     | pageB       | 1       | 1       | 0          |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Relative condition ui to a section when no completions
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    Then I should see "after course start date"
    But I should not see "after completion of activity"

  Scenario: Relative condition ui with a module when no completions
    Given I am on the "pageB" "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    Then I should see "after course start date"
    But I should not see "after completion of activity"
