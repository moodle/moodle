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
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "What to drink?"
    Then I should see "Done: View" in the "[data-region=completionrequirements]" "css_element"
    And I should see "To do: Make a choice" in the "[data-region=completionrequirements]" "css_element"
    And I set the field "Beer" to "1"
    And I press "Save my choice"
    And I should see "Done: View" in the "[data-region=completionrequirements]" "css_element"
    And I should see "Done: Make a choice" in the "[data-region=completionrequirements]" "css_element"

  Scenario: Viewing a choice activity with automatic completion as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "What to drink?"
    And I should see "View" in the "[data-region=completionrequirements]" "css_element"
    And I should see "Make a choice" in the "[data-region=completionrequirements]" "css_element"

  @javascript
  Scenario: Overriding automatic choice completion for a user
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    And I click on "Student 1, What to drink?: Not completed" "link"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "What to drink?"
    Then "span[aria-label=\"Done: View (set by Teacher 1)\"]" "css_element" should exist
    And "span[aria-label=\"Done: Make a choice (set by Teacher 1)\"]" "css_element" should exist
