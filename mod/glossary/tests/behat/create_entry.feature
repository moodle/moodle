@mod @mod_glossary
Feature: Create a glossary entry.
  In order to create glossary entries
  As a user
  I should be able to enter an entry without using reserved keywords

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity | glossary |
      | course | C1 |
      | name | Test glossary |

  Scenario: Glossary entry edition of custom tags works as expected
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    When I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Dummy first entry               |
      | Definition | Dream is the start of a journey |
      | Keyword(s) | "                               |
    And I press "Save changes"
    Then I should see "One or more keywords contain a special character which cannot be used."

  @javascript @_file_upload
  Scenario: Create glossary entry with attached file
    Given I am on the "Test glossary" "glossary activity" page logged in as student1
    # As a student, add a glossary entry with attachment
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Entry 1                                   |
      | Definition | Definition of Entry 1                     |
      | Attachment | mod/glossary/tests/fixtures/musicians.xml |
    And I press "Save changes"
    # Confirm you can download attachment from student's entry as teacher
    When I am on the "Test glossary" "glossary activity" page logged in as teacher1
    Then I should see "Entry 1"
    And I should see "musicians.xml"
    And following "musicians.xml" should download between "1" and "3000" bytes
