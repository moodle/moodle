@mod @mod_page
Feature: Configure page appearance
  In order to change the appearance of the page resource
  As an admin
  I need to configure the page appearance settings

  Background:
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    |
    And I log in as "admin"

  @javascript
  Scenario: Hide and display the page name
    Given I am on "Course 1" course homepage
    When I follow "PageName1"
    Then I should see "PageName1" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display page name" "checkbox"
    And I press "Save and display"
    Then I should not see "PageName1" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display page name" "checkbox"
    And I press "Save and display"
    Then I should see "PageName1" in the "region-main" "region"

  @javascript
  Scenario: Display and hide the page description
    Given I am on "Course 1" course homepage
    When I follow "PageName1"
    Then I should not see "PageDesc1" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display page description" "checkbox"
    And I press "Save and display"
    Then I should see "PageDesc1" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display page description" "checkbox"
    And I press "Save and display"
    Then I should not see "PageDesc1" in the "region-main" "region"

  @javascript
  Scenario: Display and hide the last modified date
    Given I am on "Course 1" course homepage
    When I follow "PageName1"
    Then I should see "Last modified:" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display last modified date" "checkbox"
    And I press "Save and display"
    Then I should not see "Last modified:" in the "region-main" "region"
    And I navigate to "Edit settings" in current page administration
    And I follow "Appearance"
    When I click on "Display last modified date" "checkbox"
    And I press "Save and display"
    Then I should see "Last modified:" in the "region-main" "region"
