@mod @mod_assign
Feature: Assignments can have default grades and scales defined
  In order to improve assignment creation
  As a teacher
  The grade and scale should be set by default

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user      | course  | role           |
      | teacher1  | C1      | editingteacher |

  Scenario: When creating an assignment the default grade type and grade scale are set as default in the form
    Given the following config values are set as admin:
      | config            | value | plugin     |
      | defaultgradetype  | 2     | mod_assign |
      | defaultgradescale | 2     | mod_assign |
    When I am on the "Course 1" course page logged in as teacher1
    And I add an assign activity to course "Course 1" section "1"
    And I set the following fields to these values:
      | name | Test assignment |
    Then the following fields match these values:
      | grade[modgrade_type]  | Scale                    |
      | grade[modgrade_scale] | Default competence scale |
    And I press "Save and return to course"
    And I am on the "Test assignment" "assign activity editing" page
    And the following fields match these values:
      | grade[modgrade_type]  | Scale                    |
      | grade[modgrade_scale] | Default competence scale |

  Scenario: Editing an assignment will save the selected grade type and grade scale
    Given the following "activity" exists:
      | activity    | assign                 |
      | course      | C1                     |
      | name        | Test assignment        |
      | description | Assignment description |
      | gradetype   | 1                      |
      | gradescale  | 1                      |
    When I am on the "Test assignment" "assign activity editing" page logged in as teacher1
    And the following fields match these values:
      | grade[modgrade_type]  | Point                    |
      | grade[modgrade_scale] | Default competence scale |
    And I set the following fields to these values:
      | grade[modgrade_type]  | Scale                                  |
      | grade[modgrade_scale] | Separate and Connected ways of knowing |
    And I press "Save and return to course"
    And I am on the "Test assignment" "assign activity editing" page
    Then the following fields match these values:
      | grade[modgrade_type]  | Scale                                  |
      | grade[modgrade_scale] | Separate and Connected ways of knowing |

  Scenario: When a scale is set as the default grade scale it should be shown as in use
    Given the following config values are set as admin:
      | config            | value | plugin     |
      | defaultgradetype  | 2     | mod_assign |
      | defaultgradescale | 2     | mod_assign |
    When I log in as "admin"
    And I navigate to "Grades > Scales" in site administration
    Then "Default competence scale" row "Used" column of "generaltable" table should contain "Yes"
    And "Separate and Connected ways of knowing" row "Used" column of "generaltable" table should contain "No"
    And the following config values are set as admin:
      | config            | value | plugin     |
      | defaultgradetype  | 2     | mod_assign |
      | defaultgradescale | 1     | mod_assign |
    And I navigate to "Grades > Scales" in site administration
    And "Default competence scale" row "Used" column of "generaltable" table should contain "No"
    And "Separate and Connected ways of knowing" row "Used" column of "generaltable" table should contain "Yes"
