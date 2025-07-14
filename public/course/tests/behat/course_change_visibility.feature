@core @core_course
Feature: We can change the visibility of courses in the management interface.
  As a moodle admin
  I need to test hiding and then showing a course.
  I need to test hiding a course and then hiding and showing the category its within.

  # Test hiding and showing a course.
  Scenario: Test toggling course visibility through the management interfaces.
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
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And category in management listing should be visible "CAT1"
    And course in management listing should be visible "C1"
    And I toggle visibility of course "C1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of course "C1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And category in management listing should be visible "CAT1"
    And course in management listing should be visible "C1"
    And I toggle visibility of course "C1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be dimmed "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing

  # Test hiding and showing a course with JS, same as the above test.
  @javascript
  Scenario: Test using AJAX to hide a course through the management interfaces.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And category in management listing should be visible "CAT1"
    And course in management listing should be visible "C1"
    And I toggle visibility of course "C1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of course "C1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And course in management listing should be visible "C1"
    And I toggle visibility of course "C1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And a new page should not have loaded since I started watching
    And category in management listing should be dimmed "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And I toggle visibility of course "C1" in management listing
    And I select "Courses" from the "Viewing" singleselect
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And I toggle visibility of course "C1" in management listing
    And a new page should not have loaded since I started watching
    And course in management listing should be dimmed "C1"
    And I toggle visibility of course "C1" in management listing
    And course in management listing should be visible "C1"
    And a new page should not have loaded since I started watching
