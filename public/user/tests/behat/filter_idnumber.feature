@core @core_user
Feature: Filter users by idnumber
  As a system administrator
  I need to be able to filter users by their ID number
  So that I can quickly find users based on an external key.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher  | 1 | teacher@example.com  | 0000002 |
      | student1 | Student1 | 1 | student1@example.com | 0000003 |
      | student2 | Student2 | 1 | student2@example.com | 2000000 |
      | student3 | Student3 | 1 | student3@example.com | 3000000 |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration

  @javascript
  Scenario: Filtering id numbers - with case "is empty"
    # We should see see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@example.com" in the "reportbuilder-table" "table"
    And I should see "Teacher" in the "reportbuilder-table" "table"
    And I should see "Student1" in the "reportbuilder-table" "table"
    And I should see "Student2" in the "reportbuilder-table" "table"
    And I should see "Student3" in the "reportbuilder-table" "table"
    And I click on "Filters" "button"
    And I set the following fields in the "ID number" "core_reportbuilder > Filter" to these values:
      | ID number operator | Is empty |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    # We should see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@example.com" in the "reportbuilder-table" "table"
    And I should not see "Teacher" in the "reportbuilder-table" "table"
    And I should not see "Student1" in the "reportbuilder-table" "table"
    And I should not see "Student2" in the "reportbuilder-table" "table"
    And I should not see "Student3" in the "reportbuilder-table" "table"

  @javascript
  Scenario Outline: Filtering id numbers - with all other cases
    # We should see see admin on the user list, the following e-mail is admin's e-mail.
    Then I should see "moodle@example.com" in the "reportbuilder-table" "table"
    And I should see "Teacher" in the "reportbuilder-table" "table"
    And I should see "Student1" in the "reportbuilder-table" "table"
    And I should see "Student2" in the "reportbuilder-table" "table"
    And I should see "Student3" in the "reportbuilder-table" "table"
    And I click on "Filters" "button"
    And I set the following fields in the "ID number" "core_reportbuilder > Filter" to these values:
      | ID number operator | <Category> |
      | ID number value    | <Argument> |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    Then I should <Admin's Visibility> "moodle@example.com" in the "reportbuilder-table" "table"
    And I should <Teacher's Vis> "Teacher" in the "reportbuilder-table" "table"
    And I should <S1's Vis> "Student1" in the "reportbuilder-table" "table"
    And I should <S2's Vis> "Student2" in the "reportbuilder-table" "table"
    And I should <S3's Vis> "Student3" in the "reportbuilder-table" "table"

    Examples:
      | Category         | Argument | Admin's Visibility | Teacher's Vis | S1's Vis | S2's Vis | S3's Vis |
      | Contains         | 0        | not see            | see           | see      | see      | see      |
      | Does not contain | 2        | see                | not see       | see      | not see  | see      |
      | Is equal to      | 2000000  | not see            | not see       | not see  | see      | not see  |
      | Starts with      | 0        | not see            | see           | see      | not see  | not see  |
      | Ends with        | 0        | not see            | not see       | not see  | see      | see      |
