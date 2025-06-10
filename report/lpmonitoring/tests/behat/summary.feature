@report @javascript @report_lpmonitoring
Feature: Display learning plan summary
  As a learning plan appreciator
  I need to view course competencies ratings by scale values

  Background:
    Given course module competency grading is not enabled
    And the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: View the competency summary in courses
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    When I open the autocomplete suggestions list
    Then I should see "Pablo Menendez" item in the autocomplete list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I click on "//ul/li/a[contains(@href, '#summary-content')]" "xpath_element"
    And I set the field "Scale" to "Scale default"
    And I should see "Competency A"
    And I should see "Competency C"
    And I should not see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should see "not good"
    And I should not see "not qualified"
    And I click on "//div[contains(@id, 'summary-content')]//td//a[contains(., 'Competency A')]" "xpath_element"
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "5" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "4" in "Competency A" row "not good" column of "summary-table" table
    And I should see "2" in "Competency A" row "good" column of "summary-table" table
    And I should see "1" in "Competency C" row "not good" column of "summary-table" table
    And I set the field "Scale" to "Scale specific"
    And I should not see "Competency A"
    And I should not see "Competency C"
    And I should see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should not see "not good"
    And I should see "not qualified"
    And I should see "3" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "qualified" column of "summary-table" table

  Scenario: View the competency summary for a plan with only level 1 competency
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    And I click on ".studentfilter .form-autocomplete-downarrow" "css_element"
    And I click on "Pablo Menendez" item in the autocomplete list
    And I set the field "studentPlansSelectorReport" to "Pablo plan level 1 only"
    When I press "Apply"
    And I click on "//ul/li/a[contains(@href, '#summary-content')]" "xpath_element"
    Then I should see "Parent Competency"
    And I should not see "Competency A"
    And I should not see "Competency B"
    And I should see "1" in "Parent Competency" row "not good" column of "summary-table" table

  Scenario: View the competency summary for a plan with competencies of level 1 and 2 both assessed
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    And I click on ".studentfilter .form-autocomplete-downarrow" "css_element"
    And I click on "Pablo Menendez" item in the autocomplete list
    And I set the field "studentPlansSelectorReport" to "Pablo plan level 1 and 2"
    When I press "Apply"
    And I click on "//ul/li/a[contains(@href, '#summary-content')]" "xpath_element"

    # Test with the first scale.
    And I set the field "Scale" to "Scale default"
    And I should see "Competency A"
    And I should not see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should see "not good"
    And I should not see "not qualified"
    And I should see "4 (+1)" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "4" in "Competency A" row "not good" column of "summary-table" table
    And I should see "2" in "Competency A" row "good" column of "summary-table" table

    # Test with a second scale.
    And I set the field "Scale" to "Scale specific"
    And I should not see "Competency A"
    And I should not see "Competency C"
    And I should see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should not see "not good"
    And I should see "not qualified"
    And I should see "3" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "qualified" column of "summary-table" table
