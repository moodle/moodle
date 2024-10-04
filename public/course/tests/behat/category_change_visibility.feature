@core @core_course
Feature: We can change the visibility of categories in the management interface.
  As a moodle admin
  I need to test hiding and showing a category.
  I need to test hiding and showing a sub category.
  I need to test visibility is applied to sub categories.
  I need to test visibility is applied to courses.
  I need to test visibility of children is reset when changing back.

  # Tests hiding and then showing a single category.
  Scenario: Test making a category hidden and then visible again.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be dimmed "CAT1"
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be visible "CAT1"

  # Tests hiding and then showing a single category.
  @javascript
  Scenario: Test using AJAX to make a category hidden and then visible again.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be dimmed "CAT1"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"

  # Tests hiding and then showing a subcategory.
  Scenario: Test making a subcategory hidden and then visible again.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | CAT1 | CAT2 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should not see "Cat 2" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And I click on category "Cat 1" in the management interface
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And I toggle visibility of category "CAT2" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And category in management listing should be dimmed "CAT2"
    And I toggle visibility of category "CAT2" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"

  # Tests hiding and then showing a subcategory.
  @javascript
  Scenario: Test using AJAX to make a subcategory hidden and then visible again.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | CAT1 | CAT2 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should not see "Cat 2" in the "#category-listing ul" "css_element"
    And category in management listing should be visible "CAT1"
    And I click to expand category "CAT1" in the management interface
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And I toggle visibility of category "CAT2" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be dimmed "CAT2"
    And I toggle visibility of category "CAT2" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"

  # The test below this is identical except with JavaScript enabled.
  Scenario: Test relation between category and course when changing visibility.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 3 | CAT1 | CAT3 |
      | Cat 4 | CAT1 | CAT4 |
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
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I should see "Cat 4" in the "#category-listing ul" "css_element"
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And I should see "Course 2" in the "#course-listing ul.course-list" "css_element"
    And I should see "Course 3" in the "#course-listing ul.course-list" "css_element"
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And category in management listing should be visible "CAT4"
    And course in management listing should be visible "C1"
    And course in management listing should be visible "C2"
    And course in management listing should be visible "C3"
    And I toggle visibility of course "C2" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page with a course selected
    And course in management listing should be visible "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be visible "C3"
    And I toggle visibility of category "CAT3" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be dimmed "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be dimmed "CAT3"
    And category in management listing should be dimmed "CAT4"
    And course in management listing should be dimmed "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be dimmed "C3"
    And I toggle visibility of category "CAT1" in management listing
    # Redirect.
    And I should see the "Course categories and courses" management page
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be dimmed "CAT3"
    And category in management listing should be visible "CAT4"
    And course in management listing should be visible "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be visible "C3"

  # The test above this is identical except without JavaScript enabled.
  @javascript @_cross_browser
  Scenario: Test the relation between category and course when changing visibility with AJAX
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | 0 | CAT2 |
      | Cat 3 | CAT1 | CAT3 |
      | Cat 4 | CAT1 | CAT4 |
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
    And I should see "Cat 1" in the "#category-listing ul" "css_element"
    And I should see "Cat 2" in the "#category-listing ul" "css_element"
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And I should see "Cat 4" in the "#category-listing ul" "css_element"
    And I should see "Course 1" in the "#course-listing ul.course-list" "css_element"
    And I should see "Course 2" in the "#course-listing ul.course-list" "css_element"
    And I should see "Course 3" in the "#course-listing ul.course-list" "css_element"
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And category in management listing should be visible "CAT4"
    And course in management listing should be visible "C1"
    And course in management listing should be visible "C2"
    And course in management listing should be visible "C3"
    And I toggle visibility of course "C2" in management listing
    And a new page should not have loaded since I started watching
    And I should see "Cat 3" in the "#category-listing ul" "css_element"
    And course in management listing should be visible "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be visible "C3"
    And I toggle visibility of category "CAT3" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be dimmed "CAT3"
    And category in management listing should be visible "CAT4"
    And course in management listing should be visible "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be visible "C3"
    And I toggle visibility of category "CAT1" in management listing
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And a new page should not have loaded since I started watching
    And category in management listing should be dimmed "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be dimmed "CAT3"
    And category in management listing should be dimmed "CAT4"
    And course in management listing should be dimmed "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be dimmed "C3"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be dimmed "CAT3"
    And category in management listing should be visible "CAT4"
    And course in management listing should be visible "C1"
    And course in management listing should be dimmed "C2"
    And course in management listing should be visible "C3"

  @javascript @_cross_browser
  Scenario: Test courses are hidden when selected category parent is hidden.
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | CAT1 | CAT2 |
      | Cat 3 | CAT2 | CAT3 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT3 | Course 1 | Course 1 | C1 |

    And I log in as "admin"
    And I go to the courses management page
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 2" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 3" in the management interface
    And a new page should have loaded since I started watching
    And I start watching to see if a new page loads
    And I should see the "Course categories and courses" management page
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And course in management listing should be visible "C1"
    And I toggle visibility of category "CAT1" in management listing
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And a new page should not have loaded since I started watching
    And category in management listing should be dimmed "CAT1"
    And category in management listing should be dimmed "CAT2"
    And category in management listing should be dimmed "CAT3"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And course in management listing should be visible "C1"
    And I toggle visibility of course "C1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And a new page should not have loaded since I started watching
    And category in management listing should be dimmed "CAT1"
    And category in management listing should be dimmed "CAT2"
    And category in management listing should be dimmed "CAT3"
    And course in management listing should be dimmed "C1"
    And I toggle visibility of category "CAT1" in management listing
    And a new page should not have loaded since I started watching
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT2"
    And category in management listing should be visible "CAT3"
    And course in management listing should be dimmed "C1"

  @javascript
  Scenario: Test confirm popup when hiding a category
    Given the following "categories" exist:
      | name   | category | idnumber |
      | Cat 1  | 0        | CAT1     |
      | Cat 1b | CAT1     | CAT1B    |
      | Cat 1c | CAT1     | CAT1C    |
      | Cat 2  | 0        | CAT2     |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1B    | Course 1 | Course 1  | C1       |
      | CAT1B    | Course 2 | Course 2  | C2       |
      | CAT1C    | Course 3 | Course 3  | C3       |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT1B"
    And category in management listing should be visible "CAT1C"
    And category in management listing should be visible "CAT2"

    # Category should not be hidden if the dialogue is cancelled.
    And I toggle visibility of category "CAT1C" in management listing
    And I should see "The category Cat 1c contains 1 course" in the "Hide category?" "dialogue"
    And I click on "Cancel" "button" in the "Hide category?" "dialogue"
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT1B"
    And category in management listing should be visible "CAT1C"
    And category in management listing should be visible "CAT2"

    # Hide CAT1 - course count should include courses all subcategories.
    And I toggle visibility of category "CAT1" in management listing
    And I should see "The category Cat 1 contains 3 courses" in the "Hide category?" "dialogue"
    And I click on "Hide" "button" in the "Hide category?" "dialogue"
    And category in management listing should be dimmed "CAT1"
    And category in management listing should be dimmed "CAT1B"
    And category in management listing should be dimmed "CAT1C"
    And category in management listing should be visible "CAT2"

    # Dialogue should not show when showing a category.
    And I toggle visibility of category "CAT1" in management listing
    And "Hide category?" "dialogue" should not exist
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT1B"
    And category in management listing should be visible "CAT1C"
    And category in management listing should be visible "CAT2"

    # Dialogue should not show when hiding a category that contains no courses.
    And I toggle visibility of category "CAT2" in management listing
    And "Hide category?" "dialogue" should not exist
    And category in management listing should be visible "CAT1"
    And category in management listing should be visible "CAT1B"
    And category in management listing should be visible "CAT1C"
    And category in management listing should be dimmed "CAT2"
