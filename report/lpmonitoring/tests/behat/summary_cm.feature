@report @javascript @report_lpmonitoring
Feature: Display learning plan summary
  As a learning plan appreciator
  I need to view course and activity competencies ratings by scale values

  Background:
    Given course module competency grading is enabled
    And the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: View the competency summary in courses, activities or both
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    When I open the autocomplete suggestions list
    Then I should see "Pablo Menendez" item in the autocomplete list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I click on "//ul/li/a[contains(@href, '#summary-content')]" "xpath_element"

    # Test with the first scale.
    And I set the field "Scale" to "Scale default"
    And I should see "Competency A"
    And I should see "Competency C"
    And I should not see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should see "not good"
    And I should not see "not qualified"
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarycourse')]" "xpath_element"
    And I should see "5" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "4" in "Competency A" row "not good" column of "summary-table" table
    And I should see "2" in "Competency A" row "good" column of "summary-table" table
    And I should see "1" in "Competency C" row "not good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarymodule')]" "xpath_element"
    And I should see "1" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "3" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "1" in "Competency A" row "not good" column of "summary-table" table
    And I should see "3" in "Competency A" row "good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summaryboth')]" "xpath_element"
    And I should see "6" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "5" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "5" in "Competency A" row "not good" column of "summary-table" table
    And I should see "5" in "Competency A" row "good" column of "summary-table" table
    And I should see "1" in "Competency C" row "not good" column of "summary-table" table

    # Test with a second scale.
    And I set the field "Scale" to "Scale specific"
    And I should not see "Competency A"
    And I should not see "Competency C"
    And I should see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should not see "not good"
    And I should see "not qualified"
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarycourse')]" "xpath_element"
    And I should see "3" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "qualified" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarymodule')]" "xpath_element"
    And I should see "2" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "1" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "1" in "Competency B" row "qualified" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summaryboth')]" "xpath_element"
    And I should see "5" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "3" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "5" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "qualified" column of "summary-table" table

  Scenario: View the competency summary for a plan with only level 1 competency
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])[last()]" to "Pablo"
    And I set the field "studentPlansSelectorReport" to "Pablo plan level 1 only"
    When I press "Apply"
    And I click on "//ul/li/a[contains(@href, '#summary-content')]" "xpath_element"
    Then I should see "Parent Competency"
    And I should not see "Competency A"
    And I should not see "Competency B"
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarycourse')]" "xpath_element"
    And I should see "1" in "Parent Competency" row "not good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarymodule')]" "xpath_element"
    And I should see "1" in "Parent Competency" row "good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summaryboth')]" "xpath_element"
    And I should see "1" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "1" in "Parent Competency" row "good" column of "summary-table" table

  Scenario: View the competency summary for a plan with competencies of level 1 and 2 both assessed
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    Given I click on "//div[contains(@class, 'studentfilter')]//span[contains(@class, 'form-autocomplete-downarrow')]" "xpath_element"
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
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarycourse')]" "xpath_element"
    And I should see "4 (+1)" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "4" in "Competency A" row "not good" column of "summary-table" table
    And I should see "2" in "Competency A" row "good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarymodule')]" "xpath_element"
    And I should see "1" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "3 (+1)" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "1" in "Competency A" row "not good" column of "summary-table" table
    And I should see "3" in "Competency A" row "good" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summaryboth')]" "xpath_element"
    And I should see "5 (+1)" in "Parent Competency" row "not good" column of "summary-table" table
    And I should see "5 (+1)" in "Parent Competency" row "good" column of "summary-table" table
    And I should see "5" in "Competency A" row "not good" column of "summary-table" table
    And I should see "5" in "Competency A" row "good" column of "summary-table" table

    # Test with a second scale.
    And I set the field "Scale" to "Scale specific"
    And I should not see "Competency A"
    And I should not see "Competency C"
    And I should see "Competency B"
    # Make sure data from the other scale are not shown.
    And I should not see "not good"
    And I should see "not qualified"
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarycourse')]" "xpath_element"
    And I should see "3" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "2" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "qualified" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summarymodule')]" "xpath_element"
    And I should see "2" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "1" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "2" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "1" in "Competency B" row "qualified" column of "summary-table" table
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'summaryboth')]" "xpath_element"
    And I should see "5" in "Parent Competency" row "not qualified" column of "summary-table" table
    And I should see "3" in "Parent Competency" row "qualified" column of "summary-table" table
    And I should see "5" in "Competency B" row "not qualified" column of "summary-table" table
    And I should see "3" in "Competency B" row "qualified" column of "summary-table" table
