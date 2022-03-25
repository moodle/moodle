@report @javascript @report_competency
Feature: See the competencies for an activity
  As a competency grader
  In order to perform mark all competencies for an activity
  I need to see the competencies linked to one activity in the breakdown report.

  Background:
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Test-Framework | ID-FW1 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | Test-Comp1 | ID-FW1 |
      | Test-Comp2 | ID-FW1 |
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname    | email                | idnumber | middlename | alternatename | firstnamephonetic | lastnamephonetic |
      | student1 | Grainne   | Beauchamp   | student1@example.com | s1       | Ann        | Jill          | Gronya            | Beecham          |
      | student2 | Niamh     | Cholmondely | student2@example.com | s2       | Jane       | Nina          | Nee               | Chumlee          |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    |
    And the following config values are set as admin:
      | fullnamedisplay | firstname |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Competencies" in current page administration
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I am on the PageName1 "page activity editing" page
    And I follow "Expand all"
    And I set the field "Course competencies" to "Test-Comp1"
    And I press "Save and return to course"

  @javascript
  Scenario: Go to the competency breakdown report
    When I navigate to "Reports" in current page administration
    And I click on "Competency breakdown" "link"
    And I set the field "Filter competencies by resource or activity" to "PageName1"
    Then I should see "Test-Comp1"
    And I should not see "Test-Comp2"
    And I should see "Ann, Jill, Grainne, Beauchamp"
    And I should see "Ann, Jill, Grainne, Beauchamp" in the ".form-autocomplete-selection" "css_element"
    And I open the autocomplete suggestions list
    And I should see "Jane, Nina, Niamh, Cholmondely" in the ".form-autocomplete-suggestions" "css_element"
    And I click on "Not rated" "link"
    And I click on "Rate" "button"
    And I set the field "Rating" to "A"
    And I click on "Rate" "button" in the ".competency-grader" "css_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "PageName1" "autocomplete_selection"
    And I should see "Test-Comp1"
    And I should see "Test-Comp2"
