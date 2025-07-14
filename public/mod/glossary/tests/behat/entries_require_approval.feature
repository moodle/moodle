@mod @mod_glossary
Feature: A teacher can choose whether glossary entries require approval
  In order to check entries before they are displayed
  As a user
  I need to enable entries requiring approval

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activity" exists:
      | activity                      | glossary                               |
      | course                        | C1                                     |
      | idnumber                      | 0001                                   |
      | name                          | Test glossary name                     |
      | intro                         | Test glossary entries require approval |
      | section                       | 1                                      |
      | defaultapproval               | 0                                      |

  Scenario: Approve and undo approve glossary entries
    Given I am on the "Test glossary name" "glossary activity" page logged in as student1
    When I add a glossary entry with the following data:
      | Concept | Just a test concept |
      | Definition | Concept definition |
      | Keyword(s) | Black |
    And I log out
    # Test that students can not see the unapproved entry.
    And I am on the "Test glossary name" "glossary activity" page logged in as student2
    Then I should see "No entries found in this section"
    And I log out
    # Approve the entry.
    And I am on the "Test glossary name" "glossary activity" page logged in as teacher1
    And I follow "Pending approval (1)"
    Then I should see "(this entry is currently hidden)"
    And I follow "Approve"
    And I am on the "Test glossary name" "glossary activity" page
    Then I should see "Concept definition"
    And I log out
    # Check that the entry can now be viewed by students.
    And I am on the "Test glossary name" "glossary activity" page logged in as student2
    Then I should see "Concept definition"
    And I log out
    # Undo the approval of the previous entry.
    And I am on the "Test glossary name" "glossary activity" page logged in as teacher1
    And I follow "Undo approval"
    And I log out
    # Check that the entry is no longer visible by students.
    And I am on the "Test glossary name" "glossary activity" page logged in as student2
    Then I should see "No entries found in this section"

  @javascript
  Scenario: View pending approval glossary items
    Given I am on the "Test glossary name" "glossary activity" page logged in as student1
    When I add a glossary entry with the following data:
      | Concept | Just a test concept |
      | Definition | Concept definition |
      | Keyword(s) | Black |
      | Tags       | Test  |
    And I log out
    And I log in as "teacher1"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Test"
    Then I should see "Glossary entries"
    And I should see "Just a test concept"
    And I should see "Entry not approved"
