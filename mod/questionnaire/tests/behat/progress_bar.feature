@mod @mod_questionnaire
Feature: Display a progress bar at the top of a questionnaire
When a student answers a questionnaire with multiple pages the progress bar will fill up as they go

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Progress bar should fill depending on progress through the pages
    Given the following "activities" exist:
      | activity      | name               | description                    | course | idnumber       | resume | navigate | progressbar |
      | questionnaire | Test questionnaire | Test questionnaire description | C1     | questionnaire0 | 1      | 1        | 1           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Yes/No" question and I fill the form with:
      | Question Name | Q1                     |
      | Yes           | y                      |
      | Question Text | Do you like questions? |
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q2            |
      | No            | y             |
      | Question Text | Are you sure? |
    And I add a "Yes/No" question and I fill the form with:
      | Question Name            | Q3                                         |
      | Yes                      | y                                          |
      | Question Text            | Would you like to answer another question? |
      | id_dependquestions_and_0 | Q1->Yes                                    |
    And I add a "Yes/No" question and I fill the form with:
      | Question Name            | Q4                        |
      | Yes                      | y                         |
      | Question Text            | Is that enough questions? |
      | id_dependquestions_and_0 | Q3->Yes                   |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    Then I should see "0%" in the "#questionnaire-progressbar-percent" "css_element"
    And I click on "Yes" "radio"
    When I press "Next Page >>"
    Then I should see "50%" in the "#questionnaire-progressbar-percent" "css_element"
    And I click on "Yes" "radio"
    When I press "Next Page >>"
    Then I should see "75%" in the "#questionnaire-progressbar-percent" "css_element"
    When I press "<< Previous Page"
    Then I should see "50%" in the "#questionnaire-progressbar-percent" "css_element"

  Scenario: Progress bar should not display on a single page questionnaire
    Given the following "activities" exist:
      | activity      | name               | description                    | course | idnumber       | resume | navigate | progressbar |
      | questionnaire | Test questionnaire | Test questionnaire description | C1     | questionnaire0 | 1      | 1        | 1           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Yes/No" question and I fill the form with:
      | Yes           | y                      |
      | Question Text | Do you like questions? |
    And I add a "Yes/No" question and I fill the form with:
      | Yes           | y                             |
      | Question Text | Do you really like questions? |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    Then ".questionnaire-progressbar" "css_element" should not exist

  Scenario: Progress bar should not display if turned off in settings
    Given the following "activities" exist:
      | activity      | name               | description                    | course | idnumber       | resume | navigate | progressbar |
      | questionnaire | Test questionnaire | Test questionnaire description | C1     | questionnaire0 | 1      | 1        | 0           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Yes/No" question and I fill the form with:
      | Question Name | Q1                     |
      | Yes           | y                      |
      | Question Text | Do you like questions? |
    And I add a "Yes/No" question and I fill the form with:
      | Question Name            | Q2                            |
      | Yes                      | y                             |
      | Question Text            | Do you really like questions? |
      | id_dependquestions_and_0 | Q1->Yes                       |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    When I navigate to "Answer the questions..." in current page administration
    Then ".questionnaire-progressbar" "css_element" should not exist
