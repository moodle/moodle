@report @javascript @report_lpmonitoring
Feature: Display learning plan template statistics in course modules
  As a learning plan appreciator
  In order to display statistics of competencies for a given template
  I need choose a template and view course modules competencies ratings statistics

  Background:
    Given course module competency grading is enabled
    And the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then the "jump" select box should contain "Statistics for learning plans"
    And I select "Statistics for learning plans" from the "jump" singleselect

  Scenario: Read template competencies statistics in course modules
    Given I open the autocomplete suggestions list
    And I should see "Medicine Year 1" item in the autocomplete list
    And I click on "Medicine Year 1" item in the autocomplete list
    And I click on "//label[contains(., 'Rating in activity')]" "xpath_element"
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I click on "Competency A" "link"
    And "Competency A" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency A" "dialogue"
    # Total number is based on number of students in each course, multiplied by number of activities associated to the competency.
    And I should see "10/27" in "incourse" of the competency "Competency A"
    And I should see "5" for "not good" in the row "2" of "Competency A" "incourse" rating
    And I should see "5" for "good" in the row "3" of "Competency A" "incourse" rating
    And I should see "7/26" in "incourse" of the competency "Competency B"
    And I should see "4" for "not qualified" in the row "2" of "Competency B" "incourse" rating
    And I should see "3" for "qualified" in the row "3" of "Competency B" "incourse" rating
