@block @block_glossary_random @javascript @addablocklink
Feature: Add the glossary random block when main feature is enabled
    In order to add the glossary random block to my course
    As a teacher
    It should be added to courses only if the glossary module is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I am on the "C1" "course" page logged in as "admin"

  Scenario: The glossary random block can be added when glossary module is enabled
    Given I turn editing mode on
    When I click on "Add a block" "link"
    Then I should see "Random glossary entry"

  Scenario: The glossary random block cannot be added when glossary module is disabled
    Given I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I toggle the "Disable Glossary" admin switch "off"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add a block" "link"
    Then I should not see "Random glossary entry"

  Scenario: View alphabetical order multilang entries in the glossary block
    Given the following "activities" exist:
      | activity | name    | intro              | course | idnumber  | defaultapproval |
      | glossary | Animals | An animal glossary | C1     | glossary3 | 1               |
    And the following "mod_glossary > entries" exist:
      | glossary | user     | concept   | definition                                                                                          |
      | Animals  | student1 | Aardvark  | <span lang="en" class="multilang">Aardvark</span><span lang="de" class="multilang">Erdferkel</span> |
      | Animals  | student1 | Kangaroo  | <span lang="en" class="multilang">Kangaroo</span><span lang="de" class="multilang">Känguru</span>   |
      | Animals  | student1 | Zebra     | <span lang="en" class="multilang">Zebra</span><span lang="de" class="multilang">Zebra</span>        |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I log out
    And I log in as "teacher1"
    And I am on "C1" course homepage with editing mode on
    And I add the "Random glossary entry..." block
    And I set the following fields to these values:
      | Title                             | ManualGlossaryblock |
      | Take entries from this glossary   | Animals             |
      | Days before a new entry is chosen | 0                   |
      | How a new entry is chosen         | Alphabetical order  |
    And I press "Save changes"
    And I should see "Aardvark" in the "ManualGlossaryblock" "block"
    And I should not see "AardvarkErdferkel" in the "ManualGlossaryblock" "block"
    And I reload the page
    And I should see "Kangaroo" in the "ManualGlossaryblock" "block"
    And I reload the page
    Then I should see "Zebra" in the "ManualGlossaryblock" "block"
    And I log out
