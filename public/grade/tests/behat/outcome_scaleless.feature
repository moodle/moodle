@core_grades @javascript
Feature: Outcomes reports handle outcomes without scales
  In order to use outcomes that are not associated with scales
  As a teacher
  I can view outcomes reports without errors when courses contain both scaled and scale-less outcomes.

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "scales" exist:
      | name         | scale             |
      | Test Scale 1 | Needs work, Good  |
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    And the following "grade outcomes" exist:
      | fullname           | shortname | course | scale        |
      | Outcome with scale | withscale | C1     | Test Scale 1 |
    And the following "grade outcomes" exist:
      | fullname              | shortname | course |
      | Outcome without scale | noscale   | C1     |
    And the following "activities" exist:
      | activity | course | name            |
      | assign   | C1     | Test assignment |

  Scenario: The outcomes report shows only assessed outcomes
    Given I am on the "Test assignment" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    When I set the field "Outcome without scale" to "1"
    And I am on the "Course 1" "grades > Outcomes report > View" page logged in as "teacher1"
    # The outcome with a scale should be shown without error, and the outcome without a scale should not be shown at all
    Then I should see "withscale" in the "generaltable" "table"
    And I should not see "noscale" in the "generaltable" "table"

  Scenario: A teacher can add and remove a scale-less outcome from an activity
    Given I am on the "Test assignment" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    # Associate outcome
    When I set the field "Outcome without scale" to "1"
    And I press "Save and return to course"
    And I am on the "Test assignment" "assign activity editing" page
    And I expand all fieldsets
    Then the field "Outcome without scale" matches value "1"
    # Dissociate outcome
    And I set the field "Outcome without scale" to "0"
    And I press "Save and return to course"
    And I am on the "Test assignment" "assign activity editing" page
    And I expand all fieldsets
    And the field "Outcome without scale" matches value "0"

  Scenario: Export outcomes produces CSV with scaled and scaleless outcomes
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "More > Outcomes" in the course gradebook
    When I click on "Manage outcomes" "button"
    Then following "Export all outcomes" button should download an outcome file that:
      | Contains text | Outcome with scale    |
      | Contains text | Outcome without scale |
      | Contains text | Test Scale 1          |
      # The scaleless outcome row has empty scale columns: shortname followed by ;;;; with no scale data.
      | Contains text | noscale;;;;           |

  @_file_upload
  Scenario: Import outcomes
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "More > Outcomes" in the course gradebook
    And I click on "Manage outcomes" "button"
    And I click on "Import outcomes" "button"
    When I click on "Choose a file..." "button"
    And I select "Upload a file" repository in file picker
    And I set the field "Attachment" to "#dirroot#/grade/tests/fixtures/outcomes.csv"
    And I click on "Upload this file" "button" in the "File picker" "dialogue"
    And I click on "Upload this file" "button"
    Then I should see "Imported outcome \"Site-wide outcome with scale\""
    And I should see "Imported outcome \"Site-wide outcome without scale\""
    And I should see "Imported outcome \"Course outcome with scale\""
    And I should see "Imported outcome \"Course outcome without scale\""
    And I should see "An outcome with shortname \"noscale\" already exists in this context"
    And I press "Continue"
    And I should see "None" in the "Course outcome without scale" "table_row"
    And I should see "None" in the "Site-wide outcome without scale" "table_row"
    And I should see "Default competence scale" in the "Course outcome with scale" "table_row"
    And I should see "Separate and Connected ways of knowing" in the "Site-wide outcome with scale" "table_row"
