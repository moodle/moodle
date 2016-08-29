@core @core_course
Feature: Test we can resort course in the management interface.
  As a moodle admin
  I need to test we can resort courses within a category.
  I need to test we can manually sort courses.

  # Test resorting courses with
  Scenario Outline: Resort courses.
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
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Sort courses" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course full name ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course full name descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course short name ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course short name descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course ID number ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course ID number descending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course time created ascending" in the ".course-listing-actions" "css_element"
    And I should see "Sort by Course time created descending" in the ".course-listing-actions" "css_element"
    And I click on <sortby> "link" in the ".course-listing-actions" "css_element"
    # Redirect.
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
  Scenario Outline: Resort courses with JavaScript enabled.
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
    And I click on category "Cat 1" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Sort courses" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course full name ascending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course full name descending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course short name ascending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course short name descending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course ID number ascending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course ID number descending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course time created ascending" in the ".course-listing-actions" "css_element"
    And I should not see "Sort by Course time created descending" in the ".course-listing-actions" "css_element"
    And I click on "Sort courses" "link"
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

  Scenario: Test moving courses up and down by one.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
      | CAT1 | Course 2 | Course 2 | C2 |
      | CAT1 | Course 3 | Course 3 | C3 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I click on "Sort courses" "link"
    And I click on "Sort by Course ID number ascending" "link" in the ".course-listing-actions" "css_element"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I click to move course "C1" down one
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And I should see course listing "Course 2" before "Course 1"
    And I should see course listing "Course 1" before "Course 3"
    And I click to move course "C3" up one
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 1"

  # Like the above test but with JavaScript enabled.
  @javascript
  Scenario: Test using AJAX to move courses up and down by one.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
      | CAT1 | Course 2 | Course 2 | C2 |
      | CAT1 | Course 3 | Course 3 | C3 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I click on "Sort courses" "link"
    And I click on "Sort by Course ID number ascending" "link" in the ".course-listing-actions" "css_element"
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I click to move course "C1" down one
    And a new page should not have loaded since I started watching
    And I should see course listing "Course 2" before "Course 1"
    And I should see course listing "Course 1" before "Course 3"
    And I click to move course "C3" up one
    And a new page should not have loaded since I started watching
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 1"
