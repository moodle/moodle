@mod @mod_questionnaire
Feature: In questionnaire, personality tests can be constructed using feedback on specific question responses and questions can be
  assigned to multiple sections.
  In order to define a feedback questionnaire
  As a teacher
  I must add the required question types and complete the feedback options with more than one section per question.

  @javascript
  Scenario: Create a questionnaire with a with feeback question types and add more than one feedback section.
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
    And I navigate to "Questions" in current page administration
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
    And I add a "Rate (scale 1..5)" question and I fill the form with:
      | Question Name | Q5 |
      | Yes | y |
      | Nb of scale items | 5 |
      | Type of rate scale | Normal |
      | Question Text | Rate these |
      | Possible answers | 0=Zero,2=Two,4=Four,8=Eight,16=Sixteen,Clubs,Diamonds,Hearts,Spades |
    Then I should see "[Rate (scale 1..5)] (Q5)"
    And I follow "Advanced settings"
    And I should see "Feedback options"
    And I follow "Feedback options"
    And I set the field "id_feedbacksections" to "2 Feedback sections"
    And I set the field "id_feedbackscores" to "Yes"
    And I set the field "id_feedbacknotes" to "These are the main Feedback notes"
    And I press "Save settings and edit Feedback Sections"
    Then I should see "Sections:"
    And I should see "[Q1]"
    And I should see "[Q2]"
    And I should see "[Q3]"
    And I should see "[Q4]"
    And I should see "[Q5]"
    And I set the field "1_1" to "checked"
    And I set the field "1_2" to "checked"
    And I set the field "weightQ1_2" to "0.1"
    And I set the field "2_1" to "checked"
    And I set the field "2_2" to "checked"
    And I set the field "weightQ2_2" to "0.1"
    And I set the field "3_2" to "checked"
    And I set the field "4_2" to "checked"
    And I set the field "5_1" to "checked"
    And I press "Save Sections settings and edit Feedback Messages"
    And I should see "Feedback heading for section 1/2"
    And I set the field "id_sectionlabel" to "Section 1 label"
    And I set the field "id_sectionheading" to "Section 1 heading"
    And I set the field "id_feedbacktext_0" to "Feedback 1 100%"
    And I set the field "id_feedbackboundaries_0" to "50"
    And I set the field "id_feedbacktext_1" to "Feedback 1 50%"
    And I set the field "id_feedbackboundaries_1" to "20"
    And I set the field "id_feedbacktext_2" to "Feedback 1 20%"
    And I press "Next section (2/2)"
    And I should see "Feedback heading for section 2/2"
    And I set the field "id_sectionlabel" to "Section 2 label"
    And I set the field "id_sectionheading" to "Section 2 heading"
    And I set the field "id_feedbacktext_0" to "Feedback 2 100%"
    And I set the field "id_feedbackboundaries_0" to "50"
    And I set the field "id_feedbacktext_1" to "Feedback 2 50%"
    And I set the field "id_feedbackboundaries_1" to "20"
    And I set the field "id_feedbacktext_2" to "Feedback 2 20%"
    And I press "Save settings"
    And I log out

#  Scenario: Student completes feedback questions.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one dropdown"
    And I set the field "dropQ1" to "Three"
    And I click on "Three" "radio"
    And I click on "Choice Three for row Cheese" "radio"
    And I click on "Choice Three for row Bread" "radio"
    And I click on "Choice Three for row Meat" "radio"
    And I click on "Choice Three for row Fruit" "radio"
    And I click on "Yes" "radio"
    And I click on "Choice Two for row Clubs" "radio"
    And I click on "Choice Four for row Diamonds" "radio"
    And I click on "Choice Zero for row Hearts" "radio"
    And I click on "Choice Sixteen for row Spades" "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I follow "Continue"
    Then I should see "Your response"
    And I should see "These are the main Feedback notes"
    And I should see "Section 1 label"
    And I should see "39%"
    And I should see "Feedback 1 50%"
    And I should see "Section 2 label"
    And I should see "76%"
    And I should see "Feedback 2 100%"
    And I log out

#  Scenario: Another student completes feedback questions differently.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one dropdown"
    And I set the field "dropQ1" to "One"
    And I click on "One" "radio"
    And I click on "Choice Two for row Cheese" "radio"
    And I click on "Choice Two for row Bread" "radio"
    And I click on "Choice Two for row Meat" "radio"
    And I click on "Choice Two for row Fruit" "radio"
    And I click on "Yes" "radio"
    And I click on "Choice Zero for row Clubs" "radio"
    And I click on "Choice Two for row Diamonds" "radio"
    And I click on "Choice Four for row Hearts" "radio"
    And I click on "Choice Eight for row Spades" "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I follow "Continue"
    And I should see "These are the main Feedback notes"
    And I should see "Section 1 label"
    And I should see "22%"
    And I should see "Feedback 1 50%"
    And I should see "Section 2 label"
    And I should see "53%"
    And I should see "Feedback 2 100%"
    And I log out