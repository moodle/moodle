@javascript @core_course
Feature: Course category breadcrumbs navigation
  To navigate around the course category pages
  As an admin user
  I should see breadcrumbs

  Background:
    Given the following "blocks" exist:
      | blockname  | contextlevel | reference | defaultregion |
      | navigation | System       | 1         | side-post     |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |

  Scenario: Admin user navigates to 'course category management' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    When I follow "Cat 1"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Manage course categories and courses" in the "region-main" "region"

  Scenario: Admin user navigates to category 'view' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I click on "view" action for "Cat 1" in management category listing
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to 'add new course' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I click on "Create new course" "link"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"
    And I should see "Add a new course" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Add a new course" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to 'add category' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I click on "Create new category" "link"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"
    And I should see "Add a category" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Add new category" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to a subcategory 'management' page
    Given the following "categories" exist:
      | name     | category | idnumber |
      | Subcat 1 | CAT1     | SUBCAT1  |
    And I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I follow "Subcat 1"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".page-context-header" "css_element"
    And I should see "Manage course categories and courses" in the "region-main" "region"

  Scenario: Admin user navigates to a subcategory 'view' page
    Given the following "categories" exist:
      | name     | category | idnumber |
      | Subcat 1 | CAT1     | SUBCAT1  |
    And I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I follow "Subcat 1"
    When I click on "view" action for "Subcat 1" in management category listing
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".page-context-header" "css_element"
    And I should see "Subcat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to 'add new course' page within a subcategory
    Given the following "categories" exist:
      | name     | category | idnumber |
      | Subcat 1 | CAT1     | SUBCAT1  |
    And I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I follow "Subcat 1"
    When I click on "Create new course" "link"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"
    And I should see "Add a new course" in the ".breadcrumb" "css_element"
    And I should see "Subcat 1" in the ".page-context-header" "css_element"
    And I should see "Add a new course" in the "region-main" "region"
    And I should see "Subcat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'settings' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Settings" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Settings" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Edit category settings" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'permissions' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Permissions" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Permissions" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Permissions in Category: Cat 1" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'assign roles' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I navigate to "Permissions" in current page administration
    When I select "Assign roles" from the "jump" singleselect
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Assign roles" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Assign roles in Category: Cat 1" in the "region-main" "region"

  Scenario: Admin user navigates to category 'check permissions' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I navigate to "Permissions" in current page administration
    When I select "Check permissions" from the "jump" singleselect
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Check permissions" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Check permissions in Category: Cat 1" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'cohorts' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Cohorts" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Cohorts" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Category: Cat 1: available cohorts" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'add new cohort' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I navigate to "Cohorts" in current page administration
    When I follow "Add new cohort"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Cohorts" in the ".breadcrumb" "css_element"
    And I should see "Add new cohort" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Add new cohort" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'upload cohorts' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I navigate to "Cohorts" in current page administration
    When I follow "Upload cohorts"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Cohorts" in the ".breadcrumb" "css_element"
    And I should see "Upload cohorts" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Upload cohorts" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'filters' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Filters" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Filters" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Filter settings in Category: Cat 1" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'restore course' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Restore course" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Restore course" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Import a backup file" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'manage backup files' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    And I navigate to "Restore course" in current page administration
    When I press "Manage backup files"
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Restore course" in the ".breadcrumb" "css_element"
    And I should see "Manage backup files" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Manage backup files" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"

  Scenario: Admin user navigates to category 'content bank' page
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I follow "Cat 1"
    When I navigate to "Content bank" in current page administration
    Then I should see "Courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Content bank" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".page-context-header" "css_element"
    And I should see "Content bank" in the "region-main" "region"
    And I should see "Cat 1" in the ".block_navigation .active_tree_node" "css_element"
