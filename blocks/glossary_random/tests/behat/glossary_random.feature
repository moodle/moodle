@block @block_glossary_random
Feature: Random glossary entry block is used in a course
  In order to show the entries from glossary
  As a teacher
  I can add the random glossary entry to a course page

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | student1 | Sam1      | Student1 | student1@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Student can not see the block if it is not configured
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Random glossary entry" block
    Then I should see "Please configure this block using the edit icon" in the "block_glossary_random" "block"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "block_glossary_random" "block" should not exist
    And I log out

  Scenario: View random (last) entry in the glossary with auto approval
    Given the following "activities" exist:
      | activity | name         | intro                     | course | idnumber  | defaultapproval |
      | glossary | GlossaryAuto | Test glossary description | C1     | glossary1 | 1               |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Random glossary entry" block
    And I configure the "block_glossary_random" block
    And I set the following fields to these values:
      | Title                           | AutoGlossaryblock   |
      | Take entries from this glossary | GlossaryAuto        |
      | How a new entry is chosen       | Last modified entry |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "There are no entries yet in the chosen glossary" in the "AutoGlossaryblock" "block"
    And I click on "Add a new entry" "link" in the "AutoGlossaryblock" "block"
    And I set the following fields to these values:
      | Concept    | Concept1    |
      | Definition | Definition1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I should see "Concept1" in the "AutoGlossaryblock" "block"
    And I should see "Definition1" in the "AutoGlossaryblock" "block"
    And I should not see "There are no entries yet in the chosen glossary" in the "AutoGlossaryblock" "block"
    And I click on "Add a new entry" "link" in the "AutoGlossaryblock" "block"
    And I set the following fields to these values:
      | Concept    | Concept2    |
      | Definition | Definition2 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    # Only the last entry appears in the block
    And I should not see "Concept1" in the "AutoGlossaryblock" "block"
    And I should not see "Definition1" in the "AutoGlossaryblock" "block"
    And I should see "Concept2" in the "AutoGlossaryblock" "block"
    And I should see "Definition2" in the "AutoGlossaryblock" "block"
    And I click on "View all entries" "link" in the "AutoGlossaryblock" "block"
    And I should see "GlossaryAuto" in the "#page-navbar" "css_element"
    And I should see "Concept1" in the "#page-content" "css_element"
    And I should see "Concept2" in the "#page-content" "css_element"
    And I log out

  Scenario: View random (last) entry in the glossary with manual approval
    Given the following "activities" exist:
      | activity | name           | intro                     | course | idnumber  | defaultapproval |
      | glossary | GlossaryManual | Test glossary description | C1     | glossary2 | 0               |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Random glossary entry" block
    And I configure the "block_glossary_random" block
    And I set the following fields to these values:
      | Title                           | ManualGlossaryblock |
      | Take entries from this glossary | GlossaryManual      |
      | How a new entry is chosen       | Last modified entry |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "There are no entries yet in the chosen glossary" in the "ManualGlossaryblock" "block"
    And I click on "Add a new entry" "link" in the "ManualGlossaryblock" "block"
    And I set the following fields to these values:
      | Concept    | Concept1    |
      | Definition | Definition1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I should see "There are no entries yet in the chosen glossary" in the "ManualGlossaryblock" "block"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should see "There are no entries yet in the chosen glossary" in the "ManualGlossaryblock" "block"
    And I follow "GlossaryManual"
    And I follow "Waiting approval"
    And I follow "Approve"
    And I click on "Course 1" "link" in the "#page-navbar" "css_element"
    And I should see "Concept1" in the "ManualGlossaryblock" "block"
    And I should see "Definition1" in the "ManualGlossaryblock" "block"
    And I log out
