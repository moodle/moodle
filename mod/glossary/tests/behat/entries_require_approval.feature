@mod @mod_glossary
Feature: A teacher can choose whether glossary entries require approval
  In order to check entries before they are displayed
  As a user
  I need to enable entries requiring approval

  Scenario: Approve and undo approve glossary entries
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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    Given I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary entries require approval |
      | Approved by default | No |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test glossary name"
    When I add a glossary entry with the following data:
      | Concept | Just a test concept |
      | Definition | Concept definition |
      | Keyword(s) | Black |
    And I log out
    # Test that students can not see the unapproved entry.
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test glossary name"
    Then I should see "No entries found in this section"
    And I log out
    # Approve the entry.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test glossary name"
    And I follow "Waiting approval"
    Then I should see "(this entry is currently hidden)"
    And I follow "Approve"
    And I follow "Test glossary name"
    Then I should see "Concept definition"
    And I log out
    # Check that the entry can now be viewed by students.
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test glossary name"
    Then I should see "Concept definition"
    And I log out
    # Undo the approval of the previous entry.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test glossary name"
    And I follow "Undo approval"
    And I log out
    # Check that the entry is no longer visible by students.
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test glossary name"
    Then I should see "No entries found in this section"
