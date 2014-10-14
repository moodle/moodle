@core @core_user
Feature: Tables can be sorted by additional names
  In order to sort fields by additional names
  As a user
  I need to browse to a page with users in a table.

  Background:
    Given the following "users" exist:
    | username | firstname | lastname | middlename | alternatename | email | idnumber |
    | student1 | Annie | Edison | Faith | Anne | student1@mail.com | s1 |
    | student2 | George | Bradley | David | Gman | student2@mail.com | s2 |
    | student3 | Travis | Sutcliff | Peter | Mr T | student3@mail.com | s3 |
    And I log in as "admin"
    And I navigate to "User policies" node in "Site administration > Users > Permissions"
    And I set the following administration settings values:
    | Full name format | firstname middlename lastname |
    | Alternative full name format | firstname middlename alternatename lastname |

  @javascript
  Scenario: All user names are show and sortable in the administration user list.
    Given I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    Then the following should exist in the "users" table:
    | First name / Middle name / Alternate name / Surname | Email address |
    | Admin User | moodle@moodlemoodle.com |
    | Annie Faith Anne Edison | student1@mail.com |
    | George David Gman Bradley | student2@mail.com |
    | Travis Peter Mr T Sutcliff | student3@mail.com |
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
