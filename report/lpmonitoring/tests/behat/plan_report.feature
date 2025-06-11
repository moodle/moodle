@report @javascript @report_lpmonitoring
Feature: Display learning plan report details
  As a learning plan admin
  In order to display competencies ratings on learning plan
  I need to rate a competency in a learning plan

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on course index
    When I follow "Medicine"
    And I select "Competency reports" from secondary navigation
    Then I should see "Monitoring of learning plans"

  Scenario: Read user learning plan by template filter
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I should see "Rebecca Armenta" in the ".currentplan" "css_element"
    And I should see "Medicine Year 1"
    And I should see "Donald Fletcher" in the ".nexplan" "css_element"

  Scenario: Read user learning plan filtered by template and user
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I should see "Pablo Menendez" item in the autocomplete list
    And I click on "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I should see "Pablo Menendez" in the ".currentplan" "css_element"
    And I should see "Medicine Year 1"
    And I should see "Stepanie Grant" in the ".prevplan" "css_element"
    And I should see "Cynthia Reyes" in the ".nexplan" "css_element"

  Scenario: Read user learning plan with empty courses rating
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I press "Apply"
    And I should see "Competency A"
    And I should see "Competency B"
    When I toggle the "Competency A" detail
    Then I should see "No data available" in "no-data-available" of the competency "Competency A"

  Scenario: Read user learning plan filtered by user
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    And the "Apply" "button" should be disabled
    And I click on ".studentfilter .form-autocomplete-downarrow" "css_element"
    And I click on "Stepanie Grant" item in the autocomplete list
    And the "studentPlansSelectorReport" select box should contain "Medicine Year 1"
    And the "studentPlansSelectorReport" select box should contain "My custom learing plan"
    And the "studentPlansSelectorReport" select box should contain "My empty learing plan"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I should see "Stepanie Grant" in the ".currentplan" "css_element"
    And I should see "Medicine Year 1"
    And I should see "Not rated" in the "//span[contains(@class, 'level') and ancestor-or-self::div[contains(., 'Competency A')]]" "xpath_element"
    And I should see "Not rated" in the "//span[contains(@class, 'level') and ancestor-or-self::div[contains(., 'Competency B')]]" "xpath_element"
    And ".prevplan" "css_element" should not exist
    And ".nexplan" "css_element" should not exist

  Scenario: Read own learning plan filtered by user
    Given I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'student')]" "xpath_element"
    And the "Apply" "button" should be disabled
    And I click on ".studentfilter .form-autocomplete-downarrow" "css_element"
    And I click on "Stepanie Grant" item in the autocomplete list
    And the "studentPlansSelectorReport" select box should contain "Medicine Year 1"
    And the "studentPlansSelectorReport" select box should contain "My custom learing plan"
    And the "studentPlansSelectorReport" select box should contain "My empty learing plan"
    And I set the field "studentPlansSelectorReport" to "My custom learing plan"
    When I press "Apply"
    Then I should see "Competency A"
    And I should not see "Competency B"
    And I should see "Stepanie Grant" in the ".currentplan" "css_element"
    And I should see "Medicine Year 1"
    And I set the field "studentPlansSelectorReport" to "My empty learing plan"
    And I press "Apply"
    And I should see "Stepanie Grant" in the ".currentplan" "css_element"
    And I should see "No competencies have been linked to this learning plan."
    And I click on ".studentfilter .form-autocomplete-downarrow" "css_element"
    And I click on "Pablo Menendez" item in the autocomplete list
    And the "studentPlansSelectorReport" select box should contain "Medicine Year 1"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan draft"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan active"
    And the "studentPlansSelectorReport" select box should contain "Pablo learing plan completed"
    And I set the field "studentPlansSelectorReport" to "Pablo learing plan active"
    And I press "Apply"
    And I should see "Active" in the ".plan-status" "css_element"
    And I set the field "studentPlansSelectorReport" to "Pablo learing plan completed"
    And I press "Apply"
    And I should see "Complete" in the ".plan-status" "css_element"
    And I set the field "studentPlansSelectorReport" to "Pablo learing plan draft"
    And I press "Apply"
    And I should see "Draft" in the ".plan-status" "css_element"
    And I set the field "studentPlansSelectorReport" to "Medicine Year 1"
    And I should see "0/1" in the ".proficient-stats" "css_element"
    And I should see "0" in the ".notproficient-stats" "css_element"
    And I should see "1" in the ".notrated-stats" "css_element"
    And I toggle the "Competency A" detail
    And I wait "1" seconds
    And I click on "rate-competency" of the competency "Competency A"
    And "Rate" "dialogue" should be visible
    And I set the field with xpath "//select[@name='rating']" to "not good"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "0/1" in the ".proficient-stats" "css_element"
    And I should see "1" in the ".notproficient-stats" "css_element"
    And I should see "0" in the ".notrated-stats" "css_element"
    And I click on "rate-competency" of the competency "Competency A"
    And "Rate" "dialogue" should be visible
    And I set the field with xpath "//select[@name='rating']" to "good"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "1/1" in the ".proficient-stats" "css_element"
    And I should see "0" in the ".notproficient-stats" "css_element"
    And I should see "0" in the ".notrated-stats" "css_element"

  Scenario: Filter user learning plan by scales values
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//label[text()='Rating in course']" "xpath_element"
    And I open the autocomplete suggestions list
    Then I should see "Pablo Menendez" item in the autocomplete list
    And I should not see "Re" item in the autocomplete list
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I open the autocomplete suggestions list
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I should see "Cynthia Reyes" item in the autocomplete list

  Scenario: Filter and sort user learning plan with scales values in plan
    Given I set the field "templateSelectorReport" to "Medicine Year 2"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//label[contains(., 'Final rating')]" "xpath_element"
    And I click on ".templatefilter .form-autocomplete-downarrow" "css_element"
    Then I should see "(1) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Frederic Simson" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "(2) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I should see "Robert Smith" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I click on "//label[contains(., 'Sort in descending order')]" "xpath_element"
    And I click on ".templatefilter .form-autocomplete-downarrow" "css_element"
    And I should see "(2) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Robert Smith" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "(1) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I should see "Frederic Simson" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I press "Apply"
    And I should see "Robert Smith" in the ".currentplan" "css_element"
    And I should see "Frederic Simson" in the ".nexplan" "css_element"
    And I click on "//label[contains(., 'Sort in ascending order')]" "xpath_element"
    And I press "Apply"
    And I should see "Frederic Simson" in the ".currentplan" "css_element"
    And I should see "Robert Smith" in the ".nexplan" "css_element"

  Scenario: Filter and sort user learning plan with scales values in course
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//label[text()='Rating in course']" "xpath_element"
    And I click on ".templatefilter .form-autocomplete-downarrow" "css_element"
    Then I should see "(3) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Donald Fletcher" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "(8) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I should see "Pablo Menendez" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I click on "//label[contains(., 'Sort in descending order')]" "xpath_element"
    And I click on ".templatefilter .form-autocomplete-downarrow" "css_element"
    And I should see "(8) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Pablo Menendez" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "(3) rating" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I should see "Donald Fletcher" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I press "Apply"
    And I should see "Pablo Menendez" in the ".currentplan" "css_element"
    And I should see "Donald Fletcher" in the ".nexplan" "css_element"
    And I click on "//label[contains(., 'Sort in ascending order')]" "xpath_element"
    And I press "Apply"
    And I should see "Donald Fletcher" in the ".currentplan" "css_element"
    And I should see "Pablo Menendez" in the ".nexplan" "css_element"

  Scenario: Filter user learning plan with comments
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    And I set the field with xpath "(//div[contains(@class, 'templatefilter')]//input[contains(@id, 'form_autocomplete_input')])" to "Re"
    And I should see "Rebecca Armenta" item in the autocomplete list
    And I should not see "comment(s)" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Cynthia Reyes" item in the autocomplete list
    And I should not see "comment(s)" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[2]" "xpath_element"
    And I open the autocomplete suggestions list
    And I should see "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "Rebecca Armenta" in the ".currentplan" "css_element"
    And I should see "Donald Fletcher" in the ".nexplan" "css_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'at least one comment')]" "xpath_element"
    And I open the autocomplete suggestions list
    And I should see "(1) comment(s)" in the "//ul[contains(@class, 'form-autocomplete-suggestions')]/li[1]" "xpath_element"
    And I should see "Stepanie Grant" item in the autocomplete list
    And I should not see "Cynthia Reyes" item in the autocomplete list
    And I should not see "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I should see "Rebecca Armenta" in the ".currentplan" "css_element"
    And I should see "Stepanie Grant" in the ".nexplan" "css_element"

  Scenario: Read user learning plan by navigating between users
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I should see "Pablo Menendez" in the ".currentplan" "css_element"
    And I should see "Medicine Year 1"
    And I should see "Stepanie Grant" in the ".prevplan" "css_element"
    And I should see "Cynthia Reyes" in the ".nexplan" "css_element"
    And I click on ".prevplan" "css_element"
    And I should see "Competency A"
    And I should see "Competency B"
    And I should see "Stepanie Grant" in the ".currentplan" "css_element"
    And I click on ".nexplan" "css_element"
    And I should see "Competency A"
    And I should see "Competency B"
    And I should see "Pablo Menendez" in the ".currentplan" "css_element"

  Scenario: Read user competency detail
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    When I press "Apply"
    Then I should see "Competency A"
    And I should see "Competency B"
    And I toggle the "Competency A" detail
    And I should see "6/6" in "totalnbcourses" of the competency "Competency A"
    And I click on "totalnbcourses" of the competency "Competency A"
    And "Linked courses" "dialogue" should be visible
    And I should see "Search"
    And "Anatomy" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Genetic" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Psychology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Pharmacology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Pathology" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And "Neuroscience" row "Rated" column of "listcourseincompetencytable" table should contain "Yes"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Pathology"
    And I should see "Pathology" in the "Linked courses" "dialogue"
    And I should not see "Anatomy" in the "Linked courses" "dialogue"
    And I should not see "Genetic" in the "Linked courses" "dialogue"
    And I should not see "Psychology" in the "Linked courses" "dialogue"
    And I should not see "Pharmacology" in the "Linked courses" "dialogue"
    And I should not see "Neuroscience" in the "Linked courses" "dialogue"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Nothing"
    And I should see "No matching records found" in the "Linked courses" "dialogue"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I should see "1" in "listevidence" of the competency "Competency A"
    And I click on "listevidence" of the competency "Competency A"
    And "List of evidence" "dialogue" should be visible
    And I should see "Search"
    And I should see "My evidence" in the "List of evidence" "dialogue"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Nothing"
    And I should see "No matching records found" in the "List of evidence" "dialogue"
    And I click on "Close" "button" in the "List of evidence" "dialogue"
    And I should see "4" for "not good" in the row "1" of "Competency A" "incourse" rating
    And I should see "2" for "good" in the row "2" of "Competency A" "incourse" rating
    And I click on "4" for "not good" in the row "1" of "Competency A" "incourse" rating
    And "Linked courses" "dialogue" should be visible
    And I should see "Search"
    And "Anatomy" row "Comment" column of "coursesbyscalevalue" table should contain "1"
    And "Anatomy" row "Grade" column of "coursesbyscalevalue" table should contain "D+"
    And "Genetic" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Pathology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Neuroscience" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And I set the field with xpath "//div[@class='lpmonitoringdialogue']//input[@type='search']" to "Genetic"
    And I should see "Genetic" in the "Linked courses" "dialogue"
    And I should not see "Anatomy" in the "Linked courses" "dialogue"
    And I should not see "Neuroscience" in the "Linked courses" "dialogue"
    And I should not see "Pathology" in the "Linked courses" "dialogue"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I click on "2" for "good" in the row "2" of "Competency A" "incourse" rating
    And "Linked courses" "dialogue" should be visible
    And "Psychology" row "Comment" column of "coursesbyscalevalue" table should contain "0"
    And "Psychology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And "Pharmacology" row "Comment" column of "coursesbyscalevalue" table should contain "0"
    And "Pharmacology" row "Grade" column of "coursesbyscalevalue" table should contain "-"
    And I click on "Close" "button" in the "Linked courses" "dialogue"
    And I should see "Not rated" in "level-proficiency" of the competency "Competency A"

  Scenario: Rate a competency in user plan via the rate button
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I press "Apply"
    And I should see "Competency A"
    And I should see "Competency B"
    And I toggle the "Competency B" detail
    And I wait "1" seconds
    When I click on "rate-competency" of the competency "Competency B"
    Then "Rate" "dialogue" should be visible
    And I set the field with xpath "//select[@name='rating']" to "not qualified"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "Not proficient" in "level-proficiency" of the competency "Competency B"
    And I should see "not qualified" in "finalrate" of the competency "Competency B"
    And I toggle the "Competency B" detail
    And I should see "Not proficient" in "level" of the competency "Competency B"

  Scenario: Rate a competency in user plan via user competency popup
    Given I set the field "templateSelectorReport" to "Medicine Year 1"
    And I open the autocomplete suggestions list
    And I click on "Cynthia Reyes" item in the autocomplete list
    And I press "Apply"
    And I should see "Competency A"
    And I should see "Competency B"
    When I click on "//a[contains(., 'Competency A')]" "xpath_element"
    Then "User competency summary" "dialogue" should be visible
    And I click on "//dd/button[contains(., 'Rate')]" "xpath_element"
    And "Rate" "dialogue" should be visible
    And I set the field with xpath "//select[@name='rating']" to "good"
    And I click on "//button[contains(@data-action, 'rate')] | //input[contains(@data-action, 'rate')]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "Proficient" in "level" of the competency "Competency A"
    And I toggle the "Competency A" detail
    And I should see "Proficient" in "level-proficiency" of the competency "Competency A"
    And I should see "good" in "finalrate" of the competency "Competency A"

  Scenario: Filter user learning plan by scales values and plan rate
    Given I set the field "templateSelectorReport" to "Medicine Year 2"
    And I click on "//a[contains(@class, 'moreless-toggler')]" "xpath_element"
    When I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I click on "//div[contains(@class, 'radio')]/span/label[contains(@for, 'scalefilterplan')]" "xpath_element"
    And I open the autocomplete suggestions list
    Then I should not see "William Presley" item in the autocomplete list
    And I should see "Robert Smith" item in the autocomplete list
    And I should see "Frederic Simson" item in the autocomplete list
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not good')]" "xpath_element"
    And I click on "//div[contains(@class, 'checkbox')]/label[contains(., 'not qualified')]" "xpath_element"
    And I open the autocomplete suggestions list
    And I should see "Robert Smith" item in the autocomplete list
    And I should see "William Presley" item in the autocomplete list
    And I should see "Frederic Simson" item in the autocomplete list
