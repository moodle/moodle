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
      | category | fullname | shortname | idnumber | sortorder |
      | CAT1 | Social studies | Senior school | Ext003 | 1 |
      | CAT1 | Applied sciences  | Middle school | Sci001 | 2 |
      | CAT1 | Extended social studies  | Junior school | Ext002 | 3 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Sort courses" in the ".course-listing-actions" "css_element"
    And I should see "By fullname" in the ".course-listing-actions" "css_element"
    And I should see "By shortname" in the ".course-listing-actions" "css_element"
    And I should see "By idnumber" in the ".course-listing-actions" "css_element"
    And I click on <sortby> "link" in the ".course-listing-actions" "css_element"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see course listing <course1> before <course2>
    And I should see course listing <course2> before <course3>

  Examples:
    | sortby | course1 | course2 | course3 |
    | "By fullname"        | "Applied sciences"        | "Extended social studies" | "Social studies" |
    | "By shortname"       | "Extended social studies" | "Applied sciences"        | "Social studies" |
    | "By idnumber"        | "Extended social studies" | "Social studies"          | "Applied sciences" |

  @javascript
  Scenario Outline: Resort courses with JavaScript enabled.
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber | sortorder |
      | CAT1 | Social studies | Senior school | Ext003 | 1 |
      | CAT1 | Applied sciences  | Middle school | Sci001 | 2 |
      | CAT1 | Extended social studies  | Junior school | Ext002 | 3 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Sort courses" in the ".course-listing-actions" "css_element"
    And I should not see "By fullname" in the ".course-listing-actions" "css_element"
    And I should not see "By shortname" in the ".course-listing-actions" "css_element"
    And I should not see "By idnumber" in the ".course-listing-actions" "css_element"
    And I click on "Sort courses" "link"
    And I should see "By fullname" in the ".course-listing-actions" "css_element"
    And I should see "By shortname" in the ".course-listing-actions" "css_element"
    And I should see "By idnumber" in the ".course-listing-actions" "css_element"
    And I click on <sortby> "link" in the ".course-listing-actions" "css_element"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see course listing <course1> before <course2>
    And I should see course listing <course2> before <course3>

  Examples:
    | sortby | course1 | course2 | course3 |
    | "By fullname"        | "Applied sciences"        | "Extended social studies" | "Social studies" |
    | "By shortname"       | "Extended social studies" | "Applied sciences"        | "Social studies" |
    | "By idnumber"        | "Extended social studies" | "Social studies"          | "Applied sciences" |

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
    And I should see the "Course categories" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I click on "Sort courses" "link"
    And I click on "By idnumber" "link" in the ".course-listing-actions" "css_element"
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
    And I should see the "Course categories" management page
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Course categories" in the "#category-listing h3" "css_element"
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I click on "Sort courses" "link"
    And I click on "By idnumber" "link" in the ".course-listing-actions" "css_element"
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see course listing "Course 1" before "Course 2"
    And I should see course listing "Course 2" before "Course 3"
    And I click to move course "C1" down one
    # AJAX, no redirect.
    And I should see course listing "Course 2" before "Course 1"
    And I should see course listing "Course 1" before "Course 3"
    And I click to move course "C3" up one
    # AJAX, no redirect.
    And I should see course listing "Course 2" before "Course 3"
    And I should see course listing "Course 3" before "Course 1"