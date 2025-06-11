@editor @tiny @tiny_cloze @javascript
Feature: Add interface to Cloze question type syntax

  Background:
    Given the following "users" exist:
        | username | firstname | lastname | email               |
        | teacher  | Mark      | Allright | teacher@example.com |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user    | course | role           |
        | teacher | C1     | editingteacher |
    Given the following "user preferences" exist:
        | user    | preference | value |
        | teacher | htmleditor | tiny  |

  Scenario: Create a Cloze question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "SHORTANSWER" to "1"
    And I click on "Select question type" "button"
    And I set the field "Answer" to "cat"
    And I set the field "Feedback" to " That is correct"
    And I click on "Insert question" "button"
    And I click on "Save changes" "button"
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    When I choose "Preview" action for "multianswer-001" in the question bank
     ##################################################
    # Deferred Feedback behaviour with CBM
    And I set the following fields to these values:
        | How questions behave | Immediate feedback |
        | Marked out of        | 2                  |
        | Marks                | Show mark and max  |
        | Specific feedback    | Shown              |
        | Right answer         | Shown              |
    And I press "Fill in correct responses"
    And I press "Submit and finish"
    And I should see "Mark 1.00 out of 1.00"
