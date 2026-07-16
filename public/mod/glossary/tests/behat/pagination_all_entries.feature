@mod @mod_glossary
Feature: Glossary pagination and alphabet filter display entries correctly
  In order to view glossary entries correctly
  As a user
  I need entries to display properly across alphabet filter, pagination and print views

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name          | intro                 | course | idnumber | entbypage | showall | showspecial | allowprintview |
      | glossary | Test glossary | Test glossary entries | C1     | g1       | 2         | 1       | 1           | 1              |
    And the following "mod_glossary > entries" exist:
      | glossary | concept  | definition              | user     |
      | g1       | Apple    | A type of fruit         | teacher1 |
      | g1       | apricot  | A small fruit           | teacher1 |
      | g1       | Banana   | A yellow fruit          | teacher1 |
      | g1       | Cherry   | A small red fruit       | teacher1 |
      | g1       | Date     | A sweet fruit           | teacher1 |
      | g1       | Eggplant | A purple vegetable      | teacher1 |
      | g1       | 1st item | Entry starting with one | teacher1 |

  Scenario: Clicking ALL in alphabet filter shows entries from all letters
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    And I click on "A" "link" in the ".entrybox" "css_element"
    # After clicking A, only A entries are shown. Click ALL to show all letters again.
    When I click on "ALL" "link" in the ".entrybox" "css_element"
    # ALL alphabet filter still has pagination (entbypage=2), so page 1 shows first 2 entries.
    Then I should see "1st item"
    And I should see "Apple"
    And I should not see "No entries found in this section"
    # Pagination bar should be visible when viewing subset of glossary entries.
    And I should see "ALL" in the ".paging" "css_element"

  Scenario: Clicking ALL in paging bar shows all glossary entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    # By default we should see paginated results with the paging bar.
    When I click on "ALL" "link" in the ".paging" "css_element"
    Then I should see "Apple"
    And I should see "apricot"
    And I should see "Banana"
    And I should see "Cherry"
    And I should see "Date"
    And I should see "Eggplant"
    And I should not see "No entries found in this section"
    # Pagination bar should be hidden when viewing all glossary entries.
    And I should not see "ALL" in the ".paging" "css_element"

  @javascript
  Scenario: Print view from ALL alphabet filter and ALL paging shows all entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    And I click on "A" "link" in the ".entrybox" "css_element"
    And I click on "ALL" "link" in the ".entrybox" "css_element"
    # Also click ALL in paging bar to remove pagination, so print shows all entries.
    And I click on "ALL" "link" in the ".paging" "css_element"
    When I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    Then I should see "Apple"
    And I should see "apricot"
    And I should see "Banana"
    And I should see "Cherry"
    And I should see "Date"
    And I should see "Eggplant"

  @javascript
  Scenario: Print view from a specific letter shows only matching entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    And I click on "A" "link" in the ".entrybox" "css_element"
    When I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    Then I should see "Apple"
    And I should see "apricot"
    And I should not see "Banana"
    And I should not see "Cherry"

  @javascript
  Scenario: Print view from Special shows only non A-Z entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    And I click on "Special" "link" in the ".entrybox" "css_element"
    When I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    Then I should see "1st item"
    And I should not see "Apple"
    And I should not see "Banana"

  @javascript
  Scenario: Print view from a specific pagination page shows only that page entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    # entbypage=2, default hook=ALL so all entries shown.
    # Sort order is case-insensitive: 1st item, Apple, apricot, Banana, Cherry, Date, Eggplant.
    # Page 1: 1st item, Apple. Page 2: apricot, Banana.
    When I click on "2" "link" in the ".paging" "css_element"
    And I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    # Page 2 should show apricot and Banana only.
    Then I should not see "1st item"
    And I should not see "Apple"
    And I should see "apricot"
    And I should see "Banana"
    And I should not see "Cherry"
    And I should not see "Date"

  @javascript
  Scenario: Print view after clicking ALL in paging bar shows all entries
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    When I click on "ALL" "link" in the ".paging" "css_element"
    And I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    Then I should see "Apple"
    And I should see "apricot"
    And I should see "Banana"
    And I should see "Cherry"
    And I should see "Date"
    And I should see "Eggplant"

  @javascript
  Scenario: Print view from full text search shows entries matching in definition
    Given I am on the "Test glossary" "glossary activity" page logged in as "teacher1"
    # "fruit" appears in definitions (Apple, apricot, Banana, Cherry, Date) but not in any concept.
    # Search full text is enabled by default.
    When I set the field "hook" to "fruit"
    And I press "Search"
    And I click on "ALL" "link" in the ".paging" "css_element"
    And I click on "Export entries" "button"
    And I click on "Printer-friendly version" "link"
    Then I should see "Apple"
    And I should see "apricot"
    And I should see "Banana"
    And I should not see "Eggplant"
    And I should not see "1st item"
