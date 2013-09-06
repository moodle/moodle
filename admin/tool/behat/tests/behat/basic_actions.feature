@tool @tool_behat
Feature: Page contents assertions
  In order to write good tests
  As a tests writer
  I need to check the page contents

  @javascript
  Scenario: Basic contents assertions
    Given I log in as "admin"
    And I am on homepage
    And I expand "Users" node
    And I follow "Groups"
    And I press "Create group"
    And I fill the moodle form with:
      | Group name | I'm the name |
      | Group description | I'm the description |
    And I press "Save changes"
    When I follow "Overview"
    And I wait until the page is ready
    And I wait "2" seconds
    And I hover ".region-content .generaltable td span" "css_element"
    Then I should see "I'm the description"
    And I should see "Filter groups by"
    And I should not see "Filter groupssss by"
    And I should see "Group members" in the ".region-content table th.c1" "css_element"
    And I should not see "Group membersssss" in the ".region-content table th.c1" "css_element"
    And I follow "Groups"
    And the "#groupeditform #showcreateorphangroupform" "css_element" should be enabled
    And the "#groupeditform #showeditgroupsettingsform" "css_element" should be disabled

  @javascript
  Scenario: Locators inside specific DOM nodes using CSS selectors
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Course 1"
    When I click on "Move this to the dock" "button" in the "Administration" "block"
    Then I should not see "Question bank"
    And I click on "//div[@id='dock']/descendant::h2[normalize-space(.)='Administration']" "xpath_element"

  @javascript
  Scenario: Locators inside specific DOM nodes using XPath
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    When I click on "Move this to the dock" "button" in the "Administration" "block"
    Then I should not see "Turn editing on"
