@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Checks formula creation in forms with multiple editors
In order to use multiple editors
As an admin
I need to re-edit formulas in a question type form

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Create and answer a Quiz using formulas
    And I navigate to "Plugins" in site administration
    And I follow "Atto toolbar settings"
    And I set the field "Toolbar config" to multiline:
    """
    math = wiris
    other = html
    """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1"
    And I set the following fields to these values:
      | Name | Quiz 1 |
    And I press "Save and return to course"
    And I follow "Quiz 1"
    And I press "Edit quiz"
    And I open the action menu in ".page-add-actions" "css_element"

    And I follow "a new question"
    And I choose Short answer
    And I press "submitbutton"
    And I press "MathType" in "Question text" field in Atto editor
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mn>2</mn></msqrt></math>'
    And I press accept button in MathType Editor
    And I press "HTML" in "Question text" field in Atto editor
    And I press "MathType" in "General feedback" field in Atto editor
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mn>3</mn></msqrt></math>'
    And I press accept button in MathType Editor
    And I press "HTML" in "General feedback" field in Atto editor
    And I press "MathType" in "Feedback" field in Atto editor
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mn>4</mn></msqrt></math>'
    And I press accept button in MathType Editor
    And I press "HTML" in "Feedback" field in Atto editor
    Then I should see "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msqrt><mn>2</mn></msqrt></math>"
    And I should see "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msqrt><mn>3</mn></msqrt></math>"
    And I should see "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msqrt><mn>4</mn></msqrt></math>"
    And a Wirisformula containing 'square root of 2' should exist
    And a Wirisformula containing 'square root of 3' should exist
    And a Wirisformula containing 'square root of 4' should exist
    And I set the following fields to these values:
      | Question name | Short 1 |
      | Answer 1 | 10 |
    And I select 100% option in Answer1
    And I press "submitbutton"
    And I follow "Quiz 1"
    And I navigate to "Preview" in current page administration
    Then a Wirisformula containing 'square root of 2' should exist
    And I set the field "Answer" to "10"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    Then a Wirisformula containing 'square root of 2' should exist
    And a Wirisformula containing 'square root of 3' should exist
    And a Wirisformula containing 'square root of 4' should exist
