@javascript @theme_iomadboost
Feature: Add a block using iomadboost theme
  In order to decide the blocks to display in the Add a block list for a theme
  As an administrator
  I need to define them using the unaddableblocks setting

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I log in as "admin"

  Scenario: Default blocks defined in unaddableblocks settings are not displayed in the Add a block list
    Given I am on "Course 1" course homepage with editing mode on
    When I click on "Add a block" "link"
    Then I should not see "Administration"
    And I should not see "Navigation"
    And I should not see "Courses"
    And I should not see "Section links"
    And I should see "Online users"

  Scenario: Admins can change unaddable blocks using the unaddableblocks setting
    Given the following config values are set as admin:
      | unaddableblocks | settings,private_files | theme_iomadboost|
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add a block" "link"
    Then I should not see "Administration"
    And I should not see "Private files"
    And I should see "Navigation"
    And I should see "Courses"
    And I should see "Section links"

  Scenario: If unaddableblocks settting is empty, no block is excluded from the Add a block list
    Given the following config values are set as admin:
      | unaddableblocks |  | theme_iomadboost|
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add a block" "link"
    Then I should see "Administration"
    And I should see "Navigation"
    And I should see "Courses"
    And I should see "Section links"
