@core_customfield @customfield_number @javascript
Feature: Managers can manage course custom fields number
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and display number custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration

  Scenario: Create a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    When I set the following fields to these values:
      | Name               | Number field |
      | Short name         | numberfield  |
      | Display template   | test         |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    Then I should see "Invalid placeholder"
    And I set the following fields to these values:
      | Name               | Number field |
      | Short name         | numberfield  |
      | Display template   | {value}      |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I should see "Number field"
    And I log out

  Scenario: Edit a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    And I set the following fields to these values:
      | Name       | Number field |
      | Short name | numberfield  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    Then I should see "Number field"
    And I press "Edit custom field: Number field"
    And I set the following fields to these values:
      | Name | Edited number field |
    And I click on "Save changes" "button" in the "Updating Number field" "dialogue"
    Then I should see "Edited number field"
    And I log out

  Scenario: Delete a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    And I set the following fields to these values:
      | Name       | Number field |
      | Short name | numberfield  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I press "Delete custom field: Number field"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Number field" "text" does not exist
    Then I should not see "Number field"
    And I log out

  Scenario Outline: A number field must shown correctly on course listing
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    When I set the following fields to these values:
      | Name               | Test number |
      | Short name         | testnumber  |
      | Decimal places     | 2           |
      | Display template   | <template>  |
      | Display when zero  | <whenzero>  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test number | <fieldvalue> |
    And I press "Save and display"
    And I am on site homepage
    And I should see "Test number" in the ".customfields-container .customfieldname" "css_element"
    And I should see "<expectedvalue>" in the ".customfields-container .customfieldvalue" "css_element"
    Examples:
      | template  | whenzero    | fieldvalue | expectedvalue |
      | $ {value} | 0           | 150        | $ 150.00      |
      | {value}   | Free        | 0          | Free          |

  Scenario: Automatically populated field should hide some field form elements
    Given I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    And I should see "Default value"
    And I should see "Minimum value"
    And I should see "Maximum value"
    And I should see "Decimal places"
    And I should see "Display format"
    And I should see "Display template"
    And I should see "Display when zero"
    And I should see "Display template"
    And I should not see "No selection"

    When I set the following fields to these values:
      | Name       | Number field                        |
      | Field type | Number of activities in the course  |
    Then I should not see "Default value"
    And I should not see "Minimum value"
    And I should not see "Maximum value"
    And I should not see "Decimal places"
    And I should see "Display format"
    And I should not see "Display template"
    And I should see "Display when zero"
    And I should not see "Display when empty"
    And I should see "No selection"
    And I open the autocomplete suggestions list
    And "Assignment" "autocomplete_suggestions" should exist

  Scenario: Automatically populated field should be displayed in course settings
    Given the following "courses" exist:
      | fullname     | shortname   | category |
      | Test course1 | C1          | 0        |
      | Test course2 | C2          | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | 1        | teacher@example.com |
    And the following "course enrolments" exist:
      | user    | course  | role           |
      | teacher | C1      | editingteacher |
      | teacher | C2      | editingteacher |
    And the following "activities" exist:
      | activity | name         | intro            | course   | idnumber   | section | visible |
      | assign   | Assignment1  | Test description | C1       | assign1    | 1       | 0       |
      | assign   | Assignment2  | Test description | C1       | assign2    | 1       | 1       |
      | assign   | Assignment3  | Test description | C1       | assign3    | 1       | 1       |
      | quiz     | Quiz1        | Test description | C1       | quiz1      | 1       | 0       |
      | quiz     | Quiz2        | Test description | C1       | quiz2      | 1       | 1       |
      | forum    | Forum1       | Test description | C1       | forum1     | 1       | 0       |
      | forum    | Forum2       | Test description | C1       | forum2     | 1       | 1       |
      | forum    | Forum3       | Test description | C1       | forum3     | 1       | 1       |
      | forum    | Forum4       | Test description | C1       | forum4     | 1       | 1       |
      | quiz     | QuizC2       | Test description | C2       | quizC2     | 1       | 1       |
    Given I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    When I set the following fields to these values:
      | Name       | Number field                        |
      | Short name | numberfield                         |
      | Field type | Number of activities in the course  |
    And I open the autocomplete suggestions list
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I should see "You must supply a value here." in the "Adding a new Number" "dialogue"
    And I set the following fields to these values:
      | Activity types | Assignment, Forum |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I log in as "teacher"
    And I am on "C1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I click on "Update" "link"
    And I should see "5" in the ".customfield_number-recalculate-value" "css_element"
    And I add a assign activity to course "C1" section "1" and I fill the form with:
      | Assignment name                     | Assignment4        |
      | ID number                           | assign4            |
      | Description                         | Test description   |
    And I run the scheduled task "\customfield_number\task\cron"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should see "6" in the ".customfield_number-recalculate-value" "css_element"
    And I add a quiz activity to course "C1" section "1" and I fill the form with:
      | Name                                | Quiz3              |
      | ID number                           | quiz3              |
      | Description                         | Test description   |
    And I run the scheduled task "\customfield_number\task\cron"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should see "6" in the ".customfield_number-recalculate-value" "css_element"

    And I am on "C2" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should see "0" in the ".customfield_number-recalculate-value" "css_element"

    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I press "Edit custom field: Number field"
    And I set the following fields to these values:
      | Display when zero   |      |
    And I click on "Save changes" "button" in the "Updating Number field" "dialogue"
    And I run the scheduled task "\customfield_number\task\cron"
    And I log in as "teacher"
    And I am on "C2" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should not see "0" in the ".customfield_number-recalculate-value" "css_element"
