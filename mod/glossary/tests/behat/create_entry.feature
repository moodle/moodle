@mod @mod_glossary @javascript
Feature: Create a glossary entry. As a user
  I should be able to enter an entry without
  using reserved keywords

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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary |
      | Description | A glossary about dreams! |
    And I log out

  Scenario: Glossary entry edition of custom tags works as expected
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Dummy first entry               |
      | Definition | Dream is the start of a journey |
      | Keyword(s) | "                               |
    And I press "Save changes"
    Then I should see "Some/All of the entered keywords cannot be used as they are reserved."
