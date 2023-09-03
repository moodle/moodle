@core @core_grades
Feature: Gradebook calculations for natural weights normalisation before the fix 20150619
  In order to make sure the grades are not changed after upgrade
  As a teacher
  I need to be able to freeze gradebook calculations

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And gradebook calculations for the course "C1" are frozen at version "20150619"
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a5 | Test assignment five | Submit something! | Sub category 1 | 20 |
      | assign | C1 | a6 | Test assignment six | Submit something! | Sub category 1 | 10 |
      | assign | C1 | a7 | Test assignment seven | Submit nothing! | Sub category 1 | 15 |
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"

  @javascript
  Scenario: Grade items weights are normalised when all grade item weights are overridden (sum exactly 100). Extra credit is set to zero (before the fix 20150619).
    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "60"
    And I set the field "Weight of Test assignment six" to "40"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "60.000"
    And the field "Weight of Test assignment six" matches value "40.000"
    And the field "Weight of Test assignment seven" matches value "0.0"
    # The weight of "seven" should be 15/30=50% (15 is the maxgrade for "seven" and 30 are max grades for this category (max grade of "five" plus max grade of "six")
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Setting grade items weights is prevented when all grade item weights are overridden (sum over 100). Extra credit is set to zero (before the fix 20150619).
    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "60"
    And I set the field "Weight of Test assignment six" to "50"
    Then I should see "Weight total exceeds 100%." in the "Test assignment five" "table_row"
    And I should see "Weight total exceeds 100%." in the "Test assignment six" "table_row"
    And the field "Weight of Test assignment five" matches value "60.0"
    And the field "Weight of Test assignment six" matches value "50.0"
    And the field "Weight of Test assignment seven" matches value "0.0"
    And I start watching to see if a new page loads
    And I press "Save changes"
    And a new page should not have loaded since I started watching
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    # The weight of "seven" should be 15/30=50% (15 is the maxgrade for "seven" and 30 are max grades for this category (max grade of "five" plus max grade of "six")
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Setting grade items weights is prevented when all grade item weights are overridden (sum under 100). Extra credit is set to zero (before the fix 20150619).
    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "40"
    And I set the field "Weight of Test assignment six" to "30"
    Then I should see "Weight total is less than 100%." in the "Test assignment five" "table_row"
    And I should see "Weight total is less than 100%." in the "Test assignment six" "table_row"
    And the field "Weight of Test assignment five" matches value "40.0"
    And the field "Weight of Test assignment six" matches value "30.0"
    And the field "Weight of Test assignment seven" matches value "0.0"
    And I start watching to see if a new page loads
    And I press "Save changes"
    And a new page should not have loaded since I started watching
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    # The weight of "seven" should be 15/30=50% (15 is the maxgrade for "seven" and 30 are max grades for this category (max grade of "five" plus max grade of "six")
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Grade items weights are normalised when not all grade item weights are overridden. Extra credit is set respectful to non-overridden items (before the fix 20150619).
    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Weight of Test assignment five" to "40"
    And I press "Save changes"
    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "40.00"
    And the field "Weight of Test assignment six" matches value "60.000"
    And the field "Weight of Test assignment seven" matches value "90.0"
    # The weight of "seven" should be 15/30=50% (15 is the maxgrade for "seven" and 30 are max grades for this category (max grade of "five" plus max grade of "six")
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"
