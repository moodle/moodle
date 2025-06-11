@mod @mod_book @core_tag
Feature: Edited book chapters handle tags correctly
  In order to get book chapters properly labelled
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
    And the following "activity" exists:
      | activity    | book                |
      | course      | C1                  |
      | idnumber    | book1               |
      | name        | Test book           |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | tags            | System       | 1         | my-index        | side-pre      |

  @javascript
  Scenario: Book chapter edition of custom tags works as expected
    Given I am on the "Test book" "book activity" page logged in as teacher1
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | Example, Chapter, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".book-tags" "css_element"
    And I should see "Chapter" in the ".book-tags" "css_element"
    And I should see "Cool" in the ".book-tags" "css_element"
    And I turn editing mode on
    And I follow "Edit chapter \"1. Dummy first chapter\""
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Chapter" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Book chapter edition of standard tags works as expected
    Given the following "tags" exist:
      | name | isstandard |
      | OT1  | 1          |
      | OT2  | 1          |
      | OT3  | 1          |
    And I am on the "Test book" "book activity" page logged in as teacher1
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".book-tags" "css_element"
    And I should see "OT3" in the ".book-tags" "css_element"
    And I should not see "OT2" in the ".book-tags" "css_element"
    And I turn editing mode on
    And I follow "Edit chapter \"1. Dummy first chapter\""
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Deleting book chapter tags
    # Add a book chapter with tags.
    Given the following "mod_book > chapters" exist:
      | book  | title               | content                         | tags                   |
      | book1 | Dummy first chapter | Dream is the start of a journey | Example, Chapter, Cool |
    And I am on the "Test book" "book activity" page logged in as teacher1
    And I turn editing mode on
    When I follow "Edit chapter \"1. Dummy first chapter\""
    # Delete one of the book chapter tags.
    And I click on "[data-value='Example']" "css_element"
    And I press "Save changes"
    # Confirm that the deleted tag no longer exists in the book chapter.
    Then I should not see "Example" in the ".book-tags" "css_element"
    And I should see "Chapter" in the ".book-tags" "css_element"
    And I should see "Cool" in the ".book-tags" "css_element"

  Scenario: Duplicating book chapter tags
    # Add a book chapter with tags.
    Given the following "mod_book > chapters" exist:
      | book  | title               | content                         | tags                   |
      | book1 | Dummy first chapter | Dream is the start of a journey | Example, Chapter, Cool |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # Duplicate the book activity.
    When I duplicate "Test book" activity
    # Confirm that the book activity duplicate contains all the tags from the original activity.
    Then I should see "Test book (copy)"
    And I am on the "Test book (copy)" "book activity" page
    And I should see "Example" in the ".book-tags" "css_element"
    And I should see "Chapter" in the ".book-tags" "css_element"
    And I should see "Cool" in the ".book-tags" "css_element"

  Scenario Outline: Only enrolled users can see book chapters by tags
    # Add a book chapter with tags.
    Given the following "mod_book > chapters" exist:
      | book  | title               | content                         | tags                   |
      | book1 | Dummy first chapter | Dream is the start of a journey | Example, Chapter, Cool |
    When I log in as "<user>"
    And I click on "Chapter" "link" in the "Tags" "block"
    Then I <chaptervisibility> see "Dummy first chapter"

    Examples:
      | user      | chaptervisibility |
      | teacher1  | should            |
      | student1  | should            |
      | student2  | should not        |
