@report @report_outline
Feature: View an outline report
  In order to ensure the outline report works as expected
  As a teacher
  I need to log in as a teacher and view the outline report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    When I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Forum name |
      | Description | Forum description |
    And I add a "Book" to section "1" and I fill the form with:
      | Name | Book name |
      | Description | Book description |

  @javascript
  Scenario: View the outline report when only the legacy log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I set the following administration settings values:
      | Log legacy data | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2" in the "//tbody/tr[(position() mod 2)=1]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"
    And I should see "1" in the "//tbody/tr[(position() mod 2)=0]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"

  @javascript
  Scenario: View the outline report when only the standard log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "admin"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2" in the "//tbody/tr[(position() mod 2)=1]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"
    And I should see "1" in the "//tbody/tr[(position() mod 2)=0]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"

  @javascript
  Scenario: View the outline report when both the standard and legacy log readers are enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I set the following administration settings values:
      | Log legacy data | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2" in the "//tbody/tr[(position() mod 2)=1]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"
    And I should see "1" in the "//tbody/tr[(position() mod 2)=0]/child::td[contains(concat(' ', normalize-space(@class),' '),' numviews ')]" "xpath_element"

  @javascript
  Scenario: View the outline report when no log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I follow "Home"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "No log reader enabled"
