@report @javascript @report_lpmonitoring
Feature: Reset ratings for user competencies
  As a learning plan appreciator
  In order to manage drop outs
  I need to reset the rating for some user competencies

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: Reset rating for one competency
    Given I set the field "templateSelectorReport" to "Medicine Year 2"
    And I open the autocomplete suggestions list
    And I click on "Robert Smith" item in the autocomplete list
    And I press "Apply"
    And I should see "Not proficient" in "level" of the competency "Competency A"
    And I should see "Not proficient" in "level" of the competency "Competency B"
    And I toggle the "Competency A" detail
    And I wait "1" seconds
    And "//div[@class='reset-grade' and ancestor-or-self::div[contains(., 'Competency A')]]/a" "xpath_element" should be visible
    And I click on "//div[@class='reset-grade' and ancestor-or-self::div[contains(., 'Competency A')]]/a" "xpath_element"
    And "Reset" "dialogue" should be visible
    And I click on "Reset" "button" in the "Reset" "dialogue"
    And I should see "Not rated" in "level-proficiency" of the competency "Competency A"
    And "//div[@class='reset-grade' and ancestor-or-self::div[contains(., 'Competency A')]]/a" "xpath_element" should not be visible
    And I should see "Not proficient" in "level" of the competency "Competency B"

  Scenario: Reset rating for all competencies
    Given I set the field "templateSelectorReport" to "Medicine Year 2"
    And I open the autocomplete suggestions list
    And I click on "Robert Smith" item in the autocomplete list
    And I press "Apply"
    And I should see "Not proficient" in "level" of the competency "Competency A"
    And I should see "Not proficient" in "level" of the competency "Competency B"
    And "Reset all ratings" "link" should be visible
    And I click on "Reset all ratings" "link"
    And "Reset all ratings" "dialogue" should be visible
    And I click on "Reset" "button" in the "Reset all ratings" "dialogue"
    And I wait "1" seconds
    And "Reset all ratings" "link" should not be visible
    And I toggle the "Competency A" detail
    And I should see "Not rated" in "level-proficiency" of the competency "Competency A"
    And I toggle the "Competency B" detail
    And I should see "Not rated" in "level-proficiency" of the competency "Competency B"
