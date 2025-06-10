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
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "Test questionnaire" "mod_questionnaire > questions" page logged in as "teacher1"
    Then I should see "Add questions"
    And I add a "Dropdown Box" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Question Text | Select one dropdown |
      | Possible answers | 1=One,2=Two,3=<span lang="en" class="multilang">Three</span>,4=Four |
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
      | Possible answers | Cheese,Bread,Meat,Fruit |
      | Named degrees    | 1=One,2=Two,3=Three,4=Four |
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
      | Possible answers | Clubs,Diamonds,Hearts,Spades |
      | Named degrees    | 0=Zero,2=Two,4=Four,8=Eight,16=Sixteen |
    Then I should see "[Rate (scale 1..5)] (Q5)"
    And I follow "Feedback"
    And I should see "Feedback options"
    And I set the field "id_feedbacksections" to "Feedback sections"
    And I set the field "id_feedbackscores" to "Yes"
    And I set the field "id_feedbacknotes" to "These are the main Feedback notes"
    And I press "Save settings and edit Feedback Sections"
    Then I should see "[New section] section questions"
    And I follow "[New section] section questions"
    And I set the field "addquestionselect" to "Q1"
    And I press "Add question..."
    And I set the field "addquestionselect" to "Q2"
    And I press "Add question..."
    And I set the field "addquestionselect" to "Q5"
    And I press "Add question..."
    And I set the field "id_sectionlabel" to "Section 1 label"
    And I set the field "id_sectionheading" to "Section 1 heading"
    And I follow "[New section] section messages"
    And I set the field "id_feedbacktext_0" to "Feedback 1 100%"
    And I set the field "id_feedbackboundaries_0" to "50"
    And I set the field "id_feedbacktext_1" to "Feedback 1 50%"
    And I set the field "id_feedbackboundaries_1" to "20"
    And I set the field "id_feedbacktext_2" to "Feedback 1 20%"
    And I press "Save changes"
    And I set the field "newsectionlabel" to "Section 2 label"
    And I press "Add new section"
    Then I should see "Section 2 label section questions"
    And I follow "Section 2 label section questions"
    And I set the field "addquestionselect" to "Q1"
    And I press "Add question..."
    And I set the field "weight1" to "0.1"
    And I set the field "addquestionselect" to "Q2"
    And I press "Add question..."
    And I set the field "weight1" to "0.1"
    And I set the field "addquestionselect" to "Q3"
    And I press "Add question..."
    And I set the field "addquestionselect" to "Q4"
    And I press "Add question..."
    And I set the field "id_sectionheading" to "Section 2 heading"
    And I follow "Section 2 label section messages"
    And I set the field "id_feedbacktext_0" to "Feedback 2 100%"
    And I set the field "id_feedbackboundaries_0" to "50"
    And I set the field "id_feedbacktext_1" to "Feedback 2 50%"
    And I set the field "id_feedbackboundaries_1" to "20"
    And I set the field "id_feedbacktext_2" to "Feedback 2 20%"
    And I press "Save changes"
    And I log out

#  Scenario: Student completes feedback questions.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one dropdown"
    And I set the field "dropQ1" to "Three"
    Then I should not see "<span lang=\"en\" class=\"multilang\">Three</span>" in the "//select[@id='dropQ1']//option[4]" "xpath_element"
    And I click on "Three" "radio"
    And I click on "Row 2, Cheese: Column 5, Three." "radio"
    And I click on "Row 3, Bread: Column 5, Three." "radio"
    And I click on "Row 4, Meat: Column 5, Three." "radio"
    And I click on "Row 5, Fruit: Column 5, Three." "radio"
    And I click on "Yes" "radio"
    And I click on "Row 2, Clubs: Column 4, Two." "radio"
    And I click on "Row 3, Diamonds: Column 5, Four." "radio"
    And I click on "Row 4, Hearts: Column 3, Zero." "radio"
    And I click on "Row 5, Spades: Column 7, Sixteen." "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    Then I should see "View your response(s)"
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
    And I click on "Row 2, Cheese: Column 4, Two." "radio"
    And I click on "Row 3, Bread: Column 4, Two." "radio"
    And I click on "Row 4, Meat: Column 4, Two." "radio"
    And I click on "Row 5, Fruit: Column 4, Two." "radio"
    And I click on "Yes" "radio"
    And I click on "Row 2, Clubs: Column 3, Zero." "radio"
    And I click on "Row 3, Diamonds: Column 4, Two." "radio"
    And I click on "Row 4, Hearts: Column 5, Four." "radio"
    And I click on "Row 5, Spades: Column 6, Eight." "radio"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    And I should see "These are the main Feedback notes"
    And I should see "Section 1 label"
    And I should see "22%"
    And I should see "Feedback 1 50%"
    And I should see "Section 2 label"
    And I should see "53%"
    And I should see "Feedback 2 100%"
    And I log out
