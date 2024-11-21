@mod @mod_questionnaire @core_completion
Feature: View activity completion information in the questionnaire activity
  In order to have visibility of questionnaire completion requirements
  As a student
  I need to be able to view my questionnaire completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity      | name                          | introduction                   | course | idnumber       | completion | completionview | completionpostsenabled | completionposts |
      | questionnaire | Test questionnaire completion | Test questionnaire description | C1     | questionnaire2 | 2          | 1              | 1                      | 1               |

  @javascript
  Scenario: Check questionnaire completion feature in web for Moodle ≤ 4.2.
    Given the site is running Moodle version 4.2 or lower
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire completion"
    Then I click on "Add questions" "link"
    And I add a "Yes/No" question and I fill the form with:
      | Question Name | Q1                       |
      | Yes           | y                        |
      | Question Text | Are you still in School? |
    Then I should see "[Yes/No] (Q1)"
    And I add a "Radio Buttons" question and I fill the form with:
      | Question Name    | Q2                         |
      | Yes              | y                          |
      | Horizontal       | Checked                    |
      | Question Text    | Select one choice          |
      | Possible answers | 1=One,2=Two,3=Three,4=Four |
    Then I should see "[Radio Buttons] (Q2)"
    And I add a "Text Box" question and I fill the form with:
      | Question Name    | Q8              |
      | No               | n               |
      | Input box length | 10              |
      | Max. text length | 15              |
      | Question Text    | Enter some text |
    Then I should see "[Text Box] (Q8)"
    And I am on the "Test questionnaire completion" "questionnaire activity editing" page
    And I set the following fields to these values:
      | Completion tracking | Show activity as complete when conditions are met |
    And I click on "Student must submit this questionnaire to complete it" "checkbox"
    And I press "Save and display"

    And I am on the "Test questionnaire completion" "questionnaire activity" page
    Then I should see "You are not eligible to take this questionnaire."

    And I am on the "Test questionnaire completion" "questionnaire activity" page logged in as "student1"
    And I click on "Answer the questions..." "link"
    Then I should see "Are you still in School?"
    And I should see "Select one choice"
    And I should see "Enter some text"
    And I click on "Yes" "radio"
    And I click on "Three" "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    Then I should see "View your response(s)"

  Scenario: Check questionnaire completion feature in web for Moodle ≥ 4.3.
    Given the site is running Moodle version 4.3 or higher
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire completion"
    Then I click on "Add questions" "link"
    And I add a "Yes/No" question and I fill the form with:
      | Question Name | Q1                       |
      | Yes           | y                        |
      | Question Text | Are you still in School? |
    Then I should see "[Yes/No] (Q1)"
    And I add a "Radio Buttons" question and I fill the form with:
      | Question Name    | Q2                         |
      | Yes              | y                          |
      | Horizontal       | Checked                    |
      | Question Text    | Select one choice          |
      | Possible answers | 1=One,2=Two,3=Three,4=Four |
    Then I should see "[Radio Buttons] (Q2)"
    And I add a "Text Box" question and I fill the form with:
      | Question Name    | Q8              |
      | No               | n               |
      | Input box length | 10              |
      | Max. text length | 15              |
      | Question Text    | Enter some text |
    Then I should see "[Text Box] (Q8)"
    And I am on the "Test questionnaire completion" "questionnaire activity editing" page
    And I click on "Expand all" "link" in the "region-main" "region"
    And I set the field "Add requirements" to "1"
    And I set the following fields to these values:
      | Add requirements                                      | 1 |
      | Student must submit this questionnaire to complete it | 1 |
    And I press "Save and display"

    And I am on the "Test questionnaire completion" "questionnaire activity" page
    Then I should see "You are not eligible to take this questionnaire."

    And I am on the "Test questionnaire completion" "questionnaire activity" page logged in as "student1"
    And I click on "Answer the questions..." "link"
    Then I should see "Are you still in School?"
    And I should see "Select one choice"
    And I should see "Enter some text"
    And I set the field "Yes" to "1"
    And I set the field "Three" to "1"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    Then I should see "View your response(s)"
