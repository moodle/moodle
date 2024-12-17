@mod @mod_glossary @core_tag
Feature: Edited glossary entries handle tags correctly
  In order to get glossary entries properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activity" exists:
      | course   | C1                       |
      | activity | glossary                 |
      | name     | Test glossary            |
      | intro    | A glossary about dreams! |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | tags            | System       | 1         | my-index        | side-pre      |

  @javascript
  Scenario: Glossary entry edition of custom tags works as expected
    Given I am on the "Test glossary" "glossary activity" page logged in as teacher1
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept | Dummy first entry |
      | Definition | Dream is the start of a journey |
      | Tags | Example, Entry, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".glossary-tags" "css_element"
    And I should see "Entry" in the ".glossary-tags" "css_element"
    And I should see "Cool" in the ".glossary-tags" "css_element"
    And I click on "Edit" "link" in the ".entrylowersection" "css_element"
    And I expand all fieldsets
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Entry" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Glossary entry edition of standard tags works as expected
    Given the following "tags" exist:
      | name | isstandard |
      | OT1  | 1          |
      | OT2  | 1          |
      | OT3  | 1          |
    And I am on the "Test glossary" "glossary activity" page logged in as teacher1
    And I press "Add entry"
    And I expand all fieldsets
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Concept | Dummy first entry |
      | Definition | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".glossary-tags" "css_element"
    And I should see "OT3" in the ".glossary-tags" "css_element"
    And I should not see "OT2" in the ".glossary-tags" "css_element"
    And I click on "Edit" "link" in the ".entrylowersection" "css_element"
    And I expand all fieldsets
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Student delete glossary entry tags
    # Add glossary entry with tags as an enrolled student.
    Given the following "mod_glossary > entries" exist:
    | glossary      | concept | definition         | tags          | user     |
    | Test glossary | Entry 1 | Entry 1 definition | OT1, OT2, OT3 | student1 |
    And I am on the "Test glossary" "glossary activity" page logged in as student1
    When I click on "Edit entry: Entry 1" "link"
    And I expand all fieldsets
    # Delete a glossary entry tag.
    And I click on "[data-value='OT1']" "css_element"
    And I press "Save changes"
    # Confirm that only the selected glossary entry tag is deleted.
    Then I should not see "OT1" in the ".glossary-tags" "css_element"
    And I should see "OT2" in the ".glossary-tags" "css_element"
    And I should see "OT3" in the ".glossary-tags" "css_element"

  Scenario Outline: Student glossary entry tags can be viewed depending on approval
    # Add glossary entry with tags as an enrolled student.
    Given the following "mod_glossary > entries" exist:
      | glossary      | concept | definition         | tags          | user     | approved   |
      | Test glossary | Entry 1 | Entry 1 definition | OT1, OT2, OT3 | student1 | <approved> |
    When I log in as "student2"
    And I click on "OT1" "link" in the "Tags" "block"
    Then I <entryvisibility> see "Entry 1"

    Examples:
      | approved | entryvisibility |
      | 0        | should not      |
      | 1        | should          |

  @javascript
  Scenario: Hidden glossary activity is not visible in tag index
    Given the following "mod_glossary > entries" exist:
      | glossary      | concept | definition         | tags          | user     |
      | Test glossary | Entry 1 | Entry 1 definition | OT1, OT2, OT3 | student1 |
    And I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    And I open "Test glossary" actions menu
    And I click on "Hide" "link" in the "Test glossary" activity
    When I log in as "student2"
    And I click on "OT1" "link" in the "Tags" "block"
    Then I should not see "Entry 1"
