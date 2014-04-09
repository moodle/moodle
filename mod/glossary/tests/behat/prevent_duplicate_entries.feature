@mod @mod_glossary
Feature: A teacher can choose whether to allow duplicate entries in a glossary
  In order to avoid confusion
  As a teacher
  I need to avoid having duplicate concept definitions

  @javascript
  Scenario: Prevent duplicate entries
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
      | Duplicate entries allowed | No |
    And I follow "Test glossary name"
    And I add a glossary entry with the following data:
      | Concept | Unique concept |
      | Definition | I'm the definition of an unique concept |
    When I press "Add a new entry"
    And I set the following fields to these values:
      | Concept | Unique concept |
      | Definition | There is no definition restriction |
    And I press "Save changes"
    Then I should see "This concept already exists. No duplicates allowed in this glossary."
    And I press "Cancel"

