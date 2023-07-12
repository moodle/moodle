@core @core_grades @javascript
Feature: Teachers can toggle the visibility of the grade categories in the Gradebook setup page.
  In order to focus only on the information that I am interested in
  As a teacher
  I need to be able to easily toggle the visibility of grade categories in the Gradebook setup page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course   | C1        | 0        |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher 1 | 1         | teacher1@example.com  | t1        |
      | teacher2  | Teacher 2 | 2         | teacher2@example.com  | t2        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | teacher2  | C1     | editingteacher |
    And the following "grade categories" exist:
      | fullname   | course |
      | Category 1 | C1     |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro             |
      | assign   | C1     | a1       | Test assignment one | Submit something! |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro             | gradecategory |
      | assign   | C1     | a2       | Test assignment two | Submit something! | Category 1    |
    And the following "grade items" exist:
      | itemname     | grademax | course | gradecategory |
      | Manual grade | 40       | C1     | Category 1    |
    And I am on the "Course" "grades > gradebook setup" page logged in as "teacher1"

  Scenario: A teacher can collapse and expand grade categories in the Gradebook setup page
    Given the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Collapse" "link" should exist in the "Category 1" "table_row"
    # Collapse the grade category 'Category 1'.
    When I click on "Collapse" "link" in the "Category 1" "table_row"
    Then the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "user-grades" table:
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
    # Expand the grade category 'Category 1'.
    And I click on "Expand" "link" in the "Category 1" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Collapse" "link" should exist in the "Category 1" "table_row"
    # Collapse again the grade category 'Category 1'.
    And I click on "Collapse" "link" in the "Category 1" "table_row"
    # Collapse the grade category 'Course'.
    And I click on "Collapse" "link" in the "Course" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
    And I should see "Course" in the "setup-grades" "table"
    And "Expand" "link" should exist in the "Course" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    # Expand the grade category 'Course'. 'Category 1' should be still collapsed.
    And I click on "Expand" "link" in the "Course" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |

  Scenario: A teacher can see the aggregated max grade for a grade category even when the category is collapsed
    Given the following should exist in the "setup-grades" table:
      | Name             | Max grade |
      | Course           |           |
      | Category 1       |           |
      | Category 1 total | 140.00    |
      | Course total     | 240.00    |
    # Collapse the grade category 'Category 1'. The aggregated max grade should now be displayed within the 'Category 1' row.
    When I click on "Collapse" "link" in the "Category 1" "table_row"
    Then the following should exist in the "setup-grades" table:
      | Name             | Max grade |
      | Course           |           |
      | Category 1       | 140.00    |
      | Course total     | 240.00    |
    And I should not see "Category 1 total" in the "setup-grades" "table"
    # Collapse the grade category 'Course'. The aggregated max grade should now be displayed within the 'Course' row.
    And I click on "Collapse" "link" in the "Course" "table_row"
    And "Course" row "Max grade" column of "setup-grades" table should contain "240.00"
    And I should not see "Course total" in the "setup-grades" "table"
    # Expand the grade category 'Course'. The aggregated max grade should not be displayed within the 'Course' row anymore.
    And I click on "Expand" "link" in the "Course" "table_row"
      | Name             | Max grade |
      | Course           |           |
      | Category 1       | 140.00    |
      | Course total     | 240.00    |

  Scenario: A teacher can collapse and expand grade categories in the Gradebook setup when moving grade items
    Given I click on "Move" "link" in the "Test assignment one" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Collapse" "link" should exist in the "Category 1" "table_row"
    # Collapse the grade category 'Category 1'.
    When I click on "Collapse" "link" in the "Category 1" "table_row"
    Then the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
    # Expand the grade category 'Category 1'.
    And I click on "Expand" "link" in the "Category 1" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Collapse" "link" should exist in the "Category 1" "table_row"
    # Collapse again the grade category 'Category 1'.
    And I click on "Collapse" "link" in the "Category 1" "table_row"
    # Collapse the grade category 'Course'.
    And I click on "Collapse" "link" in the "Course" "table_row"
    And I should see "Course" in the "setup-grades" "table"
    And "Expand" "link" should exist in the "Course" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    # Expand the grade category 'Course'. 'Category 1' should be still collapsed.
    And I click on "Expand" "link" in the "Course" "table_row"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |

  Scenario: Previously collapsed categories are still shown as collapsed when a teacher navigates back to Gradebook setup
    # Collapse the grade category 'Category 1' and navigate to the course homepage.
    Given I click on "Collapse" "link" in the "Category 1" "table_row"
    # Navigate back to Gradebook setup and confirm that the category 'Category 1' is still collapsed.
    When I am on the "Course" "grades > gradebook setup" page
    Then the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |

  Scenario: Previously collapsed categories are still shown as collapsed when a teacher is moving grade items in Gradebook setup
    # Collapse the grade category 'Category 1'.
    Given I click on "Collapse" "link" in the "Category 1" "table_row"
    # Attempt to move a grade item and confirm that the category 'Category 1' is still collapsed.
    When I click on "Move" "link" in the "Test assignment one" "table_row"
    Then the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |

  Scenario: Grade categories are shown as collapsed only to the teacher that collapsed them
    # Collapse the grade category 'Category 1'.
    Given I click on "Collapse" "link" in the "Category 1" "table_row"
    # Log in as teacher2 and confirm that the category 'Category 1' is not collapsed.
    When I am on the "Course" "grades > gradebook setup" page logged in as "teacher2"
    Then the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Collapse" "link" should exist in the "Category 1" "table_row"
    # Log in as teacher1 and confirm that the category 'Category 1' is still collapsed.
    And I am on the "Course" "grades > gradebook setup" page logged in as "teacher1"
    And the following should exist in the "setup-grades" table:
      | Name                |
      | Course              |
      | Test assignment one |
      | Category 1          |
      | Course total        |
    And "Collapse" "link" should exist in the "Course" "table_row"
    And "Expand" "link" should exist in the "Category 1" "table_row"
    And the following should not exist in the "setup-grades" table:
      | Name                |
      | Test assignment two |
      | Manual grade        |
      | Category 1 total    |
