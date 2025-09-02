@qformat @qformat_aiken
Feature: Test exporting questions using Aiken format.
  In order to reuse questions
  As an teacher
  I need to be able to export them in Aiken format.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity   | name    | course | idnumber |
      | qbank      | Qbank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory    | qtype        | name             | template    |
      | Default for Qbank 1 | multichoice  | Multi-choice-001 | two_of_four |
      | Default for Qbank 1 | multichoice  | Multi-choice-002 | one_of_four |

  Scenario: Aiken export
    When I am on the "Qbank 1" "core_question > question export" page logged in as "teacher1"
    And I set the field "id_format_aiken" to "1"
    When I press "Export questions to file"
    Then following "click here" should download a file that:
      | Has mimetype  | text/plain                  |
      | Contains text | Which is the oddest number? |
    # If the download step is the last in the scenario then we can sometimes run
    # into the situation where the download page causes a http redirect but behat
    # has already conducted its reset (generating an error). By putting a logout
    # step we avoid behat doing the reset until we are off that page.
    And I log out
