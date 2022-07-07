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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype        | name             | template    |
      | Test questions   | multichoice  | Multi-choice-001 | two_of_four |
      | Test questions   | multichoice  | Multi-choice-002 | one_of_four |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: Aiken export
    When I navigate to "Question bank > Export" in current page administration
    And I set the field "id_format_aiken" to "1"
    When I press "Export questions to file"
    Then following "click here" should download between "68" and "70" bytes
    # If the download step is the last in the scenario then we can sometimes run
    # into the situation where the download page causes a http redirect but behat
    # has already conducted its reset (generating an error). By putting a logout
    # step we avoid behat doing the reset until we are off that page.
    And I log out
