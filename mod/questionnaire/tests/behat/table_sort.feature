@mod @mod_questionnaire
Feature: In questionnaire, Teacher should be able to sort Text box responses on date and username.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Wendi     | Blake    | student1@example.com |
      | student2 | Jim       | Lai      | student2@example.com |
      | student3 | Stephan   | Parker   | student3@example.com |
      | student4 | Bobby     | Anderson | student4@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
    And the following "activities" exist:
      | activity      | name               | description                    | course | idnumber       | resume | navigate | progressbar |
      | questionnaire | Test questionnaire | Test questionnaire description | C1     | questionnaire0 | 1      | 1        | 1           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Text Box" question and I fill the form with:
      | Question Name | Q1 |
      | Yes           | y |
      | Question Text | Enter testbox question |
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q2            |
      | No            | y             |
      | Question Text | Enter a number |
    And I add a "Yes/No" question and I fill the form with:
      | Question Name            | Q3                                         |
      | Yes                      | y                                          |
      | Question Text            | Would you like to answer another question? |
    And I log out

  @javascript
  Scenario: No sorting for table with single row
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter testbox question" to "Test 1"
    And I set the field "Enter a number" to "2"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    And I press "Continue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "View all responses" in current page administration
    And "(//table[string-length(@id) > 1]//thead//th[2]//span[contains(@class,'icon-container-desc')])[1]" "xpath_element" should not exist
    And "(//table[string-length(@id) > 1]//thead//th[2]//span[contains(@class,'icon-container-asc')])[1]" "xpath_element" should not exist

  @javascript
  Scenario: Sort the table with multiple row
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter testbox question" to "Test 1"
    And I set the field "Enter a number" to "2"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    And I press "Continue"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter testbox question" to "StuTest 2"
    And I set the field "Enter a number" to "1"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    And I press "Continue"
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter testbox question" to "Test 3"
    And I set the field "Enter a number" to "3"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    And I press "Continue"
    And I log out
    And I log in as "student4"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter testbox question" to "Test 4"
    And I set the field "Enter a number" to "4"
    And I click on "Yes" "radio"
    And I press "Submit questionnaire"
    And I press "Continue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "View all responses" in current page administration
    And I click on "(//table[string-length(@id) > 1]//thead//th[contains(@class, 'c0 qn-handcursor')])[1]" "xpath_element"
    And "(//table[string-length(@id) > 1]//thead//th[contains(@class, 'asc')])[1]" "xpath_element" should exist
    And I should see "Bobby Anderson" in the "(//table[string-length(@id) > 1]//tbody//tr[1]//td[1])[1]" "xpath_element"
    And I should see "Jim Lai" in the "(//table[string-length(@id) > 1]//tbody//tr[2]//td[1])[1]" "xpath_element"
    And I should see "Stephan Parker" in the "(//table[string-length(@id) > 1]//tbody//tr[3]//td[1])[1]" "xpath_element"
    And I should see "Wendi Blake" in the "(//table[string-length(@id) > 1]//tbody//tr[4]//td[1])[1]" "xpath_element"
    And I click on "(//table[string-length(@id) > 1]//thead//th[contains(@class, 'c0 qn-handcursor')])[1]" "xpath_element"
    And "(//table[string-length(@id) > 1]//thead//th[contains(@class, 'desc')])[1]" "xpath_element" should exist
    And I should see "Wendi Blake" in the "(//table[string-length(@id) > 1]//tbody//tr[1]//td[1])[1]" "xpath_element"
    And I should see "Stephan Parker" in the "(//table[string-length(@id) > 1]//tbody//tr[2]//td[1])[1]" "xpath_element"
    And I should see "Jim Lai" in the "(//table[string-length(@id) > 1]//tbody//tr[3]//td[1])[1]" "xpath_element"
    And I should see "Bobby Anderson" in the "(//table[string-length(@id) > 1]//tbody//tr[4]//td[1])[1]" "xpath_element"
