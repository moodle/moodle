@report @report_themeusage
Feature: Navigate to a theme usage report
  In order to see a theme usage report
  As an admin
  I need to set a theme for user/course/category/cohort and view the report

  Background:
    Given the following config values are set as admin:
      | allowuserthemes     | 1 |
      | allowcoursethemes   | 1 |
      | allowcategorythemes | 1 |
      | allowcohortthemes   | 1 |
    And I log in as "admin"

  Scenario: I am able to see theme usage report for all contexts overriding the default theme
    Given the following "courses" exist:
      | fullname | shortname | theme |
      | Course 1 | course1   | boost |
      | Course 2 | course2   | boost |
    And the following "user" exists:
      | username  | student1 |
      | firstname | Student  |
      | lastname  | One      |
      | theme     | boost    |
    And I navigate to "Reports > Theme usage" in site administration
    And I set the field "Theme name" to "boost"
    And I set the field "Usage type" to "all"
    When I press "Get report"
    Then the following should exist in the "reportbuilder-table" table:
      | Usage type | Force theme |
      | Course (2) | Boost       |
      | User (1)   | Boost       |

  Scenario: I am able to see theme usage report for courses overriding the default theme
    Given the following "course" exists:
      | fullname  | Course 1 |
      | shortname | course1  |
      | theme     | boost    |
    And I navigate to "Reports > Theme usage" in site administration
    And I set the field "Theme name" to "boost"
    And I set the field "Usage type" to "course"
    When I press "Get report"
    Then the following should exist in the "reportbuilder-table" table:
      | Course full name | Course short name | Force theme |
      | Course 1         | course1           | Boost       |

  Scenario: I am able to see theme usage report for users overriding the default theme
    Given the following "user" exists:
      | username  | student1 |
      | firstname | Student  |
      | lastname  | One      |
      | theme     | boost    |
    And I navigate to "Reports > Theme usage" in site administration
    And I set the field "Theme name" to "boost"
    And I set the field "Usage type" to "user"
    When I press "Get report"
    Then the following should exist in the "reportbuilder-table" table:
      | First name | Last name | Force theme |
      | Student    | One       | Boost       |

  Scenario: I am able to see theme usage report for cohorts overriding the default theme
    Given the following "cohort" exists:
      | name     | Cohort 1 |
      | idnumber | cohort1  |
      | context  | system   |
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I press "Edit" action in the "cohort1" report row
    And I set the field "theme" to "boost"
    And I press "Save changes"
    And I navigate to "Reports > Theme usage" in site administration
    And I set the field "Theme name" to "boost"
    And I set the field "Usage type" to "cohort"
    When I press "Get report"
    Then the following should exist in the "reportbuilder-table" table:
      | Name     | Category | Force theme |
      | Cohort 1 | System   | Boost       |

  Scenario: I am able to see theme usage report for categories overriding the default theme
    Given the following "categories" exist:
      | name       | category | idnumber  |
      | Category 1 | 0        | category1 |
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "edit" action for "Category 1" in management category listing
    And I set the field "theme" to "boost"
    And I press "Save changes"
    And I navigate to "Reports > Theme usage" in site administration
    And I set the field "Theme name" to "boost"
    And I set the field "Usage type" to "category"
    When I press "Get report"
    Then the following should exist in the "reportbuilder-table" table:
      | Category name  | Course count | Force theme |
      | Category 1     | 0            | Boost       |
