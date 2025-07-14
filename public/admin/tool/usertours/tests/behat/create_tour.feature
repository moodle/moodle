@tool @tool_usertours
Feature: Add a new user tour
  In order to help users learn of new features
  As an administrator
  I need to create a user tour

  @javascript
  Scenario: Add a new tour
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 1 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                                                                                                                     | Content type   |
      | Display in middle of page | Welcome | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful | Manual         |
    And I add steps to the "First tour" tour:
      | targettype | targetvalue_block | Title    | id_content                                                                    | Content type   |
      | Block      | Timeline          | Timeline | This is the Timeline. All of your upcoming activities can be found here       | Manual         |
      | Block      | Calendar          | Calendar | This is the Calendar. All of your assignments and due dates can be found here | Manual         |
    And I add steps to the "First tour" tour:
      | targettype | targetvalue_selector | Title     | id_content                                                                                         | Content type   |
      | Selector   | .usermenu            | User menu | This is your personal user menu. You'll find your personal preferences and your user profile here. | Manual         |
    When I am on homepage
    Then I should see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"
    And I click on "Next" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should see "This is the Timeline. All of your upcoming activities can be found here"
    And I should not see "This is the Calendar. All of your assignments and due dates can be found here"
    And I click on "Next" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should see "This is the Calendar. All of your assignments and due dates can be found here"
    And I should not see "This area shows you what's happening in some of your courses"
    And I click on "Skip tour" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should not see "This area shows you what's happening in some of your courses"
    And I am on homepage
    And I should not see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"
    And I should not see "This area shows you what's happening in some of your courses"
    And I follow "Reset user tour on this page"
    And I should see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"

  @javascript
  Scenario: A hidden tour should not be visible
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 0 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                                                                                                                     | Content type   |
      | Display in middle of page | Welcome | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful | Manual         |
    When I am on homepage
    Then I should not see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"

  @javascript
  Scenario: Tour visibility can be toggled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 0 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                                                                                                                     | Content type   |
      | Display in middle of page | Welcome | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful | Manual         |
    And I open the User tour settings page
    When I click on "Enable" "link" in the "My first tour" "table_row"
    And I am on homepage
    Then I should see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"

  @javascript
  Scenario: Display step numbers was enabled
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                    | Steps tour       |
      | Description             | My steps tour    |
      | Apply to URL match      | /my/%            |
      | Tour is enabled         | 1                |
      | Display step numbers    | 1                |
      | End tour button's label | Sample end label |
    And I add steps to the "Steps tour" tour:
      | targettype                | Title   | id_content             | Content type   |
      | Display in middle of page | Welcome | First step of the Tour | Manual         |
    And I add steps to the "Steps tour" tour:
      | targettype | targetvalue_block | Title    | id_content              | Content type   |
      | Block      | Timeline          | Timeline | Second step of the Tour | Manual         |
      | Block      | Calendar          | Calendar | Third step of the Tour  | Manual         |
    When I am on homepage
    Then I should see "First step of the Tour"
    And I should see "Next (1/3)"
    And I should not see "End tour"
    And I should not see "Sample end label"
    And "Skip tour" "button" should exist in the "[data-role='flexitour-step']" "css_element"
    And I click on "Next (1/3)" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should see "Second step of the Tour"
    And I should see "Next (2/3)"
    And I click on "Next (2/3)" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should see "Third step of the Tour"
    And I should not see "Next (3/3)"
    And I should not see "Skip tour"
    And "Sample end label" "button" should exist in the "[data-role='flexitour-step']" "css_element"

  @javascript
  Scenario: Display step numbers was disabled
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                 | Steps tour    |
      | Description          | My steps tour |
      | Apply to URL match   | /my/%         |
      | Tour is enabled      | 1             |
      | Display step numbers | 0             |
    And I add steps to the "Steps tour" tour:
      | targettype                | Title   | id_content             | Content type   |
      | Display in middle of page | Welcome | First step of the Tour | Manual         |
    And I add steps to the "Steps tour" tour:
      | targettype | targetvalue_block | Title    | id_content              | Content type   |
      | Block      | Timeline          | Timeline | Second step of the Tour | Manual         |
      | Block      | Calendar          | Calendar | Third step of the Tour  | Manual         |
    When I am on homepage
    Then I should see "First step of the Tour"
    And I should see "Next"
    And I should not see "Next (1/3)"
    And I click on "Next" "button" in the "[data-role='flexitour-step']" "css_element"
    And I should see "Second step of the Tour"
    And I should see "Next"
    And I should not see "Next (2/3)"

  @javascript
  Scenario: Single step tour with display step numbers was enable
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                 | Steps tour    |
      | Description          | My steps tour |
      | Apply to URL match   | /my/%         |
      | Tour is enabled      | 1             |
      | Display step numbers | 1             |
    And I add steps to the "Steps tour" tour:
      | targettype                | Title   | id_content                 | Content type   |
      | Display in middle of page | Welcome | This is a single step tour | Manual         |
    When I am on homepage
    Then I should see "This is a single step tour"
    And I should not see "Next (1/1)"
