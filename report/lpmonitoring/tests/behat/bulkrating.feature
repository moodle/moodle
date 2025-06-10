@report @javascript @report_lpmonitoring
Feature: Bulk rating
  As a learning plan appreciator
  In order to rate competencies on learning plans rapidly
  I need to rate all students at the same time

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Bulk rating for all students for all competencies"
    # Rate Pablo.
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I toggle the "Competency A" detail
    And I click on "rate-competency" of the competency "Competency A"
    And "Rate" "dialogue" should be visible
    And I set the field with xpath "//select[@name='rating']" to "not good"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    # Go to bulk rating page.
    And I am on course index
    And I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    And I should see "Bulk rating for all students for all competencies"
    And I follow "Bulk rating for all students for all competencies"

  Scenario: Bulk rate students, without the "force" option
    Given I set the field "templateSelector" to "Medicine Year 1"
    # Competency A : Verify that the default option ('not good') is actually checked and check 'good' instead.
    Then I should see "Competency A" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table/caption" "xpath_element"
    And I should see "not good" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[1]" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[2]/input[@checked]" "xpath_element" should exist
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[2]/td[2]/input" "xpath_element"
    # Competency B : Check the 'Do not bulk rate this competency' checkbox.
    And I should see "Competency B" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]/table/caption" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]//div[contains(@class, 'donotapplybulk')]//input[@checked]" "xpath_element" should not exist
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]//div[contains(@class, 'donotapplybulk')]//input" "xpath_element"
    # Save the task.
    And I click on "Save" "button"
    And I should see "Evaluations will be executed soon"
    And I set the field "templateSelector" to "Medicine Year 2"
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    And I set the field "templateSelector" to "Medicine Year 1"
    And I should see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be disabled
    # Run the task and check the messages don't appear anymore.
    And I run all adhoc tasks
    And I set the field "templateSelector" to "Medicine Year 2"
    And I set the field "templateSelector" to "Medicine Year 1"
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    # Check that students have been rated, except Pablo (who was already rated).
    And I am on course index
    And I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I toggle the "Competency A" detail
    And I should see "not good" in "finalrate" of the competency "Competency A"
    And I click on ".prevplan" "css_element"
    And I toggle the "Competency A" detail
    And I should see "good" in "finalrate" of the competency "Competency A"
    And I click on ".prevplan" "css_element"
    And I toggle the "Competency A" detail
    And I should see "good" in "finalrate" of the competency "Competency A"

  Scenario: Bulk rate students, with the "force" option
    Given I set the field "templateSelector" to "Medicine Year 1"
    # Competency A : Check 'good'.
    When I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[2]/td[2]/input" "xpath_element"
    # Competency B : Check the 'Do not bulk rate this competency' checkbox.
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]//div[contains(@class, 'donotapplybulk')]//input" "xpath_element"
    # Save the task and run it.
    And I click on "Save" "button"
    Then I should see "Evaluations will be executed soon"
    And I run all adhoc tasks
    # Check that students have been rated, including Pablo (who was already rated).
    And I am on course index
    And I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I toggle the "Competency A" detail
    And I should see "good" in "finalrate" of the competency "Competency A"
    And I click on ".prevplan" "css_element"
    And I toggle the "Competency A" detail
    And I should see "good" in "finalrate" of the competency "Competency A"
    And I click on ".prevplan" "css_element"
    And I toggle the "Competency A" detail
    And I should see "good" in "finalrate" of the competency "Competency A"

  Scenario: Bulk rate students, check the second navigation always display
    Given I set the field "templateSelector" to "Medicine Year 1"
    Then "//div[contains(@class, 'tertiary-navigation')][1]//select/option[text()='Bulk rating for all students for all competencies']" "xpath_element" should exist