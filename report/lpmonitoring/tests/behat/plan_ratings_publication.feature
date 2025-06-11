@report @javascript @report_lpmonitoring
Feature: Manage publication of ratings in learning plans
  As a learning plan admin
  In order to hide/show ratings for learning plans
  I need to manage ratings publication in learning plans and templates

  Background:
    Given hide competency rating is enabled
    And the lpmonitoring fixtures exist
    And I log in as "lpmanager"
    And I am on course index
    When I follow "Medicine"
    And I select "More" from secondary navigation
    Then I should see "Learning plan templates"
    And I follow "Learning plan templates"

  Scenario: Manage learning plan ratings publication [template level]
    # Template level
    Given I click on "Hide ratings for this template" of edit menu in the "Medicine Year 1" row
    And I log out
    And I log in as "appreciator"
    And I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "Rebecca Armenta"
    And the "Ratings display" "checkbox" should not be checked
    And I toggle the "Competency B" detail
    And I wait "1" seconds
    And I click on "rate-competency" of the competency "Competency B"
    And I set the field with xpath "//select[@name='rating']" to "not qualified"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    # Login as student
    And I log out
    And I log in as "rebeccaa"
    When I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    Then I should see "0/3" in the ".proficient-stats" "css_element"
    And I should see "0" in the ".notproficient-stats" "css_element"
    And I should see "3" in the ".notrated-stats" "css_element"
    And I should see "Not rated" in "level" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not rated" in "level-proficiency" of the competency "Competency B"
    And I click on "//a[contains(., 'Competency B')]" "xpath_element"
    And I should see "-" dd in "Proficient" dt
    And I should see "-" dd in "Rating" dt
    And I should see "No evidence" dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Medicine Year 1"
    And I should see "-" in "Competency B" row "Rating" column of "managecompetencies" table
    And I should see "-" in "Competency B" row "Proficient" column of "managecompetencies" table
    And I log out
    # Login as learning plan admin
    And I log in as "lpmanager"
    And I am on the "Medicine" "Category" page
    And I select "More" from secondary navigation
    And I should see "Learning plan templates"
    And I follow "Learning plan templates"
    And I click on "Display ratings for this template" of edit menu in the "Medicine Year 1" row
    And I log out
    # Login as student
    And I log in as "rebeccaa"
    And I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "0/3" in the ".proficient-stats" "css_element"
    And I should see "1" in the ".notproficient-stats" "css_element"
    And I should see "2" in the ".notrated-stats" "css_element"
    And I should see "Not proficient" in "level" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    And I click on "//a[contains(., 'Competency B')]" "xpath_element"
    And I should see "No" dd in "Proficient" dt
    And I should see "not qualified" dd in "Rating" dt
    And I should see "The competency rating was manually set in the learning plan 'Medicine Year 1'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Medicine Year 1"
    And I should see "not qualified" in "Competency B" row "Rating" column of "managecompetencies" table
    And I should see "No" in "Competency B" row "Proficient" column of "managecompetencies" table

  Scenario: Manage learning plan ratings publication [learning plan level]
    Given I click on "Hide ratings for this template" of edit menu in the "Medicine Year 1" row
    And I log out
    And I log in as "appreciator"
    And I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "Rebecca Armenta"
    And the "Ratings display" "checkbox" should not be checked
    And I toggle the "Competency B" detail
    And I wait "1" seconds
    And I click on "rate-competency" of the competency "Competency B"
    And I set the field with xpath "//select[@name='rating']" to "not qualified"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    And the "Ratings display" "checkbox" should not be checked
    When I click on "Ratings display" "checkbox"
    And I should see "Reset" in the ".displayratings" "css_element"
    And I log out
    # Login as student
    And I log in as "rebeccaa"
    And I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "0/3" in the ".proficient-stats" "css_element"
    And I should see "1" in the ".notproficient-stats" "css_element"
    And I should see "2" in the ".notrated-stats" "css_element"
    And I should see "Not proficient" in "level" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    And I click on "//a[contains(., 'Competency B')]" "xpath_element"
    And I should see "No" dd in "Proficient" dt
    And I should see "not qualified" dd in "Rating" dt
    And I should see "The competency rating was manually set in the learning plan 'Medicine Year 1'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Medicine Year 1"
    And I should see "not qualified" in "Competency B" row "Rating" column of "managecompetencies" table
    And I should see "No" in "Competency B" row "Proficient" column of "managecompetencies" table

  Scenario: Manage learning plan ratings publication [reset display rating in learning plan]
    Given I log out
    And I log in as "appreciator"
    And I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "Rebecca Armenta"
    And the "Ratings display" "checkbox" should be checked
    And I toggle the "Competency B" detail
    And I wait "1" seconds
    And I click on "rate-competency" of the competency "Competency B"
    And I set the field with xpath "//select[@name='rating']" to "not qualified"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    When I click on "Ratings display" "checkbox"
    And I should see "Reset" in the ".displayratings" "css_element"
    And I log out
    # Login as student
    And I log in as "rebeccaa"
    When I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    Then I should see "0/3" in the ".proficient-stats" "css_element"
    And I should see "0" in the ".notproficient-stats" "css_element"
    And I should see "3" in the ".notrated-stats" "css_element"
    And I should see "Not rated" in "level" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not rated" in "level-proficiency" of the competency "Competency B"
    And I click on "//a[contains(., 'Competency B')]" "xpath_element"
    And I should see "-" dd in "Proficient" dt
    And I should see "-" dd in "Rating" dt
    And I should see "No evidence" dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Medicine Year 1"
    And I should see "-" in "Competency B" row "Rating" column of "managecompetencies" table
    And I should see "-" in "Competency B" row "Proficient" column of "managecompetencies" table
    And I log out
    # Login as learning plan admin
    And I log in as "appreciator"
    And I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "Rebecca Armenta"
    And I click on ".stats-display-rating .resetdisplayrating a" "css_element"
    And the "Ratings display" "checkbox" should not be checked
    And I should not see "Reset" in the ".displayratings" "css_element"
    And I log out
    # Login as student
    And I log in as "rebeccaa"
    And I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "0/3" in the ".proficient-stats" "css_element"
    And I should see "1" in the ".notproficient-stats" "css_element"
    And I should see "2" in the ".notrated-stats" "css_element"
    And I should see "Not proficient" in "level" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    And I click on "//a[contains(., 'Competency B')]" "xpath_element"
    And I should see "No" dd in "Proficient" dt
    And I should see "not qualified" dd in "Rating" dt
    And I should see "The competency rating was manually set in the learning plan 'Medicine Year 1'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Medicine Year 1"
    And I should see "not qualified" in "Competency B" row "Rating" column of "managecompetencies" table
    And I should see "No" in "Competency B" row "Proficient" column of "managecompetencies" table
