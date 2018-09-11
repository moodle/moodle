@mod @mod_questionnaire
Feature: In questionnaire, personality tests can be constructed using feedback on specific question responses.
  In order to define a feedback questionnaire
  As a teacher
  I must add the required question types and complete the feedback options.

  @javascript
  Scenario: Create a questionnaire with a rate question type and verify that feedback options exist.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber | resume | navigate |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 | 1 | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Advanced settings" in current page administration
    Then I should not see "Feedback options"
    And I follow "Questions"
    Then I should see "Add questions"
    And I add a "Dropdown Box" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Question Text | Select one dropdown |
      | Possible answers | 1=One,2=Two,3=Three,4=Four |
    Then I should see "[Dropdown Box] (Q1)"
    And I add a "Radio Buttons" question and I fill the form with:
      | Question Name | Q2 |
      | Yes | y |
      | Horizontal | Checked |
      | Question Text | Select one radio |
      | Possible answers | 1=One,2=Two,3=Three,4=Four |
    Then I should see "[Radio Buttons] (Q2)"
    And I add a "Rate (scale 1..5)" question and I fill the form with:
      | Question Name | Q3 |
      | Yes | y |
      | Nb of scale items | 4 |
      | Type of rate scale | Normal |
      | Question Text | Rate these |
      | Possible answers | 1=One,2=Two,3=Three,4=Four,Cheese,Bread,Meat,Fruit |
    Then I should see "[Rate (scale 1..5)] (Q3)"
    And I add a "Yes/No" question and I fill the form with:
      | Question Name | Q4 |
      | Yes | y |
      | Question Text | Yes or no |
    Then I should see "[Yes/No] (Q4)"
    And I follow "Advanced settings"
    And I should see "Feedback options"
    And I follow "Feedback options"
    And I set the field "id_feedbacksections" to "Global Feedback"
    And I set the field "id_feedbackscores" to "Yes"
    And I set the field "id_feedbacknotes" to "These are the main Feedback notes"
    And I press "Save settings and edit Feedback Sections"
    Then I should see "Global Feedback heading"
    And I set the field "id_sectionlabel" to "Global feedback label"
    And I set the field "id_sectionheading" to "Global section heading"
    And I set the field "id_feedbacktext_0" to "Feedback 100%"
    And I set the field "id_feedbackboundaries_0" to "50"
    And I set the field "id_feedbacktext_1" to "Feedback 50%"
    And I set the field "id_feedbackboundaries_1" to "20"
    And I set the field "id_feedbacktext_2" to "Feedback 20%"
    And I press "Save settings"
    And I log out

#  Scenario: Student completes feedback questions.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one dropdown"
    And I set the field "Select one dropdown" to "Three"
    And I click on "Three" "radio"
    And I click on "Choice Three for row Cheese" "radio"
    And I click on "Choice Three for row Bread" "radio"
    And I click on "Choice Three for row Meat" "radio"
    And I click on "Choice Three for row Fruit" "radio"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I follow "Continue"
    Then I should see "Your response"
    And I should see "These are the main Feedback notes"
    And I should see "Global feedback label"
    And I should see "76%"
    And I should see "Feedback 100%"
    And I log out

#  Scenario: Another student completes feedback questions differently.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one dropdown"
    And I set the field "Select one dropdown" to "One"
    And I click on "One" "radio"
    And I click on "Choice Two for row Cheese" "radio"
    And I click on "Choice Two for row Bread" "radio"
    And I click on "Choice Two for row Meat" "radio"
    And I click on "Choice Two for row Fruit" "radio"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I follow "Continue"
    Then I should see "Your response"
    And I should see "These are the main Feedback notes"
    And I should see "Global feedback label"
    And I should see "44%"
    And I should see "Feedback 50%"
    And I log out