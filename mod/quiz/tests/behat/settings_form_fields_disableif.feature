@mod @mod_quiz
Feature: Settings form fields disabled if not required
  In to create quizzes as simply as possible
  As a teacher
  I don't need to to use certain form fields.

  Background:
    Given the following "users" exist:
      | username | firstname |
      | teacher  | Teach     |
      | student1 | Student1  |
      | student2 | Student2  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |

  @javascript
  Scenario: Depending on the number of attempts, different form fields are disabled.
    When I log in as "teacher"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1"
    And I expand all fieldsets
    And I set the field "Name" to "Test quiz"
    And I set the field "Attempts allowed" to "1"
    Then the "Grading method" "field" should be disabled
    And the "Each attempt builds on the last" "field" should be disabled
    And the "id_delay1_enabled" "field" should be disabled
    And the "id_delay2_enabled" "field" should be disabled

    And I set the field "Attempts allowed" to "2"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    And the "id_delay1_enabled" "field" should be enabled
    And the "id_delay2_enabled" "field" should be disabled

    And I set the field "Attempts allowed" to "3"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    And the "id_delay1_enabled" "field" should be enabled
    And the "id_delay2_enabled" "field" should be enabled

    And I set the field "Attempts allowed" to "Unlimited"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    # And the "id_delay1_enabled" "field" should be enabled
    # And the "id_delay2_enabled" "field" should be enabled

    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
        | Override user    | Student1 |
        | Attempts allowed | 3        |
    And I press "Save"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Attempts allowed" to "1"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    And the "id_delay1_enabled" "field" should be enabled
    And the "id_delay2_enabled" "field" should be enabled

    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I click on "Edit" "link" in the "region-main" "region"
    And I set the field "Attempts allowed" to "2"
    And I press "Save"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Attempts allowed" to "1"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    And the "id_delay1_enabled" "field" should be enabled
    And the "id_delay2_enabled" "field" should be disabled

    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
        | Override user    | Student2  |
        | Attempts allowed | Unlimited |
    And I press "Save"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Attempts allowed" to "1"
    And the "Grading method" "field" should be enabled
    And the "Each attempt builds on the last" "field" should be enabled
    And the "id_delay1_enabled" "field" should be enabled
    And the "id_delay2_enabled" "field" should be enabled

  @javascript
  Scenario: Depending on whether there is a close date, some review options are disabled.
    When I log in as "teacher"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1"
    And I expand all fieldsets
    And I set the field "Name" to "Test quiz"
    Then the "id_attemptclosed" "checkbox" should be disabled
    And the "id_correctnessclosed" "checkbox" should be disabled
    And the "id_marksclosed" "checkbox" should be disabled
    And the "id_specificfeedbackclosed" "checkbox" should be disabled
    And the "id_generalfeedbackclosed" "checkbox" should be disabled
    And the "id_rightanswerclosed" "checkbox" should be disabled
    And the "id_overallfeedbackclosed" "checkbox" should be disabled
    And I set the field "id_timeclose_enabled" to "1"
    And the "id_attemptclosed" "checkbox" should be enabled
    And the "id_correctnessclosed" "checkbox" should be enabled
    And the "id_marksclosed" "checkbox" should be enabled
    And the "id_specificfeedbackclosed" "checkbox" should be enabled
    And the "id_generalfeedbackclosed" "checkbox" should be enabled
    And the "id_rightanswerclosed" "checkbox" should be enabled
    And the "id_overallfeedbackclosed" "checkbox" should be enabled
    And I should not see "Repaginate now"

  @javascript
  Scenario: If there are quiz attempts, there is not option to repaginate.
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
    When I am on the "Quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    Then I should see "Repaginate now"
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
    And I am on the "Quiz 1" "quiz activity editing" page
    And I expand all fieldsets
    And I should not see "Repaginate now"
