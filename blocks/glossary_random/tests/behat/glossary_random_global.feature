@block @block_glossary_random
Feature: Random glossary entry block linking to global glossary
  In order to show the entries from glossary
  As a teacher
  I can add the random glossary entry to a course page

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "activities" exist:
      | activity   | name             | intro                          | course               | idnumber  | globalglossary | defaultapproval |
      | glossary   | Tips and Tricks  | Frontpage glossary description | C2 | glossary0 | 1              | 1               |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | student1 | Sam1      | Student1 | student1@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: View random (last) entry in the global glossary
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 2"
    And I follow "Tips and Tricks"
    And I press "Add a new entry"
    And I set the following fields to these values:
      | Concept    | Never come late               |
      | Definition | Come in time for your classes |
    And I press "Save changes"
    And I log out
    # As a teacher add a block to the course page linking to the global glossary.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Random glossary entry" block
    And I configure the "block_glossary_random" block
    And I set the following fields to these values:
      | Title                           | Tip of the day      |
      | Take entries from this glossary | Tips and Tricks     |
      | How a new entry is chosen       | Last modified entry |
    And I press "Save changes"
    Then I should see "Never come late" in the "Tip of the day" "block"
    And I should not see "Add a new entry" in the "Tip of the day" "block"
    And I should see "View all entries" in the "Tip of the day" "block"
    And I log out
    # Student who can't see the module is still able to view entries in this block (because the glossary was marked as global)
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Never come late" in the "Tip of the day" "block"
    And I should not see "Add a new entry" in the "Tip of the day" "block"
    And I should see "View all entries" in the "Tip of the day" "block"
    And I log out

  Scenario: Removing the global glossary that is used in random glossary block
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Random glossary entry" block
    And I configure the "block_glossary_random" block
    And I set the following fields to these values:
      | Title                           | Tip of the day      |
      | Take entries from this glossary | Tips and Tricks     |
      | How a new entry is chosen       | Last modified entry |
    And I press "Save changes"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 2"
    And I follow "Tips and Tricks"
    And I follow "Edit settings"
    And I set the field "globalglossary" to "0"
    And I press "Save and return to course"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Please configure this block using the edit icon." in the "Tip of the day" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And "Tip of the day" "block" should not exist
    And I log out
