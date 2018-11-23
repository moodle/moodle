@core @core_user
Feature: Tables can be sorted by additional names
  In order to sort fields by additional names
  As a user
  I need to browse to a page with users in a table.

  Background:
    Given the following "users" exist:
    | username | firstname | lastname | middlename | alternatename | email | idnumber |
    | student1 | Annie | Edison | Faith | Anne | student1@example.com | s1 |
    | student2 | George | Bradley | David | Gman | student2@example.com | s2 |
    | student3 | Travis | Sutcliff | Peter | Mr T | student3@example.com | s3 |
    And I log in as "admin"
    And I navigate to "Users > Permissions > User policies" in site administration
    And the following config values are set as admin:
    | fullnamedisplay | firstname middlename lastname |
    | alternativefullnameformat | firstname middlename alternatename lastname |

  @javascript
  Scenario: All user names are show and sortable in the administration user list.
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "users" table:
    | First name / Middle name / Alternate name / Surname | Email address |
    | Admin User | moodle@example.com |
    | Annie Faith Anne Edison | student1@example.com |
    | George David Gman Bradley | student2@example.com |
    | Travis Peter Mr T Sutcliff | student3@example.com |
    And "Annie Faith Anne Edison" "table_row" should appear before "George David Gman Bradley" "table_row"
    And "George David Gman Bradley" "table_row" should appear before "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Middle name"
    And "George David Gman Bradley" "table_row" should appear before "Annie Faith Anne Edison" "table_row"
    And "Annie Faith Anne Edison" "table_row" should appear before "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Middle name"
    And "George David Gman Bradley" "table_row" should appear after "Annie Faith Anne Edison" "table_row"
    And "Annie Faith Anne Edison" "table_row" should appear after "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Alternate name"
    And "Annie Faith Anne Edison" "table_row" should appear before "George David Gman Bradley" "table_row"
    And "George David Gman Bradley" "table_row" should appear before "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Alternate name"
    And "Annie Faith Anne Edison" "table_row" should appear after "George David Gman Bradley" "table_row"
    And "George David Gman Bradley" "table_row" should appear after "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Surname"
    And "George David Gman Bradley" "table_row" should appear before "Annie Faith Anne Edison" "table_row"
    And "Annie Faith Anne Edison" "table_row" should appear before "Travis Peter Mr T Sutcliff" "table_row"
    And I follow "Surname"
    And "George David Gman Bradley" "table_row" should appear after "Annie Faith Anne Edison" "table_row"
    And "Annie Faith Anne Edison" "table_row" should appear after "Travis Peter Mr T Sutcliff" "table_row"
