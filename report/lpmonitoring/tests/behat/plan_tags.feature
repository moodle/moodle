@report @javascript @report_lpmonitoring
Feature: Manage learning plans tags
  As a learning plan admin
  In order to filter learning plans by tag
  I need to manage tags in learning plans

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: Manage leaning plans tags
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I press "Apply"
    And I should see "Tag(s)"
    And I should see "0" in the ".tags-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I should see "Edit tags" in the "//h5[contains(@class, 'modal-title') and contains(., 'Edit tags')]" "xpath_element"
    And I should see "No selection" in the "Edit tags" "dialogue"
    When I set the field "Tag(s)" to "student in difficulty"
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    Then I should see "1" in the ".tags-stats" "css_element"
    And I click on "//label[contains(@for, 'tag')]" "xpath_element"
    And the "tagSelectorReport" select box should contain "Choose a tag"
    And the "tagSelectorReport" select box should contain "student in difficulty"
    And I set the field "tagSelectorReport" to "student in difficulty"
    And I press "Apply"
    And I should see "Rebecca Armenta"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I should see "student in difficulty" in the ".modal.show .form-autocomplete-selection" "css_element"
    And I click on "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//span[contains(@data-value, 'student in difficulty')]" "xpath_element"
    And I should not see "student in difficulty" in the ".modal.show .form-autocomplete-selection" "css_element"
    And I click on "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "0" in the ".tags-stats" "css_element"
    And the "tagSelectorReport" select box should not contain "student in difficulty"
    And I click on "//label[contains(@for, 'template')]" "xpath_element"
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I click on ".nexplan" "css_element"
    And I should see "Donald Fletcher"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I click on "Tag(s)" "field"
    And I type "brillant"
    And I press the enter key
    And I click on "Tag(s)" "field"
    And I type "talented"
    And I press the enter key
    And I click on "//div[contains(@data-region, 'modal-container') and contains(@class, 'show')]//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "2" in the ".tags-stats" "css_element"
    And I click on ".nexplan" "css_element"
    And I should see "Stepanie Grant"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I click on ".modal.show .form-autocomplete-downarrow" "css_element"
    And I should see "brillant" in the "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//ul[@class='form-autocomplete-suggestions']" "xpath_element"
    And I should see "talented" in the "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//ul[@class='form-autocomplete-suggestions']" "xpath_element"
    And I set the field with xpath "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//input[@aria-autocomplete='list']" to "brillant"
    And I click on "//div[contains(@class, 'show') and contains(@data-region, 'modal-container')]//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "1" in the ".tags-stats" "css_element"
    And I click on "//label[contains(@for, 'tag')]" "xpath_element"
    And the "tagSelectorReport" select box should contain "Choose a tag"
    And the "tagSelectorReport" select box should contain "brillant"
    And the "tagSelectorReport" select box should contain "talented"
    And the "tagSelectorReport" select box should not contain "student in difficulty"
    And I set the field "tagSelectorReport" to "brillant"
    And I press "Apply"
    And I should see "Stepanie Grant"
    And I should see "Donald Fletcher"
    And I click on ".nexplan" "css_element"
    And I should see "Stepanie Grant"
    And I should see "Donald Fletcher"
    And I set the field "tagSelectorReport" to "talented"
    And I press "Apply"
    And I should see "Donald Fletcher"
    And I should not see "Stepanie Grant"

  Scenario: Filter user learning plan with tags comments
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I press "Apply"
    And I should see "Tag(s)"
    And I should see "0" in the ".tags-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I should see "Edit tags" in the "//h5[contains(@class, 'modal-title') and contains(., 'Edit tags')]" "xpath_element"
    And I should see "No selection" in the "Edit tags" "dialogue"
    When I set the field "Tag(s)" to "student in difficulty"
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    Then I should see "1" in the ".tags-stats" "css_element"
    And I click on "//label[contains(@for, 'tag')]" "xpath_element"
    And the "tagSelectorReport" select box should contain "Choose a tag"
    And the "tagSelectorReport" select box should contain "student in difficulty"
    And I set the field "tagSelectorReport" to "student in difficulty"
    And I click on "//div[contains(@class, 'tagfilter')]//div[contains(@class, 'checkbox')]/label[contains(., 'at least one comment')]" "xpath_element"
    And I press "Apply"
    And I should see "Rebecca Armenta"

  Scenario: Filter user learning plan with tags no comments
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Cynthia Reyes" item in the autocomplete list
    And I press "Apply"
    And I should see "Tag(s)"
    And I should see "0" in the ".tags-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I should see "Edit tags" in the "//h5[contains(@class, 'modal-title') and contains(., 'Edit tags')]" "xpath_element"
    And I should see "No selection" in the "Edit tags" "dialogue"
    When I set the field "Tag(s)" to "brilliant student"
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    Then I should see "1" in the ".tags-stats" "css_element"
    And I click on "//label[contains(@for, 'tag')]" "xpath_element"
    And the "tagSelectorReport" select box should contain "Choose a tag"
    And the "tagSelectorReport" select box should contain "brilliant student"
    And I set the field "tagSelectorReport" to "brilliant student"
    And I click on "//div[contains(@class, 'tagfilter')]//div[contains(@class, 'checkbox')]/label[contains(., 'at least one comment')]" "xpath_element"
    And I press "Apply"
    And I should see "No learning plans found" in the ".alert-info" "css_element"

  Scenario: Filter user learning plan with tags no comments
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Cynthia Reyes" item in the autocomplete list
    And I press "Apply"
    And I should see "Tag(s)"
    And I should see "0" in the ".tags-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managetagsmodal')]" "xpath_element"
    And I should see "Edit tags" in the "//h5[contains(@class, 'modal-title') and contains(., 'Edit tags')]" "xpath_element"
    And I should see "No selection" in the "Edit tags" "dialogue"
    When I set the field "Tag(s)" to "brilliant student"
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    Then I should see "1" in the ".tags-stats" "css_element"
    And I click on "//label[contains(@for, 'tag')]" "xpath_element"
    And the "tagSelectorReport" select box should contain "Choose a tag"
    And the "tagSelectorReport" select box should contain "brilliant student"
    And I set the field "tagSelectorReport" to "brilliant student"
    And I press "Apply"
    And I should see "Cynthia Reyes" item in the autocomplete list
