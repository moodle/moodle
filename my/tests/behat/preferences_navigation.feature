@core @core_my @javascript
Feature: Navigate and use preferences page
  In order to navigate through preferences page
  As a user
  I need to be able to use preferences page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | Sam       | Student  | s1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And I log in as "admin"

  Scenario Outline: Navigating through user menu Preferences
    When I follow "Preferences" in the user menu
    # Click each link in the 'Preferences' page.
    And I click on "<userprefpage>" "link" in the "#page-content" "css_element"
    # Confirm that each redirected page has 'Preferences' in the breadcrumbs.
    And "Users" "link" should not exist in the ".breadcrumb" "css_element"
    Then "Preferences" "link" should exist in the ".breadcrumb" "css_element"
    # Additional confirmation that breadcrumbs is correct.
    And "<userprefpage>" "text" should exist in the ".breadcrumb" "css_element"
    # Confirm that user name and profile picture are displayed in header section.
    And I should see "Admin User" in the ".page-header-headings" "css_element"
    And ".page-header-image" "css_element" should exist in the "#page-header" "css_element"

    Examples:
      | userprefpage                 |
      | Edit profile                 |
      | Change password              |
      | Preferred language           |
      | Forum preferences            |
      | Editor preferences           |
      | Calendar preferences         |
      | Content bank preferences     |
      | Message preferences          |
      | Notification preferences     |
      | Manage badges                |
      | Badge preferences            |
      | Backpack settings            |
      | This user's role assignments |
      | Permissions                  |
      | Check permissions            |
      | Blog preferences             |
      | External blogs               |
      | Register an external blog    |

  Scenario Outline: Navigating through course participant preferences
    Given I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Sam Student"
    When I click on "Preferences" "link" in the "#region-main-box" "css_element"
    Then I should see "Sam Student" in the ".page-header-headings" "css_element"
    And ".page-header-image" "css_element" should exist in the "#page-header" "css_element"
    # Click each link in the 'Preferences' page.
    And I click on "<courseprefpage>" "link" in the "#page-content" "css_element"
    # Confirm that each redirected page has 'Users/{user}/Preferences' in the breadcrumbs.
    Then "Users" "link" should exist in the ".breadcrumb" "css_element"
    And "Sam Student" "link" should exist in the ".breadcrumb" "css_element"
    And "Preferences" "link" should exist in the ".breadcrumb" "css_element"
    # Additional confirmation that breadcrumbs is correct.
    And "<courseprefpage>" "text" should exist in the ".breadcrumb" "css_element"
    # Confirm that user name and profile picture are displayed in header section.
    And I should see "Sam Student" in the ".page-header-headings" "css_element"
    And ".page-header-image" "css_element" should exist in the "#page-header" "css_element"

    Examples:
      | courseprefpage               |
      | Edit profile                 |
      | Preferred language           |
      | Forum preferences            |
      | Editor preferences           |
      | Calendar preferences         |
      | Content bank preferences     |
      | Message preferences          |
      | Notification preferences     |
      | This user's role assignments |
      | Permissions                  |
      | Check permissions            |

  Scenario: Navigation with Event monitoring enabled
    Given I navigate to "Reports > Event monitoring rules" in site administration
    And I click on "Enable" "link"
    And I press "Add a new rule"
    And I set the following fields to these values:
      | Rule name       | Testing1            |
      | Area to monitor | Subsystem (core)    |
      | Event           | Allow role override |
    And I press "Save changes"
    When I follow "Preferences" in the user menu
    # Confirm that Event monitoring is visible and clickable.
    Then I should see "Miscellaneous"
    And I follow "Event monitoring"
    # Confirm that user can subscribe to new rule.
    And "Subscribe to rule \"Testing1\"" "link" should exist
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Sam Student"
    And I click on "Preferences" "link" in the "#region-main-box" "css_element"
    # Confirm that admin cannot change student's event monitor subscription.
    And I should not see "Event monitoring"
