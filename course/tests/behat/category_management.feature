@core @core_course
Feature: Test category management actions
  As a moodle admin
  Test we can create a category
  Test we can create a sub category
  Test we can edit a category
  Test we can delete a category
  Test deleting categories interface when user permissions are restricted
  Test we can move a category
  Test we can assign roles within a category
  Test we can set permissions on a category
  Test we can manage cohorts within a category
  Test we can manage filters for a category

  Scenario: Test editing a category through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "edit" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Edit category settings"
    And I should see "Cat 1"
    And I press "Cancel"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I click on "edit" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Edit category settings"
    And I should see "Cat 1"
    And I set the following fields to these values:
      | Category name | Category 1 (edited) |
      | Category ID number | CAT1e |
    And I press "Save changes"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Category 1 (edited)" in the "#category-listing" "css_element"
    And I should see "Category 1 (edited)" in the "#course-listing h3" "css_element"

  @javascript
  Scenario: Test deleting a categories through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 3 | 0 | CAT3 |

    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT3 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I click on "delete" action for "Cat 2" in management category listing
    # Redirect
    And I should see "Delete category: Cat 2"
    And I should see "Contents of Cat 2"
    And I should see "This category is empty"
    And I press "Cancel"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I click on "delete" action for "Cat 2" in management category listing
    # Redirect
    And I should see "Delete category: Cat 2"
    And I should see "Contents of Cat 2"
    And I should see "This category is empty"
    And I press "Delete"
    # Redirect
    And I should see "Delete category: Cat 2"
    And I should see "Deleted course category Cat 2"
    And I press "Continue"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should not see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I click on "delete" action for "Cat 3" in management category listing
    # Redirect
    And I should see "Delete category: Cat 3"
    And I set the following fields to these values:
      | What to do | Move contents to another category |
      | Move into  | Cat 1                             |
    And I press "Delete"
    # Redirect
    And I should see "Delete category: Cat 3"
    And I should see "Deleted course category Cat 3"
    And I press "Continue"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should not see "Cat 2" in the "#category-listing ul" "css_element"
    And I should not see "Cat 3" in the "#category-listing ul" "css_element"
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"

  Scenario: Test deleting categories action is not listed when permissions are restricted.
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager   | Manager   | Manager  |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
    And the following "courses" exist:
      | category | fullname | shortname |
      | CAT1 | Course 1 | C1 |
    And the following "system role assigns" exist:
      | user | role | contextlevel |
      | manager | manager | System |
    And the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | moodle/course:delete | Prevent | manager | Course | C1 |
      | moodle/course:create | Prevent | manager | System |    |

    When I log in as "manager"
    And I go to the courses management page
    Then I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I open the action menu for "Cat 1" in management category listing
    And "Cat 1" category actions menu should not have "Delete" item

  Scenario: Test deleting categories interface when course create permission is restricted in system.
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager   | Manager   | Manager  |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
    And the following "courses" exist:
      | category | fullname | shortname |
      | CAT1 | Course 1 | C1 |
    And the following "system role assigns" exist:
      | user | role | contextlevel |
      | manager | manager | System |
    And the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | moodle/course:delete | Allow | manager | Course | C1 |
      | moodle/course:create | Prevent | manager | System |    |

    When I log in as "manager"
    And I go to the courses management page
    And I open the action menu for "Cat 1" in management category listing
    Then "Cat 1" category actions menu should have "Delete" item
    And I click on "delete" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Delete category: Cat 1"
    And I should see "Contents of Cat 1"
    And I should see "Delete all - cannot be undone"
    And "What to do" "select" should not exist
    And "Move into" "select" should not exist
    And I press "Cancel"

  Scenario: Test deleting categories interface when course delete permission is restricted for category.
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager   | Manager   | Manager  |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
    And the following "courses" exist:
      | category | fullname | shortname |
      | CAT1 | Course 1 | C1 |
    And the following "system role assigns" exist:
      | user | role | contextlevel |
      | manager | manager | System |
    And the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | moodle/course:delete | Prevent | manager | Course | C1 |
      | moodle/course:create | Allow | manager | System |    |

    When I log in as "manager"
    And I go to the courses management page
    And I open the action menu for "Cat 1" in management category listing
    Then "Cat 1" category actions menu should have "Delete" item
    And I click on "delete" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Delete category: Cat 1"
    And I should see "Contents of Cat 1"
    And I should see "Move contents to another category"
    And "What to do" "select" should not exist
    And "Move into" "select" should exist
    And the "Move into" select box should contain "Cat 2"
    And the "Move into" select box should contain "Category 1"
    And I press "Cancel"

  @javascript
  Scenario: Test deleting categories interface when course create permissions are restricted for some categories.
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager   | Manager   | Manager  |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
    And the following "courses" exist:
      | category | fullname | shortname |
      | CAT1 | Course 1 | C1 |
    And the following "system role assigns" exist:
      | user | role | contextlevel |
      | manager | manager | System |
    And the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | moodle/course:delete | Allow | manager | Course | C1 |
      | moodle/course:create | Allow | manager | System |    |
      | moodle/course:create | Prevent | manager | Category | CAT2  |

    When I log in as "manager"
    And I go to the courses management page
    And I open the action menu for "Cat 1" in management category listing
    Then "Cat 1" category actions menu should have "Delete" item
    And I click on "delete" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Delete category: Cat 1"
    And I should see "Contents of Cat 1"
    And "What to do" "select" should exist
    And I expand the "Move into" autocomplete
    And "Cat 2" "autocomplete_suggestions" should not exist
    And "Category 1" "autocomplete_selection" should be visible
    And I set the field "What to do" to "Delete all - cannot be undone"
    And "Move into" "select" should not be visible
    And I press "Cancel"

  Scenario: Test I can assign roles for a category through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "permissions" action for "Cat 1" in management category listing
    And I select "Assign roles" from the "jump" singleselect
    # Redirect
    And I should see "Assign roles in Category: Cat 1"
    And I should see "Please choose a role to assign"

  Scenario: Test I can set access permissions for a category through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "permissions" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Permissions in Category: Cat 1"
    And I click on "Back to Category: Cat 1" "link"
    # Redirect
    And I should see "Cat 1" in the "h1" "css_element"

  Scenario: Test clicking to manage cohorts for a category through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "cohorts" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Category: Cat 1: available cohorts"

  Scenario: Test configuring filters for a category
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "filters" action for "Cat 1" in management category listing
    # Redirect
    And I should see "Filter settings in Category: Cat 1"
    And I click on "Back to Category: Cat 1" "link"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-listing h3" "css_element"

  @javascript
  Scenario: Test that I can create a category and view it in the management interface
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "Create new category" "link" in the ".category-listing-actions" "css_element"
    # Redirect.
    And I should see "Add new category"
    And I set the following fields to these values:
      | Parent category | Top |
      | Category name | Test category 2 |
      | Category ID number | TC2 |
    And I press "Create category"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Test category 2" in the "#course-listing h3" "css_element"
    And I should see category listing "Cat 1" before "Test category 2"
    And I should see "No courses in this category"
    And I click on "createnewsubcategory" action for "Test category 2" in management category listing
    # Redirect
    And I should see "Add new category"
    And I set the following fields to these values:
      | Parent category | Top |
      | Category name | Test category 3 |
      | Category ID number | TC3 |
    And I press "Create category"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Test category 3" in the "#course-listing h3" "css_element"
    And I should see category listing "Cat 1" before "Test category 2"
    And I should see "No courses in this category"

  @javascript
  Scenario: Test moving a categories through the management interface.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 3 | 0 | CAT3 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I select category "Cat 2" in the management interface
    And I select category "Cat 3" in the management interface
    And I set the field "menumovecategoriesto" to "Cat 1"
    When I press "bulkmovecategories"
    # Redirect
    And I click on category "Cat 1" in the management interface
    # Redirect
    Then I should see category "CAT3" as subcategory of "CAT1" in the management interface
    And I move category "Cat 3" to top level in the management interface
    # Redirect
    And I should not see category "CAT3" as subcategory of "CAT1" in the management interface
    Then I should see category "CAT2" as subcategory of "CAT1" in the management interface

  @javascript
  Scenario: Test bulk action is shown only when some category/course is selected
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 3 | 0 | CAT3 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT3 | Course 1 | Course 1 | C1 |
      | CAT3 | Course 2 | Course 2 | C2 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    When I set the field "selectsortby" to "allcategories"
    Then the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And the "movecategoriesto" "select" should be disabled
    And I select category "Cat 2" in the management interface
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I set the field "selectsortby" to "selectedcategories"
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I unselect category "Cat 2" in the management interface
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And I select category "Cat 3" in the management interface
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I select category "Cat 2" in the management interface
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I unselect category "Cat 2" in the management interface
    And I unselect category "Cat 3" in the management interface
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And the "movecoursesto" "select" should be disabled
    And I click on category "Cat 3" in the management interface
    #Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And I should see "Course 2" in the "#course-listing ul.course-list" "css_element"
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And the "movecoursesto" "select" should be disabled
    And I select course "Course 1" in the management interface
    And the "movecoursesto" "select" should be enabled
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And I select course "Course 2" in the management interface
    And the "movecoursesto" "select" should be enabled
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled
    And I select category "Cat 3" in the management interface
    And the "movecoursesto" "select" should be enabled
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I unselect course "Course 2" in the management interface
    And the "movecoursesto" "select" should be enabled
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I unselect course "Course 1" in the management interface
    And the "movecoursesto" "select" should be disabled
    And the "movecategoriesto" "select" should be enabled
    And the "resortcategoriesby" "select" should be enabled
    And the "resortcoursesby" "select" should be enabled
    And I unselect category "Cat 3" in the management interface
    And the "movecoursesto" "select" should be disabled
    And the "movecategoriesto" "select" should be disabled
    And the "resortcategoriesby" "select" should be disabled
    And the "resortcoursesby" "select" should be disabled

  Scenario: Test that is not possible to create a course category with a duplicate idnumber
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And I log in as "admin"
    And I navigate to "Courses > Add a category" in site administration
    And I set the following fields to these values:
      | Category name | Test duplicate |
      | Category ID number | CAT1 |
    When I press "Create category"
    Then I should see "ID number is already used for another category"

  Scenario: Test that is possible to remove an idnumber from a course category
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 ||
    And I log in as "admin"
    And I go to the courses management page
    And I should see "CAT1" in the "#category-listing" "css_element"
    When I click on "edit" action for "Cat 1" in management category listing
    And I set the following fields to these values:
      | Category name | Category 1 (edited) |
      | Category ID number ||
    And I press "Save changes"
    # Redirect
    Then I should see "Category 1 (edited)" in the "#category-listing" "css_element"
    And I should not see "CAT1" in the "#course-listing" "css_element"
