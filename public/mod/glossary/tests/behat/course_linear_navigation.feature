@mod @mod_glossary
Feature: Display the course linear navigation in the glossary pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in glossary pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activity" exists:
      | activity        | glossary      |
      | course          | C1            |
      | name            | Test glossary |
      | idnumber        | glossary1     |
      | editalways      | 1             |
      | defaultapproval | 1             |

  @javascript
  Scenario: As a student I should see the course linear navigation in glossary pages that allow it
    Given I am on the "Test glossary" "glossary activity" page logged in as "student"
    Then the course linear navigation should be visible
    And I press "Add entry"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Concept    | Dummy first entry               |
      | Definition | Dream is the start of a journey |
    And I press "Save changes"
    And the course linear navigation should be visible
    And I set the field "hook" to "Dummy"
    And I press "Search"
    And the course linear navigation should be visible
    And I click on "D" "link" in the ".entrybox" "css_element"
    And the course linear navigation should be visible
    And I click on "Edit" "link"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Concept    | Dummy edited entry               |
    And I press "Save changes"
    And the course linear navigation should be visible
    And I click on "Entry link: Dummy edited entry" "link"
    And the course linear navigation should be visible
    And I click on "Delete entry: Dummy edited entry" "link"
    And the course linear navigation should not be visible
    And I press "Continue"
    And the course linear navigation should be visible
    And the following "mod_glossary > entries" exist:
      | glossary  | user    | concept        | definition | approved |
      | glossary1 | student | Just to print  | Printing   | 1        |
    And I press "Export entries"
    And I click on "Printer-friendly version" "link"
    And I switch to a second window
    And the course linear navigation should not be visible

  @javascript @_file_upload
  Scenario: As a teacher I should see the course linear navigation in glossary pages that allow it
    Given the following "mod_glossary > entries" exist:
      | glossary  | user    | concept       | definition          | approved |
      | glossary1 | student | Approved      | Approved entry      | 1        |
      | glossary1 | student | Non approved  | Non approved entry  | 0        |
    And I am on the "Test glossary" "glossary activity" page logged in as "teacher"
    When I follow "Pending approval"
    Then the course linear navigation should not be visible
    And I follow "Approve"
    And the course linear navigation should not be visible
    And I follow "Glossary"
    And the course linear navigation should be visible
    And I click on "Entry link: Approved" "link"
    And the course linear navigation should be visible
    And the following "activities" exist:
      | activity | name           | intro                     | displayformat | course | idnumber     | mainglossary  |
      | glossary | Main Glossary  | Main glossary description | encyclopedia  | C1     | glossarymain | 1             |
    And the following "mod_glossary > entries" exist:
      | glossary      | user    | concept    | definition     |
      | glossarymain  | student | Main entry | Main glossary  |
    And I am on the "Main Glossary" "glossary activity" page
    And I select "Browse by category" from the "Browse the glossary using this index" singleselect
    And the course linear navigation should be visible
    And I press "Edit categories"
    And the course linear navigation should not be visible
    And I press "Add category"
    And the course linear navigation should not be visible
    And I set the field "name" to "GlossaryCategory"
    And I press "Save changes"
    And the course linear navigation should not be visible
    And I press "Back"
    And the course linear navigation should be visible
    And I press "Import entries"
    And the course linear navigation should not be visible
    And I upload "mod/glossary/tests/fixtures/texfilter_glossary_en.xml" file to "File to import" filemanager
    And I press "Submit"
    And the course linear navigation should not be visible
    And I press "Continue"
    And the course linear navigation should be visible
    And I press "Export entries"
    And I click on "Export" "link"
    And the course linear navigation should not be visible
    And I am on the "Test glossary" "glossary activity" page
    And I click on "Export to main glossary" "link"
    And the course linear navigation should not be visible
    And I press "Continue"
    And the course linear navigation should be visible
