@core @core_user
Feature: Trim entered user filters
  As a system administrator
  I need to be able to filter users ignoring whitespace
  So that I can find users even when entered data has surrounding whitespace.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Teacher   | 1        | teacher@example.com |
      | student1 | Student   | 1        | student@example.com |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration

  @javascript
  Scenario: Filtering username - with case "contains"
    When I set the field "id_realname_op" to "contains"
    And I set the field "id_realname" to " Teacher "
    And I press "Add filter"
    # We should see the teacher user, with the trimmed string present.
    Then I should see "User full name contains \"Teacher\""
    And I should see "Teacher" in the "users" "table"
    And I should not see "Student" in the "users" "table"

  @javascript
  Scenario: Filtering username - with case "contains" and a whitespace string
    When I set the field "id_realname_op" to "contains"
    And I set the field "id_realname" to "  "
    And I press "Add filter"
    Then I should see "User full name contains \"  \""

  @javascript
  Scenario: Filtering username - with case "is equal to"
    When I set the field "id_realname_op" to "is equal to"
    And I set the field "id_realname" to " Teacher"
    And I press "Add filter"
    Then I should see "User full name is equal to \" Teacher\""
