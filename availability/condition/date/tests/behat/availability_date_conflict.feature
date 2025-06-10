@availability @availability_date @javascript
Feature: As a teacher I can set availability dates restriction to an activity and see a warning when conflicting dates are set

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | intro     | introformat | course | content | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | Page 1  | 1             | 1        |

  Scenario: When I set dates to potential conflicting dates in the same subset, I should see a warning.
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1" "availability_date > Date Restriction" to "4"
    And I set the field "Direction" in the "1" "availability_date > Date Restriction" to "from"
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "2" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "2" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "2" "availability_date > Date Restriction" to "6"
    And I set the field "Direction" in the "2" "availability_date > Date Restriction" to "until"
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "3" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "3" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "3" "availability_date > Date Restriction" to "6"
    When I set the field "Direction" in the "3" "availability_date > Date Restriction" to "from"
    Then I should see "Conflicts with other date restrictions"

  Scenario: If there are conflicting dates in the same subset, I should not see a warning if condition are separated by "any".
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the "1" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.1" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.1" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.1" "availability_date > Date Restriction" to "4"
    And I set the field "Direction" in the "1.1" "availability_date > Date Restriction" to "from"
    And I click on "Add restriction..." "button" in the "1" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.2" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.2" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.2" "availability_date > Date Restriction" to "6"
    And I set the field "Direction" in the "1.2" "availability_date > Date Restriction" to "until"
    And I click on "Add restriction..." "button" in the "1" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.3" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.3" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.3" "availability_date > Date Restriction" to "6"
    And I set the field "Direction" in the "1.3" "availability_date > Date Restriction" to "from"
    When I set the field "Required restrictions" in the "1" "core_availability > Set Of Restrictions" to "any"
    Then I should not see "Conflicts with other date restrictions"

  Scenario: There should a conflicting availability dates are in the same subset separated by "all".
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    And I expand all fieldsets
    # Root level: Student "must" match the following.
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    # This is the second level: Student "must" match any of the following.
    And I click on "Add restriction..." "button" in the "1" "core_availability > Availability Button Area"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    # And now the third and final level.
    And I click on "Add restriction..." "button" in the "1.1" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.1.1" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.1.1" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.1.1" "availability_date > Date Restriction" to "2"
    And I set the field "Direction" in the "1.1.1" "availability_date > Date Restriction" to "from"
    And I click on "Add restriction..." "button" in the "1.1" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.1.2" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.1.2" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.1.2" "availability_date > Date Restriction" to "3"
    And I set the field "Direction" in the "1.1.2" "availability_date > Date Restriction" to "until"
    # Then add a restriction to the second level.
    And I click on "Add restriction..." "button" in the "1" "core_availability > Availability Button Area"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the "1.2" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.2.1" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.2.1" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.2.1" "availability_date > Date Restriction" to "4"
    And I set the field "Direction" in the "1.2.1" "availability_date > Date Restriction" to "from"
    And I click on "Add restriction..." "button" in the "1.2" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "year" in the "1.2.2" "availability_date > Date Restriction" to "2023"
    And I set the field "Month" in the "1.2.2" "availability_date > Date Restriction" to "April"
    And I set the field "day" in the "1.2.2" "availability_date > Date Restriction" to "3"
    When I set the field "Direction" in the "1.2.2" "availability_date > Date Restriction" to "until"
    # Same subset, we can detect conflicts.
    Then I should see "Conflicts with other date restrictions"
