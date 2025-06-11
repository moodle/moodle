@report @javascript @report_lpmonitoring @report_lpmonitoring_for_user
Feature: Display learning plan user report
  As a student
  In order to display competencies ratings on learning plan
  I need to display rating by scale value and final rating

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "pablom"
    When I follow "Profile" in the user menu
    Then I should see "Monitoring of learning plans"
    And I follow "Monitoring of learning plans"
    And I should see "Monitoring of learning plans"

  Scenario: Read user competency detail
    Given the "studentPlansSelectorReport" select box should contain "Medicine Year 1"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan empty"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    When I press "Apply"
    Then I should see "Learning plan competencies: Medicine Year 1"
    And I should see "Competency B"
    And I toggle the "Competency A" detail
    And I should see "6/6" in "totalnbcourses" of the competency "Competency A"
    And I click on "totalnbcourses" of the competency "Competency A"
    And "Linked courses" "dialogue" should be visible
    And "Anatomy" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Genetic" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Psychology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Pharmacology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Pathology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Neuroscience" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I should see "1" in "listevidence" of the competency "Competency A"
    And I click on "listevidence" of the competency "Competency A"
    And "List of evidence" "dialogue" should be visible
    And I should see "My evidence" in the "List of evidence" "dialogue"
    And I click on "Close" "button" in the "List of evidence" "dialogue"
    And I should see "4" for "not good" in the row "1" of "Competency A" "incourse" rating
    And I should see "2" for "good" in the row "2" of "Competency A" "incourse" rating
    And I click on "4" for "not good" in the row "1" of "Competency A" "incourse" rating
    And "Linked courses" "dialogue" should be visible
    And "Anatomy" row "Comment" column of "coursesbyscalevalue" table should contain "1"
    And "Anatomy" row "Grade" column of "coursesbyscalevalue" table should contain "D+"
    And "Genetic" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Pathology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Neuroscience" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I click on "2" for "good" in the row "2" of "Competency A" "incourse" rating
    And "Linked courses" "dialogue" should be visible
    And "Psychology" row "Comment" column of "coursesbyscalevalue" table should contain "0"
    And "Psychology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Pharmacology" row "Comment" column of "coursesbyscalevalue" table should contain "0"
    And "Pharmacology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I should see "Not rated" in "level-proficiency" of the competency "Competency A"
    And I should see "0" in the ".count-stats" "css_element"
    And I set the field "studentPlansSelectorReport" to "Pablo learing plan"
    And I press "Apply"
    And I should see "Learning plan competencies: Pablo learing plan"
    And I should see "Competency A"
    And I should not see "Competency B"
    And I set the field "studentPlansSelectorReport" to "Pablo learing plan empty"
    And I press "Apply"
    And I should see "Learning plan competencies: Pablo learing plan empty"
    And I should see "No competencies have been linked to this learning plan."
