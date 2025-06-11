@report @javascript @report_lpmonitoring
Feature: Manage learning plans comments
  As a learning plan appreciator

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: Manage learning plans comments (as appreciator)
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    When I press "Apply"
    Then I should see "1" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And I wait "1" seconds
    And "View or add comments" "dialogue" should be visible
    And I set the field with xpath "//textarea[contains(@id, 'plancommentarea')]" to "Comment 2 for Rebecca"
    And I click on "Save comment" "link"
    And I set the field with xpath "//textarea[contains(@id, 'plancommentarea')]" to "Comment 3 for Rebecca"
    And I click on "Save comment" "link"
    And I click on "Close" "button" in the "View or add comments" "dialogue"
    And I should see "3" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And "View or add comments" "dialogue" should be visible
    And I click on "//ul[contains(@class, 'comment-list')]/li[2]//div[contains(@class, 'comment-delete')]/a" "xpath_element"
    And I click on "Close" "button" in the "View or add comments" "dialogue"
    And I should see "2" in the ".comments-stats" "css_element"
    And I click on ".nexplan" "css_element"
    And I should see "Donald Fletcher"
    And I should see "Comments"
    And I should see "0" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And "View or add comments" "dialogue" should be visible
    And I set the field with xpath "//textarea[contains(@id, 'plancommentarea')]" to "Comment 1 for Donald"
    And I click on "Save comment" "link"
    And I click on "Close" "button" in the "View or add comments" "dialogue"
    And I should see "1" in the ".comments-stats" "css_element"
  @commentplan
  Scenario: Manage learning plans comments (as appreciator and as student)
    # Create a comment as appreciator
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "0" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And I wait "1" seconds
    And "View or add comments" "dialogue" should be visible
    And I set the field with xpath "//textarea[contains(@id, 'plancommentarea')]" to "Comment 1 for Pablo"
    And I click on "Save comment" "link"
    And I click on "Close" "button" in the "View or add comments" "dialogue"
    And I should see "1" in the ".comments-stats" "css_element"
    And I log out
    # The student can view the comment and add another comment
    And I log in as "pablom"
    And I follow "Profile" in the user menu
    And I follow "Monitoring of learning plans"
    And I should see "Monitoring of learning plans"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I press "Apply"
    And I should see "Learning plan competencies: Medicine Year 1"
    And I should see "1" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And I wait "1" seconds
    And "View or add comments" "dialogue" should be visible
    And I should see "Comment 1 for Pablo" in the "//ul[contains(@class, 'comment-list')]/li[1]" "xpath_element"
    And "//ul[contains(@class, 'comment-list')]/li[1]//div[contains(@class, 'comment-delete')]/a" "xpath_element" should not exist
    And I set the field with xpath "//textarea[contains(@id, 'plancommentarea')]" to "Comment from Pablo"
    And I click on "Save comment" "link"
    And I should see "Comment from Pablo" in the "//ul[contains(@class, 'comment-list')]/li[2]" "xpath_element"
    And "//ul[contains(@class, 'comment-list')]/li[2]//div[contains(@class, 'comment-delete')]/a" "xpath_element" should exist
    And I click on "Close" "button" in the "View or add comments" "dialogue"
    And I should see "2" in the ".comments-stats" "css_element"
    And I log out
    # The appreciator can view the comment that the student entered
    And I log in as "appreciator"
    And I am on "Medicine" lpmonitoring page
    And I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I should see "2" in the ".comments-stats" "css_element"
    And I click on "//a[contains(@data-action, 'managecommentsmodal')]" "xpath_element"
    And I wait "1" seconds
    And "View or add comments" "dialogue" should be visible
    And I should see "Comment 1 for Pablo" in the "//ul[contains(@class, 'comment-list')]/li[1]" "xpath_element"
    And I should see "Comment from Pablo" in the "//ul[contains(@class, 'comment-list')]/li[2]" "xpath_element"
    And "//ul[contains(@class, 'comment-list')]/li[1]//div[contains(@class, 'comment-delete')]/a" "xpath_element" should exist
    And "//ul[contains(@class, 'comment-list')]/li[2]//div[contains(@class, 'comment-delete')]/a" "xpath_element" should not exist
