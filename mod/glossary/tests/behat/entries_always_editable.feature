@mod @mod_glossary
Feature: A teacher can set whether glossary entries are always editable or not
  In order to ensure students think before adding new entries
  As a teacher
  I need to prevent entries to be always editable

  @javascript
  Scenario: Glossary entries are not always editable
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | Maximum time to edit posts | 1 minutes |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
      | Always allow editing | No |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test glossary name"
    When I add a glossary entry with the following data:
      | Concept | Test concept name |
      | Definition | Test concept description |
    Then "Delete: Test concept name" "link" should exists
    And "Edit: Test concept name" "link" should exists
    And I wait "65" seconds
    And I reload the page
    Then "Delete: Test concept name" "link" should not exists
    And "Edit: Test concept name" "link" should not exists
