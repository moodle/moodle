@mod @mod_choice @core_completion
Feature: Automatic completion in the choice activity
  In order for me to know what to do to complete the choice activity
  As a student
  I need to be able to see the completion requirements of the choice activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
    And the following "course" exists:
      | fullname          | Course 1  |
      | shortname         | C1        |
      | category          | 0         |
      | enablecompletion  | 1         |
    And the following "activity" exists:
      | activity          | choice                  |
      | name              | What to drink?          |
      | intro             | Friday drinks, anyone?  |
      | course            | C1                      |
      | idnumber          | choice1                 |
      | completion        | 2                       |
      | completionview    | 1                       |
      | completionsubmit  | 1                       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Viewing a choice activity with automatic completion as a student
    When I am on the "What to drink?" "choice activity" page logged in as student1
    Then the "View" completion condition of "What to drink?" is displayed as "done"
    And the "Make a choice" completion condition of "What to drink?" is displayed as "todo"
    And I set the field "Beer" to "1"
    And I press "Save my choice"
    And the "View" completion condition of "What to drink?" is displayed as "done"
    And the "Make a choice" completion condition of "What to drink?" is displayed as "done"

  Scenario: Viewing a choice activity with automatic completion as a teacher
    When I am on the "What to drink?" "choice activity" page logged in as teacher1
    Then "What to drink?" should have the "View" completion condition
    And "What to drink?" should have the "Make a choice" completion condition

  @javascript
  Scenario: Overriding automatic choice completion for a user
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Reports > Activity completion" in current page administration
    And I click on "Student 1, What to drink?: Not completed" "link"
    And I press "Save changes"
    And I log out
    When I am on the "What to drink?" "choice activity" page logged in as student1
    Then the "View" completion condition of "What to drink?" overridden by "Teacher 1" is displayed as "done"
    And the "Make a choice" completion condition of "What to drink?" overridden by "Teacher 1" is displayed as "done"
