@mod @mod_workshop
Feature: Setting grades to pass via workshop editing form
  In order to define grades to pass
  As a teacher
  I can set them in the workshop settings form, without the need to go to the gradebook

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |

  Scenario: Adding a new workshop with grade to pass field set
    Given the following "activities" exist:
      | activity   | name             | intro                        | course | idnumber    | section |
      | workshop   | Awesome workshop | Grades to pass are set here  | c1     | workshop1   | 1       |
    When I am on the "Awesome workshop" "workshop activity editing" page logged in as teacher1
    And I set the field "Submission grade to pass" to "45"
    And I set the field "Assessment grade to pass" to "10.5"
    And I press "Save and return to course"
    And I am on the "Awesome workshop" "workshop activity editing" page
    Then the field "Submission grade to pass" matches value "45.00"
    And the field "Assessment grade to pass" matches value "10.50"

  @javascript
  Scenario: Grade to pass kept even with submission types without online text (MDL-64862)
    Given I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    When I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name               | Another workshop            |
      | Description                 | Grades to pass are set here |
      | Submission grade to pass    | 42                          |
      | Assessment grade to pass    | 10.1                        |
      | submissiontypetextavailable | 0                           |
    Then I should not see "Adding a new Workshop"
    And I am on the "Another workshop" "workshop activity editing" page
    And the field "Submission grade to pass" matches value "42.00"
    And the field "Assessment grade to pass" matches value "10.10"

  Scenario: Adding a new workshop with grade to pass fields left empty
    Given the following "activities" exist:
      | activity   | name                     | intro                           | course | idnumber    | section |
      | workshop   | Another awesome workshop | No grades to pass are set here  | c1     | workshop1   | 1       |
    When I am on the "Another awesome workshop" "workshop activity editing" page logged in as teacher1
    Then the field "Submission grade to pass" matches value "0.00"
    And the field "Assessment grade to pass" matches value "0.00"

  Scenario: Adding a new workshop with non-numeric value of a grade to pass
    Given the following "activities" exist:
      | activity   | name                     | intro                              | course | idnumber    | section |
      | workshop   | Another awesome workshop | Invalid grade to pass is set here  | c1     | workshop1   | 1       |
    When I am on the "Another awesome workshop" "workshop activity editing" page logged in as teacher1
    And I set the field "Assessment grade to pass" to "You shall not pass!"
    And I press "Save and return to course"
    Then I should see "Updating Workshop in Topic 1"
    And I should see "You must enter a number here"

  Scenario: Adding a new workshop with invalid value of a grade to pass
    Given the following "activities" exist:
      | activity   | name                    | intro                              | course | idnumber   | section |
      | workshop   | Almost awesome workshop | Invalid grade to pass is set here  | c1     | workshop1  | 1       |
    When I am on the "Almost awesome workshop" "workshop activity editing" page logged in as teacher1
    And I set the field "Assessment grade to pass" to "10000000"
    And I press "Save and return to course"
    Then I should see "Updating Workshop in Topic 1"
    And I should see "The grade to pass can not be greater than the maximum possible grade"

  Scenario: Emptying grades to pass fields sets them to zero
    Given the following "activities" exist:
      | activity   | name                   | intro                                     | course | idnumber  | section |
      | workshop   | Super awesome workshop | Grade to pass are set and then unset here | c1     | workshop1 | 1       |
    When I am on the "Super awesome workshop" "workshop activity editing" page logged in as teacher1
    And I set the field "Submission grade to pass" to "59.99"
    And I set the field "Assessment grade to pass" to "0.000"
    And I press "Save and return to course"
    And I should not see "Updating Workshop in Topic 1"
    And I am on the "Super awesome workshop" "workshop activity editing" page
    And the field "Submission grade to pass" matches value "59.99"
    And the field "Assessment grade to pass" matches value "0.00"
    When I set the field "Submission grade to pass" to ""
    And I set the field "Assessment grade to pass" to ""
    And I press "Save and display"
    Then I should not see "Adding a new Workshop"
    And I am on the "Super awesome workshop" "workshop activity editing" page
    And the field "Submission grade to pass" matches value "0.00"
    And the field "Assessment grade to pass" matches value "0.00"
