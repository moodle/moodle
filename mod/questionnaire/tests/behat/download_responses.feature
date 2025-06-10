@mod @mod_questionnaire
Feature: Questionnaire responses can be downloaded as a CSV, etc.

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
    And the following "activities" exist:
      | activity      | name                        | introduction                   | course | idnumber       |
      | questionnaire | Test questionnaire download | Test questionnaire description | C1     | questionnaire1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire download"
    And I navigate to "Questions" in current page administration
    And I add a "Text Box" question and I fill the form with:
      | Question Name    | Q1              |
      | No               | n               |
      | Input box length | 10              |
      | Max. text length | 15              |
      | Question Text    | Enter some text |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire download"
    And I navigate to "Answer the questions..." in current page administration
    And I set the field "Enter some text" to "Student response"
    And I press "Submit questionnaire"
    And I log out

  @javascript
  Scenario: Download responses
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire download"
    And I navigate to "View all responses" in current page administration
    Then I click on "Download" "link"
    # Without the ability to check the downloaded file, the absence of an
    # exception being thrown here is considered a success.
    And I click on "Download" "button"
