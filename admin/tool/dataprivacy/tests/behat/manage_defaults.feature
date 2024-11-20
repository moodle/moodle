@tool @tool_dataprivacy @javascript
Feature: Manage data registry defaults
  As the privacy officer
  In order to manage the data registry
  I need to be able to manage the default data categories and data storage purposes for various context levels.

  Background:
    Given I log in as "admin"
    And the following "categories" exist:
      | name                   | idnumber | category |
      | Science and technology | scitech  |          |
      | Physics                | st-phys  | scitech  |
    And the following "courses" exist:
      | fullname                  | shortname   | category |
      | Fundamentals of physics 1 | Physics 101 | st-phys  |
    And the following "activities" exist:
      | activity | name         | idnumber | course      |
      | assign   | Assignment 1 | assign1  | Physics 101 |
      | forum    | Forum 1      | forum1   | Physics 101 |
    And the following "blocks" exist:
      | blockname    | contextlevel | reference   | pagetypepattern | defaultregion |
      | online_users | Course       | Physics 101 | course-view-*   | site-post     |
    And the following data privacy "categories" exist:
      | name          |
      | Site category |
      | Category 1    |
      | Category 2    |
    And the following data privacy "purposes" exist:
      | name         | retentionperiod |
      | Site purpose | P10Y           |
      | Purpose 1    | P3Y            |
      | Purpose 2    | P5Y            |
    And I set the site category and purpose to "Site category" and "Site purpose"

  # Setting a default for course categories should apply to everything beneath that category.
  Scenario: Set course category data registry defaults
    Given I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I should see "Inherit"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years"
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I should see "3 years"
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I should see "3 years"
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And I should see "3 years"

  # When Setting a default for course categories, and overriding a specific category, only that category and its
  # children will be overridden.
  # If any child is a course category, it will get the default.
  Scenario: Set course category data registry defaults with override
    Given I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    And I press "Save changes"
    And I should see "Category 1"
    And I should see "Purpose 1"
    And I set the category and purpose for the course category "scitech" to "Category 2" and "Purpose 2"
    When I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    Then the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years"
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    # Physics 101 is also a category, so it will get the category default.
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I should see "3 years"
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I should see "3 years"
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And I should see "3 years"

  # When overriding a specific category, only that category and its children will be overridden.
  Scenario: Set course category data registry defaults with override
    Given I set the category and purpose for the course category "scitech" to "Category 2" and "Purpose 2"
    When I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    Then the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years"
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    # Physics 101 is also a category, so it will get the category default.
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I should see "5 years"
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I should see "5 years"
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And I should see "5 years"

  # Resetting instances removes custom values.
  Scenario: Set course category data registry defaults with override
    Given I set the category and purpose for the course category "scitech" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I click on "Reset instances with custom values" "checkbox"
    And I press "Save changes"
    And I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    Then the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years"

  Scenario: Set course data registry defaults
    Given I set the category and purpose for the course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Courses" "link" in the "#region-main" "css_element"
    And I should see "Inherit"
    And I should not see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years (after the course end date)"
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I should see "5 years"
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And I should see "5 years"

  Scenario: Set course data registry defaults with override
    Given I set the category and purpose for the course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Courses" "link" in the "#region-main" "css_element"
    And I should see "Inherit"
    And I should not see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    And I click on "Reset instances with custom values" "checkbox"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years (after the course end date)"
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I should see "3 years"
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And I should see "3 years"

  Scenario: Set module level data registry defaults
    Given I set the category and purpose for the "assign1" "assign" in course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Activity modules" "link"
    And I should see "Inherit"
    And I should see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years (after the course end date)"

  Scenario: Set module level data registry defaults with override
    Given I set the category and purpose for the "assign1" "assign" in course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Activity modules" "link"
    And I should see "Inherit"
    And I should see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    And I click on "Reset instances with custom values" "checkbox"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I click on "Forum 1 (Forum)" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years (after the course end date)"

  Scenario: Set data registry defaults for an activity module
    Given I set the category and purpose for the "assign1" "assign" in course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Activity modules" "link"
    And I should see "Inherit"
    And I should see "Add a new module default"
    And I press "Add a new module default"
    And I set the field "Activity module" to "Assignment"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I press "Save changes"
    Then I should see "Category 1" in the "Assignment" "table_row"
    And I should see "Purpose 1" in the "Assignment" "table_row"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years (after the course end date)"

  Scenario: Set data registry defaults for an activity module with override
    Given I set the category and purpose for the "assign1" "assign" in course "Physics 101" to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Activity modules" "link"
    And I should see "Inherit"
    And I should see "Add a new module default"
    And I press "Add a new module default"
    And I set the field "Activity module" to "Assignment"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    And I click on "Reset instances with custom values" "checkbox"
    When I press "Save changes"
    Then I should see "Category 1" in the "Assignment" "table_row"
    And I should see "Purpose 1" in the "Assignment" "table_row"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Activities and resources" "link"
    And I wait until the page is ready
    And I click on "Assignment 1 (Assignment)" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years (after the course end date)"

  Scenario: Set block category data registry defaults
    Given I set the category and purpose for the "online_users" block in the "Physics 101" course to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Blocks" "link"
    And I should see "Inherit"
    And I should not see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Blocks" "link"
    And I wait until the page is ready
    And I click on "Online users" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Category 2"
    And the field "purposeid" matches value "Purpose 2"
    And I should see "5 years (after the course end date)"

  Scenario: Set course category data registry defaults with override
    Given I set the category and purpose for the "online_users" block in the "Physics 101" course to "Category 2" and "Purpose 2"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Set defaults" "link"
    And I click on "Blocks" "link"
    And I should see "Inherit"
    And I should not see "Add a new module default"
    And I press "Edit"
    And I set the field "Category" to "Category 1"
    And I set the field "Purpose" to "Purpose 1"
    And I click on "Reset instances with custom values" "checkbox"
    When I press "Save changes"
    Then I should see "Category 1"
    And I should see "Purpose 1"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Science and technology" "link"
    And I wait until the page is ready
    And I click on "Courses" "link" in the ".data-registry" "css_element"
    And I wait until the page is ready
    And I click on "Physics 101" "link"
    And I wait until the page is ready
    And I click on "Blocks" "link"
    And I wait until the page is ready
    And I click on "Online users" "link"
    And I wait until the page is ready
    And the field "categoryid" matches value "Not set (use the default value)"
    And the field "purposeid" matches value "Not set (use the default value)"
    And I should see "3 years (after the course end date)"
