@mod @mod_questionnaire
Feature: Questionnaires can use an existing public survey to gather responses in one place.
  When a student answers the same public questionnaire in two different course instances
  The answers will be visible only in those course instances.

  Background: Add a public questionnaire and use it in two different course.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
      | Course 3 | C3 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | manager |
      | teacher1 | C2 | manager |
      | student1 | C2 | student |
      | teacher1 | C3 | manager |
      | student1 | C3 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Public questionnaire | Anonymous questionnaire description | C1 | questionnaire0 |

  @javascript
  Scenario: Public questionnaire instances have responses visible in their respective courses
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Public questionnaire"
    And I navigate to "Advanced settings" in current page administration
    And I set the field "realm" to "public"
    And I press "Save and display"
    And I navigate to "Questions" in current page administration
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Question Text | Enter a number |

    And I add a questionnaire activity to course "Course 2" section "1" and I fill the form with:
      | Name | Questionnaire instance 1 |
      | Description | Description |
      | Use public | Public questionnaire [Course 1] |
    Then I should see "Questionnaire instance 1"

    And I add a questionnaire activity to course "Course 3" section "1" and I fill the form with:
      | Name | Questionnaire instance 2 |
      | Description | Description |
      | Use public | Public questionnaire [Course 1] |
    Then I should see "Questionnaire instance 2"
    And I log out

    And I log in as "student1"
    And I am on "Course 2" course homepage
    And I follow "Questionnaire instance 1"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Questionnaire instance 1"
    And I set the field "Enter a number" to "1"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    Then I should see "View your response(s)"
    And I should see "Enter a number"
    And "//div[contains(@class,'questionnaire_numeric') and contains(@class,'questionnaire_response')]//span[@class='selected' and text()='1']" "xpath_element" should exist

    And I am on "Course 3" course homepage
    And I follow "Questionnaire instance 2"
    And I should see "Answer the questions..."
    And I should not see "View your response(s)"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Questionnaire instance 2"
    And I set the field "Enter a number" to "2"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I press "Continue"
    Then I should see "View your response(s)"
    And I should see "Enter a number"
    And "//div[contains(@class,'questionnaire_numeric') and contains(@class,'questionnaire_response')]//span[@class='selected' and text()='2']" "xpath_element" should exist

    And I am on "Course 2" course homepage
    And I follow "Questionnaire instance 1"
    Then I should see "View your response(s)"
    And I navigate to "View your response(s)" in current page administration
    And I should see "Enter a number"
    And "//div[contains(@class,'questionnaire_numeric') and contains(@class,'questionnaire_response')]//span[@class='selected' and text()='1']" "xpath_element" should exist

    And I am on "Course 3" course homepage
    And I follow "Questionnaire instance 2"
    Then I should see "View your response(s)"
    And I navigate to "View your response(s)" in current page administration
    And I should see "Enter a number"
    And "//div[contains(@class,'questionnaire_numeric') and contains(@class,'questionnaire_response')]//span[@class='selected' and text()='2']" "xpath_element" should exist
    And I log out
