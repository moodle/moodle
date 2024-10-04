@report @report_log @mod_subsection
Feature: Subsection behavior in Log report.

  Background:
    Given the following "courses" exist:
      | fullname | shortname  | category  | numsections | initsections |
      | Course 1 | C1         | 0         | 2           | 1            |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name         | course | idnumber    | section |
      | subsection | Subsection 1 | C1     | subsection1 | 1       |
      | page       | Page 1       | C1     | page1       | 3       |

  @javascript
  Scenario: Visible subsections should be available in the activities selector
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    Then the "Activities" select box should contain "Subsection 1"
    And the "Activities" select box should contain "Page 1"

  @javascript
  Scenario: Hidden subsections should be available in the activities selector
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I hide section "Subsection 1"
    When I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    Then the "Activities" select box should not contain "Subsection 1"
    And the "Activities" select box should not contain "Page 1"
