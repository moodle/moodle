@report @javascript @report_lpmonitoring
Feature: Display learning plan ratings details
  As a learning plan appreciator
  In order to rate competencies on learning plan
  I need to view course competencies ratings

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])" to "Pablo"
    Then ".userplan-fullname span" "css_element" should exist
    Then I should see "Pablo Menendez"
    And I press "Apply"

  Scenario: View the competency report in courses
    Given I click on "//ul/li/a[contains(@href, '#report-content')]" "xpath_element"
    When I click on "//td[contains(@class, 'searchable')]/a[contains(., 'Competency A')]" "xpath_element"
    Then "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I click on "Close" "button" in the "User competency summary" "dialogue"

    # First verification of the evidences or else it raises an exception.
    And I click on "//tr[contains(@class, 'odd')]/td[contains(@class, 'searchable')][2]//a[contains(@class, 'listevidence')]" "xpath_element"
    And "List of evidence" "dialogue" should be visible
    And I click on "Close" "button" in the "List of evidence" "dialogue"
    And I should see "not good" in the "//tr[contains(@class, 'odd')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And I click on "//tr[contains(@class, 'odd')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "not good" dd in "Rating" dt
    And I should see "The competency rating was manually set in the course 'Course: Genetic'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "not qualified" in the "//tr[contains(@class, 'even')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And I click on "//tr[contains(@class, 'even')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency B" in the "User competency summary" "dialogue"
    And I should see "not qualified" dd in "Rating" dt
    And I should see "The competency rating was manually set in the course 'Course: Genetic'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"

    # We double check with an other User.
    And I open the autocomplete suggestions list
    And I click on "Donald Fletcher" item in the autocomplete list
    And I should see "good" in the "//tr[contains(@class, 'odd')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And I click on "//tr[contains(@class, 'odd')]/td[contains(@class, 'course-cell')][2]//a" "xpath_element"
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the ".competency-heading" "css_element"
    And I should see "good" dd in "Rating" dt
    And I should see "The competency rating was manually set in the course 'Course: Genetic'." dd in "Evidence" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"

  Scenario: Check with a course hidden for students
    Given I click on "//ul/li/a[contains(@href, '#report-content')]" "xpath_element"
    When I set the field with xpath "(//input[contains(@id, 'table-search-columns')])" to "Psycho"
    Then I should see "good" in "Competency A" row "Psychology" column of "main-table" table
    And I click on "//tr[contains(., 'Competency A')]//td[contains(@class, 'course-cell') and not(contains(@class, 'filtersearchhidden'))]//a" "xpath_element"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the ".competency-heading" "css_element"
    And I should see "good" dd in "Rating" dt
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    # Check the course is correctly hidden
    And I am on the "Medicine" "Category" page
    And I should see "Genetic"
    And I should not see "Psychology"

  Scenario: Filter the data table on competencies and courses.
    Given I click on "//ul/li/a[contains(@href, '#report-content')]" "xpath_element"
    # No search : we see both competencies, for some courses.
    And "//tr/td[contains(., 'Competency A')]" "xpath_element" should be visible
    And "//tr/td[contains(., 'Competency B')]" "xpath_element" should be visible
    And "//th[contains(@class, 'course-cell') and contains(., 'Pathology')]" "xpath_element" should be visible
    And "//th[contains(@class, 'course-cell') and contains(., 'Neuroscience')]" "xpath_element" should be visible
    And I should see "not qualified" in "Competency B" row "Pathology" column of "main-table" table
    # We search for "Competency B" only : Competency A is hidden.
    When I set the field with xpath "(//input[contains(@id, 'table-search-competency')])" to "Competency B"
    Then "//tr/td[contains(., 'Competency A')]" "xpath_element" should not be visible
    And "//tr/td[contains(., 'Competency B')]" "xpath_element" should be visible
    # We search for "Patho" in courses : the columns for other courses are hidden (we don't check activities in this test).
    And I set the field with xpath "(//input[contains(@id, 'table-search-columns')])" to "Patho"
    And "//th[contains(@class, 'course-cell') and contains(., 'Pathology')]" "xpath_element" should be visible
    And "//th[contains(@class, 'course-cell') and contains(., 'Neuroscience')]" "xpath_element" should not be visible
    And I should see "not qualified" in "Competency B" row "Pathology" column of "main-table" table

  Scenario: Filter the data table by scale values
    Given I click on "//ul/li/a[contains(@href, '#report-content')]" "xpath_element"
    # No filter selected : both competencies should be visible.
    And "//tr/td[contains(., 'Competency A')]" "xpath_element" should be visible
    And "//tr/td[contains(., 'Competency B')]" "xpath_element" should be visible
    # We check that the correct values are in the "Filter by scale value" dropdown.
    And the "scale-filter-report" select box should contain "good"
    And the "scale-filter-report" select box should contain "not good"
    And the "scale-filter-report" select box should contain "not qualified"
    And the "scale-filter-report" select box should contain "Not rated"
    And the "scale-filter-report" select box should contain "qualified"
    # We filter with different values and check which competencies should be visible.
    When I set the field "scale-filter-report" to "not good"
    Then "//tr/td[contains(., 'Competency A')]" "xpath_element" should be visible
    And "//tr/td[contains(., 'Competency B')]" "xpath_element" should not be visible
    And I set the field "scale-filter-report" to "not qualified"
    And "//tr/td[contains(., 'Competency A')]" "xpath_element" should not be visible
    And "//tr/td[contains(., 'Competency B')]" "xpath_element" should be visible
    # We combine with a Competency search.
    And I set the field with xpath "(//input[contains(@id, 'table-search-competency')])" to "Competency A"
    And I should see "No matching records found"
