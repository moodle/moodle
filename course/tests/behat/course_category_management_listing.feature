@core @core_course
Feature: Course category management interface performs as expected
  In order to test JS enhanced display of categories and subcategories.
  As a moodle admin
  I need to expand and collapse categories.

  @javascript
  Scenario Outline: Test general look of management interface
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And I log in as "admin"
    And I go to the courses management page
    And I select "<selected>" from the "Viewing" singleselect
    And I should see "<heading>" in the "h2" "css_element"
    And I should see "<selected>" in the "Viewing" "select"
    And I should see "<pagecontent>" in the "#page-content" "css_element"
    And I should see the "<selected>" management page

    Examples:
      | heading                              | selected                      | pagecontent       |
      | Manage course categories and courses | Course categories and courses | Course categories |
      | Manage course categories             | Course categories             | Course categories |
      | Manage courses                       | Courses                       | Cat 1             |

  @javascript
  Scenario: Test view mode functionality
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | CAT1 | topics |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Course categories" in the "Viewing" "select"
    And the field "jump" matches value "Course categories and courses"
    And I start watching to see if a new page loads
    Then I should see "Course categories and courses" in the "Viewing" "select"
    And I should see "Course categories" in the "Viewing" "select"
    And I should see "Courses" in the "Viewing" "select"
    And I should see "Category 1" in the "#course-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "No courses in this category" in the "#course-listing" "css_element"
    And I click on category "Cat 1" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#course-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Course 1" in the "#course-listing" "css_element"
    Then I should see "Courses" in the "Viewing" "select"
    And I select "Courses" from the "jump" singleselect
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Courses" management page
    And I should see "Cat 1" in the "#course-listing h3" "css_element"
    And I should see "Course 1" in the "#course-listing" "css_element"
    And I click on course "Course 1" in the management interface
    And a new page should have loaded since I started watching
    And I should see the "Courses" management page with a course selected
    And I should see "Cat 1" in the "#course-listing h3" "css_element"
    And I should see "Course 1" in the "#course-listing" "css_element"
    And I should see "Course 1" in the "#course-detail h3" "css_element"
    And I should see "C1" in the "#course-detail .shortname" "css_element"
    And I should see "Course 1" in the "#course-detail .fullname" "css_element"
    And I should see "Topics" in the "#course-detail .format" "css_element"
    And I should see "Cat 1" in the "#course-detail .category" "css_element"

  Scenario: Test displaying of sub categories
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 1-1 | CAT1 | CAT3 |
      | Cat 1-2 | CAT1 | CAT4 |
      | Cat 1-1-1 | CAT3 | CAT5 |
      | Cat 1-1-2 | CAT3 | CAT6 |
      | Cat 2-1 | CAT2 | CAT7 |
      | Cat 2-1-1 | CAT7 | CAT8 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | CAT1 |
      | Course 2 | C2 | CAT1 |
      | Course 3 | C3 | CAT3 |
      | Course 4 | C4 | CAT3 |
      | Course 5 | C5 | CAT5 |
      | Course 6 | C6 | CAT5 |
      | Course 7 | C7 | CAT8 |
      | Course 8 | C8 | CAT8 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click on "Cat 1" "link"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click on "Cat 1-1" "link"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click on "Cat 2" "link"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"

  # This is similar to the above scenario except here we are going to use AJAX
  # to load the categories.
  @javascript @_cross_browser
  Scenario: Test AJAX loading of sub categories
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 1-1 | CAT1 | CAT3 |
      | Cat 1-2 | CAT1 | CAT4 |
      | Cat 1-1-1 | CAT3 | CAT5 |
      | Cat 1-1-2 | CAT3 | CAT6 |
      | Cat 2-1 | CAT2 | CAT7 |
      | Cat 2-1-1 | CAT7 | CAT8 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | CAT1 |
      | Course 2 | C2 | CAT1 |
      | Course 3 | C3 | CAT3 |
      | Course 4 | C4 | CAT3 |
      | Course 5 | C5 | CAT5 |
      | Course 6 | C6 | CAT5 |
      | Course 7 | C7 | CAT8 |
      | Course 8 | C8 | CAT8 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT1" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT3" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT2" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT7" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT1" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT1" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"

  @javascript
  Scenario Outline: Top level categories are displayed correctly when resorted
    Given the following "categories" exist:
      | category | name | idnumber | sortorder |
      | 0 | Social studies | Ext003 | 1 |
      | 0 | Applied sciences | Sci001 | 2 |
      | 0 | Extended social studies | Ext002 | 3 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I set the field "menuselectsortby" to "All categories"
    And I set the field "menuresortcategoriesby" to <sortby>
    And I press "Sort"
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see category listing <cat1> before <cat2>
    And I should see category listing <cat2> before <cat3>

    Examples:
      | sortby | cat1 | cat2 | cat3 |
      | "Sort by Category name ascending"       | "Applied sciences"        | "Extended social studies" | "Social studies" |
      | "Sort by Category name descending"      | "Social studies"          | "Extended social studies" | "Applied sciences" |
      | "Sort by Category ID number ascending"  | "Extended social studies" | "Social studies"          | "Applied sciences" |
      | "Sort by Category ID number descending" | "Applied sciences"        | "Social studies"          | "Extended social studies" |

  @javascript
  Scenario Outline: Sub categories are displayed correctly when resorted
    Given the following "categories" exist:
      | category | name | idnumber | sortorder |
      | 0 | Master cat  | CAT1 | 1 |
      | CAT1 | Social studies | Ext003 | 1 |
      | CAT1 | Applied sciences | Sci001 | 2 |
      | CAT1 | Extended social studies | Ext002 | 3 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on "Master cat" category in the management category listing
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on <sortby> action for "Master cat" in management category listing
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see category listing <cat1> before <cat2>
    And I should see category listing <cat2> before <cat3>

    Examples:
      | sortby | cat1 | cat2 | cat3 |
      | "resortbyname"         | "Applied sciences"        | "Extended social studies" | "Social studies" |
      | "resortbynamedesc"     | "Social studies"          | "Extended social studies" | "Applied sciences" |
      | "resortbyidnumber"     | "Extended social studies" | "Social studies"          | "Applied sciences" |
      | "resortbyidnumberdesc" | "Applied sciences"        | "Social studies"          | "Extended social studies" |

  @javascript
  Scenario Outline: Test courses are displayed correctly after being resorted.
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber | sortorder | timecreated |
      | CAT1 | Social studies | Senior school | Ext003 | 1 | 1000000001 |
      | CAT1 | Applied sciences  | Middle school | Sci001 | 2 | 1000000002 |
      | CAT1 | Extended social studies  | Junior school | Ext002 | 3 | 1000000003 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on "Cat 1" "link"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I open the action menu in ".course-listing-actions" "css_element"
    And I should see "Sort by Course full name ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course full name descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course short name ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course short name descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course ID number ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course ID number descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course time created ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course time created descending" in the ".course-listing-actions" "css_element"
    And I click on <sortby> "link" in the ".course-listing-actions" "css_element"
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see course listing <course1> before <course2>
    And I should see course listing <course2> before <course3>

    Examples:
      | sortby | course1 | course2 | course3 |
      | "Sort by Course full name ascending"     | "Applied sciences"        | "Extended social studies" | "Social studies" |
      | "Sort by Course full name descending"    | "Social studies"          | "Extended social studies" | "Applied sciences" |
      | "Sort by Course short name ascending"    | "Extended social studies" | "Applied sciences"        | "Social studies" |
      | "Sort by Course short name descending"   | "Social studies"          | "Applied sciences"        | "Extended social studies" |
      | "Sort by Course ID number ascending"     | "Extended social studies" | "Social studies"          | "Applied sciences" |
      | "Sort by Course ID number descending"    | "Applied sciences"        | "Social studies"          | "Extended social studies" |
      | "Sort by Course time created ascending"  | "Social studies"          | "Applied sciences"        | "Extended social studies" |
      | "Sort by Course time created descending" | "Extended social studies" | "Applied sciences"        | "Social studies" |

  @javascript
  Scenario: Test course pagination
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
      | CAT1 | Course 2 | Course 2 | C2 |
      | CAT1 | Course 3 | Course 3 | C3 |
      | CAT1 | Course 4 | Course 4 | C4 |
      | CAT1 | Course 5 | Course 5 | C5 |
      | CAT1 | Course 6 | Course 6 | C6 |
      | CAT1 | Course 7 | Course 7 | C7 |
      | CAT1 | Course 8 | Course 8 | C8 |
      | CAT1 | Course 9 | Course 9 | C9 |
      | CAT1 | Course 10 | Course 10 | C10 |
      | CAT1 | Course 11 | Course 11 | C11 |
      | CAT1 | Course 12 | Course 12 | C12 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on "Cat 1" "link"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I open the action menu in ".course-listing-actions" "css_element"
    And I click on "Sort by Course ID number ascending" "link" in the ".course-listing-actions" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see "Per page: 20" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 4"
    And I should see course listing "Course 4" before "Course 5"
    And I should see course listing "Course 5" before "Course 6"
    And I should see course listing "Course 6" before "Course 7"
    And I should see course listing "Course 7" before "Course 8"
    And I should see course listing "Course 8" before "Course 9"
    And I should see course listing "Course 9" before "Course 10"
    And I should see course listing "Course 10" before "Course 11"
    And I should see course listing "Course 11" before "Course 12"
    And "#course-listing .pagination" "css_element" should not exist
    And I open the action menu in ".courses-per-page" "css_element"
    And I should see "5" in the ".courses-per-page" "css_element"
    And I should see "10" in the ".courses-per-page" "css_element"
    And I should see "20" in the ".courses-per-page" "css_element"
    And I should see "All" in the ".courses-per-page" "css_element"
    And I click on "5" "link" in the ".courses-per-page" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 4"
    And I should see course listing "Course 4" before "Course 5"
    And I should not see "Course 6"
    And I should not see "Course 7"
    And I should not see "Course 8"
    And I should not see "Course 9"
    And I should not see "Course 10"
    And I should not see "Course 11"
    And I should not see "Course 12"
    And "#course-listing .pagination" "css_element" should exist
    And I should see "Showing courses 1 to 5 of 12 courses"
    And I should not see "First" in the "#course-listing .pagination" "css_element"
    And I should not see "Prev" in the "#course-listing .pagination" "css_element"
    And I should see "1" in the "#course-listing .pagination" "css_element"
    And I should see "2" in the "#course-listing .pagination" "css_element"
    And I should see "3" in the "#course-listing .pagination" "css_element"
    And I should see "Next" in the "#course-listing .pagination" "css_element"
    And I click on "2" "link" in the "#course-listing .pagination" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see "Course 10" in the "#course-listing" "css_element"
    And I should not see "Course 2" in the "#course-listing" "css_element"
    And I should not see "Course 3" in the "#course-listing" "css_element"
    And I should not see "Course 4" in the "#course-listing" "css_element"
    And I should not see "Course 5" in the "#course-listing" "css_element"
    And I should see course listing "Course 6" before "Course 7"
    And I should see course listing "Course 7" before "Course 8"
    And I should see course listing "Course 8" before "Course 9"
    And I should see course listing "Course 9" before "Course 10"
    And I should not see "Course 11"
    And I should not see "Course 12"
    And "#course-listing .pagination" "css_element" should exist
    And I should see "Showing courses 6 to 10 of 12 courses"
    And I should see "Prev" in the "#course-listing .pagination" "css_element"
    And I should see "1" in the "#course-listing .pagination" "css_element"
    And I should see "2" in the "#course-listing .pagination" "css_element"
    And I should see "3" in the "#course-listing .pagination" "css_element"
    And I should see "Next" in the "#course-listing .pagination" "css_element"
    And I click on "Next" "link" in the "#course-listing .pagination" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see "Course 11"
    And I should not see "Course 2" in the "#course-listing" "css_element"
    And I should not see "Course 3" in the "#course-listing" "css_element"
    And I should not see "Course 4" in the "#course-listing" "css_element"
    And I should not see "Course 5" in the "#course-listing" "css_element"
    And I should not see "Course 6" in the "#course-listing" "css_element"
    And I should not see "Course 7" in the "#course-listing" "css_element"
    And I should not see "Course 8" in the "#course-listing" "css_element"
    And I should not see "Course 9" in the "#course-listing" "css_element"
    And I should not see "Course 10" in the "#course-listing" "css_element"
    And I should see course listing "Course 11" before "Course 12"
    And "#course-listing .pagination" "css_element" should exist
    And I should see "Showing courses 11 to 12 of 12 courses"
    And I should see "Prev" in the "#course-listing .pagination" "css_element"
    And I should see "1" in the "#course-listing .pagination" "css_element"
    And I should see "2" in the "#course-listing .pagination" "css_element"
    And I should see "3" in the "#course-listing .pagination" "css_element"
    And I should not see "Next" in the "#course-listing .pagination" "css_element"
    And I click on "Prev" "link" in the "#course-listing .pagination" "css_element"
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see "Course 10" in the "#course-listing" "css_element"
    And I should not see "Course 2" in the "#course-listing" "css_element"
    And I should not see "Course 3" in the "#course-listing" "css_element"
    And I should not see "Course 4" in the "#course-listing" "css_element"
    And I should not see "Course 5" in the "#course-listing" "css_element"
    And I should see course listing "Course 6" before "Course 7"
    And I should see course listing "Course 7" before "Course 8"
    And I should see course listing "Course 8" before "Course 9"
    And I should see course listing "Course 9" before "Course 10"
    And I should not see "Course 11"
    And I should not see "Course 12"
    And "#course-listing .pagination" "css_element" should exist
    And I should see "Showing courses 6 to 10 of 12 courses"
    And I should see "Prev" in the "#course-listing .pagination" "css_element"
    And I should see "1" in the "#course-listing .pagination" "css_element"
    And I should see "2" in the "#course-listing .pagination" "css_element"
    And I should see "3" in the "#course-listing .pagination" "css_element"
    And I should see "Next" in the "#course-listing .pagination" "css_element"

  Scenario: Test pagination is only shown when required
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
      | CAT1 | Course 2 | Course 2 | C2 |
      | CAT1 | Course 3 | Course 3 | C3 |
      | CAT1 | Course 4 | Course 4 | C4 |
      | CAT1 | Course 5 | Course 5 | C5 |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on "Cat 1" "link"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I open the action menu in ".course-listing-actions" "css_element"
    And I click on "Sort by Course ID number ascending" "link" in the ".course-listing-actions" "css_element"
    # Redirect.
    And I should see "Per page: 20" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 4"
    And I should see course listing "Course 4" before "Course 5"
    And "#course-listing .pagination" "css_element" should not exist
    And I click on "5" "link" in the ".course-listing-actions" "css_element"
    # Redirect
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 4"
    And I should see course listing "Course 4" before "Course 5"
    And "#course-listing .pagination" "css_element" should not exist

  # We need at least 30 courses for this next test.
  @javascript
  Scenario: Test many course pagination
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
      | CAT1 | Course 2 | Course 2 | C2 |
      | CAT1 | Course 3 | Course 3 | C3 |
      | CAT1 | Course 4 | Course 4 | C4 |
      | CAT1 | Course 5 | Course 5 | C5 |
      | CAT1 | Course 6 | Course 6 | C6 |
      | CAT1 | Course 7 | Course 7 | C7 |
      | CAT1 | Course 8 | Course 8 | C8 |
      | CAT1 | Course 9 | Course 9 | C9 |
      | CAT1 | Course 10 | Course 10 | C10 |
      | CAT1 | Course 11 | Course 11 | C11 |
      | CAT1 | Course 12 | Course 12 | C12 |
      | CAT1 | Course 13 | Course 13 | C13 |
      | CAT1 | Course 14 | Course 14 | C14 |
      | CAT1 | Course 15 | Course 15 | C15 |
      | CAT1 | Course 16 | Course 16 | C16 |
      | CAT1 | Course 17 | Course 17 | C17 |
      | CAT1 | Course 18 | Course 18 | C18 |
      | CAT1 | Course 19 | Course 19 | C19 |
      | CAT1 | Course 20 | Course 20 | C20 |
      | CAT1 | Course 21 | Course 21 | C21 |
      | CAT1 | Course 22 | Course 22 | C22 |
      | CAT1 | Course 23 | Course 23 | C23 |
      | CAT1 | Course 24 | Course 24 | C24 |
      | CAT1 | Course 25 | Course 25 | C25 |
      | CAT1 | Course 26 | Course 26 | C26 |
      | CAT1 | Course 27 | Course 27 | C27 |
      | CAT1 | Course 28 | Course 28 | C28 |
      | CAT1 | Course 29 | Course 29 | C29 |
      | CAT1 | Course 30 | Course 30 | C30 |
      | CAT1 | Course 31 | Course 31 | C31 |
      | CAT1 | Course 32 | Course 32 | C32 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on "Cat 1" "link"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I open the action menu in ".course-listing-actions" "css_element"
    And I click on "Sort by Course ID number ascending" "link" in the ".course-listing-actions" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 20" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 19" before "Course 20"
    And I should not see "Course 21"
    And I should see "Showing courses 1 to 20 of 32 courses"
    And I open the action menu in ".courses-per-page" "css_element"
    And I click on "100" "link" in the ".courses-per-page" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 100" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 19" before "Course 20"
    And I should see course listing "Course 21" before "Course 22"
    And I should see course listing "Course 31" before "Course 32"
    And "#course-listing .pagination" "css_element" should not exist
    And I open the action menu in ".courses-per-page" "css_element"
    And I click on "5" "link" in the ".courses-per-page" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 4" before "Course 5"
    And I should not see "Course 6"
    And I should see "Showing courses 1 to 5 of 32 courses"
    And I should not see "Prev" in the "#course-listing .pagination" "css_element"
    And I should see "Next" in the "#course-listing .pagination" "css_element"
    And I click on "4" "link" in the "#course-listing .pagination" "css_element"
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see "Per page: 5" in the ".course-listing-actions" "css_element"
    And I should not see "Course 15"
    And I should see course listing "Course 16" before "Course 17"
    And I should see course listing "Course 17" before "Course 18"
    And I should see course listing "Course 18" before "Course 19"
    And I should see course listing "Course 19" before "Course 20"
    And I should not see "Course 21"
    And I should see "Showing courses 16 to 20 of 32 courses"

  Scenario: Test clicking to edit a course.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    And I click on "edit" action for "Course 1" in management course listing
    # Redirect
    And I should see "Edit course settings"
    And I should see "Course 1"

  @javascript
  Scenario: Test AJAX expanded categories stay open.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 1-1 | CAT1 | CAT3 |
      | Cat 1-2 | CAT1 | CAT4 |
      | Cat 1-1-1 | CAT3 | CAT5 |
      | Cat 1-1-2 | CAT3 | CAT6 |
      | Cat 2-1 | CAT2 | CAT7 |
      | Cat 2-1-1 | CAT7 | CAT8 |
      | Cat 2-1-1-1 | CAT8 | CAT10 |
      | Cat 2-1-2 | CAT7 | CAT9 |
      | Cat 2-1-2-1 | CAT9 | CAT11 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT2" in the management interface
    And a new page should not have loaded since I started watching
    And I click to expand category "CAT7" in the management interface
    And a new page should not have loaded since I started watching
    And I click to expand category "CAT9" in the management interface
    And a new page should not have loaded since I started watching
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2-1" in the "#course-category-listings ul" "css_element"
    And I click on "Cat 1" category in the management category listing
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2-1" in the "#course-category-listings ul" "css_element"
    And I click on "resortbyidnumber" action for "Cat 1" in management category listing
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 1-2" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat 2-1-1-1" in the "#course-category-listings ul" "css_element"
    And I should see "Cat 2-1-2-1" in the "#course-category-listings ul" "css_element"

  @javascript
  Scenario: Test category expansion after deletion
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat A (1) | 0 | CAT1 |
      | Cat B (2) | 0 | CAT2 |
      | Cat C (1-1) | CAT1 | CAT3 |
      | Cat D (2-1) | CAT2 | CAT4 |
      | Cat E (2-1-1) | CAT4 | CAT5 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat A (1)" in the "#course-category-listings ul" "css_element"
    And I should see "Cat B (2)" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat C (1-1)" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat D (2-1)" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat E (2-1-1)" in the "#course-category-listings ul" "css_element"
    And I click to expand category "CAT1" in the management interface
    And I should see "Cat C (1-1)" in the "#course-category-listings ul" "css_element"
    And a new page should not have loaded since I started watching
    And I click to expand category "CAT2" in the management interface
    And I should see "Cat D (2-1)" in the "#course-category-listings ul" "css_element"
    And a new page should not have loaded since I started watching
    And I click to expand category "CAT4" in the management interface
    And I should see "Cat E (2-1-1)" in the "#course-category-listings ul" "css_element"
    And a new page should not have loaded since I started watching
    And I click on "delete" action for "Cat B (2)" in management category listing
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see "Delete category: Cat B (2)"
    And I should see "Contents of Cat B (2)"
    And I press "Delete"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see "Delete category: Cat B (2)"
    And I should see "Deleted course category Cat B (2)"
    And I press "Continue"
    And a new page should have loaded since I started watching
    And I should see the "Course categories and courses" management page
    And I should see "Cat A (1)" in the "#course-category-listings ul" "css_element"
    And I should not see "Cat B (2)" in the "#course-category-listings ul" "css_element"
