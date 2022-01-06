@enrol @enrol_manual
Feature: A teacher can manage manually enrolled users in their course
  In order to manage manually enrolled students in my course
  As a teacher
  I can manually add and remove users in my course

  Background:
    Given the following "users" exist:
      | username | firstname | middlename | lastname | email               |
      | teacher  | Teacher   |            | User     | teacher@example.com |
      | user1    | First     | Alice      | User     | first@example.com   |
      | user2    | Second    | Bob        | User     | second@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript
  Scenario Outline: Manually enrolling users should observe alternative fullname format
    Given the following config values are set as admin:
      | alternativefullnameformat | firstname middlename lastname |
    And the following "permission overrides" exist:
      | capability                | permission   | role           | contextlevel | reference |
      | moodle/site:viewfullnames | <permission> | editingteacher | Course       | C1        |
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Enrolment methods" in current page administration
    And I click on "Enrol users" "link" in the "Manual enrolments" "table_row"
    And I set the field "addselect_searchtext" to "First"
    And I wait "1" seconds
    And I set the field "Not enrolled users" to "<expectedfullname> (first@example.com)"
    And I press "Add"
    Then the "Enrolled users" select box should contain "<expectedfullname> (first@example.com)"
    Examples:
      | permission | expectedfullname |
      | Allow      | First Alice User |
      | Prohibit   | First User       |

  @javascript
  Scenario Outline: Manually unenrolling users should observe alternative fullname format
    Given the following config values are set as admin:
      | alternativefullnameformat | firstname middlename lastname |
    And the following "permission overrides" exist:
      | capability                | permission   | role           | contextlevel | reference |
      | moodle/site:viewfullnames | <permission> | editingteacher | Course       | C1        |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Enrolment methods" in current page administration
    And I click on "Enrol users" "link" in the "Manual enrolments" "table_row"
    And I set the field "removeselect_searchtext" to "First"
    And I wait "1" seconds
    And I set the field "Enrolled users" to "<expectedfullname> (first@example.com)"
    And I press "Remove"
    Then the "Not enrolled users" select box should contain "<expectedfullname> (first@example.com)"
    Examples:
      | permission | expectedfullname |
      | Allow      | First Alice User |
      | Prohibit   | First User       |
