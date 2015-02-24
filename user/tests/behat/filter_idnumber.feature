@core @core_user
Feature: Filter users by idnumber
  As a system administrator
  I need to be able to filter users by their ID number
  So that I can quickly find users based on an external key.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher  | 1 | teacher@asd.com  | 0000002 |
      | student1 | Student1 | 1 | student1@asd.com | 0000003 |
      | student2 | Student2 | 1 | student2@asd.com | 2000000 |
      | student3 | Student3 | 1 | student3@asd.com | 3000000 |
    And I log in as "admin"
    And I expand "Site administration" node
    # Front page settings also has a "Users" node.
    And I expand "Users" node
    And I expand "Users" node
    And I expand "Accounts" node
    When I follow "Browse list of users"

  @javascript
  Scenario: Filtering id numbers - with case "is empty"
    # We should see see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@moodlemoodle.com" in the "users" "table"
    And I should see "Teacher" in the "users" "table"
    And I should see "Student1" in the "users" "table"
    And I should see "Student2" in the "users" "table"
    And I should see "Student3" in the "users" "table"
    And I follow "Show more..."
    And I set the field "id_idnumber_op" to "is empty"
    When I press "Add filter"
    # We should see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@moodlemoodle.com" in the "users" "table"
    And I should not see "Teacher" in the "users" "table"
    And I should not see "Student1" in the "users" "table"
    And I should not see "Student2" in the "users" "table"
    And I should not see "Student3" in the "users" "table"

  @javascript
  Scenario Outline: Filtering id numbers - with all other cases
    # We should see see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@moodlemoodle.com" in the "users" "table"
    And I should see "Teacher" in the "users" "table"
    And I should see "Student1" in the "users" "table"
    And I should see "Student2" in the "users" "table"
    And I should see "Student3" in the "users" "table"
    And I follow "Show more..."
    And I set the field "id_idnumber_op" to "<Category>"
    And I set the field "idnumber" to "<Argument>"
    When I press "Add filter"
    Then I should <Admin's Visibility> "moodle@moodlemoodle.com" in the "users" "table"
    And I should <Teacher's Vis> "Teacher" in the "users" "table"
    And I should <S1's Vis> "Student1" in the "users" "table"
    And I should <S2's Vis> "Student2" in the "users" "table"
    And I should <S3's Vis> "Student3" in the "users" "table"

Examples:
    | Category        | Argument | Admin's Visibility | Teacher's Vis | S1's Vis | S2's Vis | S3's Vis |
    | contains        | 0        | not see            | see           | see      | see      | see      |
    | doesn't contain | 2        | see                | not see       | see      | not see  | see      |
    | is equal to     | 2000000  | not see            | not see       | not see  | see      | not see  |
    | starts with     | 0        | not see            | see           | see      | not see  | not see  |
    | ends with       | 0        | not see            | not see       | not see  | see      | see      |