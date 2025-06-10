@report @javascript @report_lpmonitoring
Feature: Display learning plan ratings details
  As a learning plan appreciator
  In order to rate competencies on learning plan
  I need to view course modules competencies ratings

  Background:
    Given course module competency grading is enabled
    And the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: Filter user learning plan by scales values in activities
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//label[text()='Rating in activity']" "xpath_element"
    And I open the autocomplete suggestions list
    Then I should see "Pablo Menendez" item in the autocomplete list
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I should not see "Cynthia Reyes" item in the autocomplete list
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])" to "Re"
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I should see "Cynthia Reyes" item in the autocomplete list

  Scenario: Filter and sort user learning plan with scales values in activities
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//label[contains(., 'Rating in activity')]" "xpath_element"
    And I click on "//label[contains(., 'Sort in ascending order')]" "xpath_element"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])" to "e"
    Then I should see "(3) rating" item in the autocomplete list
    And I should see "Pablo Menendez" item in the autocomplete list
    And I should see "(6) rating" item in the autocomplete list
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I click on "//label[contains(., 'Sort in descending order')]" "xpath_element"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])" to "e"
    And I should see "(6) rating" item in the autocomplete list
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I should see "(3) rating" item in the autocomplete list
    And I should see "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I should see "Rebecca Armenta" in the ".currentplan" "css_element"
    And I should see "Pablo Menendez" in the ".nexplan" "css_element"
    And I click on "//label[contains(., 'Sort in ascending order')]" "xpath_element"
    And I set the field with xpath "(//input[contains(@id, 'form_autocomplete_input')])" to "e"
    And I press "Apply"
    And I should see "Pablo Menendez" in the ".currentplan" "css_element"
    And I should see "Rebecca Armenta" in the ".nexplan" "css_element"

  Scenario: Read user competency detail
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I toggle the "Competency A" detail
    And I should see "4/6" in "totalnbcms" of the competency "Competency A"
    And I click on "totalnbcms" of the competency "Competency A"
    And "Linked activities" "dialogue" should be visible
    And I should see "Search"
    And "Activity Ps1" row "Rated" column of "listcmincompetencytable" table should contain "Yes"
    And "Activity G1" row "Rated" column of "listcmincompetencytable" table should contain "Yes"
    And "Activity Ph1" row "Rated" column of "listcmincompetencytable" table should contain "No"
    And "Activity N1" row "Rated" column of "listcmincompetencytable" table should contain "Yes"
    And "Activity N2" row "Rated" column of "listcmincompetencytable" table should contain "No"
    And "Activity N3" row "Rated" column of "listcmincompetencytable" table should contain "Yes"
    And "Activity Ps1" row "Courses" column of "listcmincompetencytable" table should contain "Psychology"
    And "Activity G1" row "Courses" column of "listcmincompetencytable" table should contain "Genetic"
    And "Activity Ph1" row "Courses" column of "listcmincompetencytable" table should contain "Pharmacology"
    And "Activity N1" row "Courses" column of "listcmincompetencytable" table should contain "Neuroscience"
    And "Activity N2" row "Courses" column of "listcmincompetencytable" table should contain "Neuroscience"
    And "Activity N3" row "Courses" column of "listcmincompetencytable" table should contain "Neuroscience"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Neuroscience"
    And I should see "Neuroscience" in the "Linked activities" "dialogue"
    And I should not see "Anatomy" in the "Linked activities" "dialogue"
    And I should not see "Genetic" in the "Linked activities" "dialogue"
    And I should not see "Psychology" in the "Linked activities" "dialogue"
    And I should not see "Pharmacology" in the "Linked activities" "dialogue"
    And I should not see "Pathology" in the "Linked activities" "dialogue"
    And I should not see "Activity Ps1" in the "Linked activities" "dialogue"
    And I should not see "Activity G1" in the "Linked activities" "dialogue"
    And I should not see "Activity Ph1" in the "Linked activities" "dialogue"
    And I should see "Activity N1" in the "Linked activities" "dialogue"
    And I should see "Activity N2" in the "Linked activities" "dialogue"
    And I should see "Activity N3" in the "Linked activities" "dialogue"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Nothing"
    And I should see "No matching records found" in the "Linked activities" "dialogue"
    And I click on "Close" "button" in the "Linked activities" "dialogue"
    And I click on "incm" of the competency "Competency A"
    And I should see "1" for "not good" in the row "1" of "Competency A" "incm" rating
    And I should see "3" for "good" in the row "2" of "Competency A" "incm" rating
    And I click on "1" for "not good" in the row "1" of "Competency A" "incm" rating
    And "Linked activities" "dialogue" should be visible
    And "Activity N1" row "Comment" column of "cmsbyscalevalue" table should contain "1"
    And "Activity N1" row "Grade" column of "cmsbyscalevalue" table should contain "-"
    And "Activity N1" row "Course" column of "cmsbyscalevalue" table should contain "Neuroscience"
    And I click on "Close" "button" in the "Linked activities" "dialogue"
    And I click on "3" for "good" in the row "2" of "Competency A" "incm" rating
    And "Linked activities" "dialogue" should be visible
    And I should see "Search"
    And "Activity Ps1" row "Comment" column of "cmsbyscalevalue" table should contain "0"
    And "Activity Ps1" row "Grade" column of "cmsbyscalevalue" table should contain "-"
    And "Activity Ps1" row "Course" column of "cmsbyscalevalue" table should contain "Psychology"
    And "Activity G1" row "Comment" column of "cmsbyscalevalue" table should contain "1"
    And "Activity G1" row "Grade" column of "cmsbyscalevalue" table should contain "B"
    And "Activity G1" row "Course" column of "cmsbyscalevalue" table should contain "Genetic"
    And "Activity N3" row "Comment" column of "cmsbyscalevalue" table should contain "0"
    And "Activity N3" row "Grade" column of "cmsbyscalevalue" table should contain "D"
    And "Activity N3" row "Course" column of "cmsbyscalevalue" table should contain "Neuroscience"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Neuroscience"
    And I should see "Neuroscience" in the "Linked activities" "dialogue"
    And I should not see "Psychology" in the "Linked activities" "dialogue"
    And I should not see "Genetic" in the "Linked activities" "dialogue"
    And I click on "Close" "button" in the "Linked activities" "dialogue"
    And I should see "Not rated" in "level-proficiency" of the competency "Competency A"
    And I click on "incourse" of the competency "Competency A"
    And I should see "4" for "not good" in the row "1" of "Competency A" "incourse" rating
    And I should see "2" for "good" in the row "2" of "Competency A" "incourse" rating
