@ou @ou_vle @report @report_customsql @javascript
Feature: Ad-hoc database queries report
  As an administrator
  In order to understand what is going on in my Moodle site
  I need to be able to run arbitrary queries against the database

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
      | Course 2 | C2 | 0 | 1 |
      | Course 3 | C3 | 0 | 2 |
      | Course 4 | C4 | 0 | 2 |
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | T1 | Teqacher1 |
      | student1 | S1 | Student1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"

  Scenario: Create a query, edit it and then delete it.
    When I navigate to "Ad-hoc database queries" node in "Site administration > Reports"
    And I follow "Ad-hoc database queries"

    # start creating the first query
    And I press "Add a new query"
    And I set the following fields to these values:
      | Query name  | Query 1                                              |
      | Description | Description 1                                        |
      | Query SQL   | SELECT * FROM {user} u where u.username = 'teacher1' |

    And I press "Verify the Query SQL text and update the form"
    And I press "Save changes"
    Then I should see "Query 1"
    And I should see "Description 1"
    And I should see "Download these results as CSV"

    # Edit this query
    When I follow "Edit this query"
    Then the following fields match these values:
      | Query name  | Query 1                                              |
      | Description | Description 1                                        |
      | Query SQL   | SELECT * FROM {user} u where u.username = 'teacher1' |

    When I set the field "Query name" to "Query 2"
    And I set the field "Description" to "Description 2"
    And I set the field "Query SQL" to "SELECT * FROM {course} c where c.shortname = 'C2' "
    And I press "Verify the Query SQL text and update the form"
    And I press "Save changes"
    Then I should see "Query 2"
    And I should see "Description 2"
    And I follow "Delete this query"
    And I press "Yes"

  Scenario: Create a query where the query cannot be found in DB.
    When I navigate to "Ad-hoc database queries" node in "Site administration > Reports"
    And I follow "Ad-hoc database queries"

    # start creating a query
    And I press "Add a new query"
    And I set the field "Query name" to "Query should not be found"
    And I set the field "Description" to "This query cannot be found in DB"
    And I set the field "Query SQL" to "SELECT * FROM {user} u where u.username = 'teacher5' "
    And I press "Verify the Query SQL text and update the form"
    And I press "Save changes"
    Then I should see "This query did not return any data."

  Scenario: Create 2 categories and create a query for each category.
    # Do the basic
    When I navigate to "Ad-hoc database queries" node in "Site administration > Reports"
    And I follow "Ad-hoc database queries"
    And I press "Manage report categories"

    # Create the first category and create a query in this category.
    And I press "Add a new category"
    And I set the field "Category name" to "Category 1"
    And I press "Add a new category"
    Then I should see "Category 1"

    And I follow "Ad-hoc database queries"
    And I follow "Category 1"
    Then I should see "No queries available"

    And I press "Add a new query"
    And I set the field "Query name" to "Query1 for category1"
    And I set the field "Description" to "Description of query for cat1"
    And I set the field "Query SQL" to "SELECT * FROM {user} u where u.username = 'teacher1' "
    And I press "Verify the Query SQL text and update the form"
    And I set the field "Select category for this report" to "Category 1"
    And I press "Save changes"
    Then I should see "Query1 for category1"

    And I follow "Ad-hoc database queries"
    And I follow "Category 1"
    Then I should not see "No queries available"

    And I follow "Ad-hoc database queries"
    And I press "Manage report categories"

    # Create another category
    And I press "Add a new category"
    And I set the field "Category name" to "Category 2"
    And I press "Add a new category"
    Then I should see "Manage report categories"

    # Create a query in Category 2
    And I follow "Ad-hoc database queries"
    And I press "Add a new query"
    And I set the field "Query name" to "Query for cat2"
    And I set the field "Description" to "Description of query for cat2"
    And I set the field "Query SQL" to "SELECT * FROM {user} u where u.username = 'teacher1' "
    And I press "Verify the Query SQL text and update the form"
    And I set the field "Select category for this report" to "Category 2"
    And I press "Save changes"
    Then I should see "Query for cat2"

    # Test exand/collapse of categories.
    When I navigate to "Ad-hoc database queries" node in "Site administration > Reports"
    Then I should see "Expand all"
    And I should not see "Query1 for category1"
    When I follow "Category 1"
    And I should see "Query1 for category1"
    And I should not see "Expand all"
    When I follow "Collapse all"
    Then I should not see "Query1 for category1"
    And I should see "Expand all"

  Scenario: Create a query and then edit it by filling most of the elements in the form.
    When I navigate to "Ad-hoc database queries" node in "Site administration > Reports"
    And I follow "Ad-hoc database queries"

    # start creating the first query
    And I press "Add a new query"
    And I set the field "Query name" to "Query 1"
    And I set the field "Description" to "Description 1"
    And I set the field "Query SQL" to "SELECT * FROM {course} c where c.shortname = 'C1' "
    And I press "Verify the Query SQL text and update the form"
    And I set the field "id_runable" to "Scheduled, daily"
    And I press "Save changes"
    Then I should see "Query 1"
    And I should see "Daily"

    And I follow "Edit this query"
    And I set the field "Query name" to "Query 2"
    And I set the field "Description" to "Description 2"
    And I set the field "Query SQL" to "SELECT * FROM {course} c where c.shortname = 'C2' "
    And I press "Verify the Query SQL text and update the form"
    And I set the field "id_runable" to "Scheduled, on the first day of each month"
    And I set the field "Who can access this query" to "moodle/site:config"
    And I set the field "Limit rows returned" to "1"
    And I set the field "The query returns one row, accumulate the results one row at a time" to "1"
    And I press "Save changes"
    Then I should see "Query 2"
    And I should see "Monthly"
