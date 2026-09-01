@core
Feature: The secondary navigation is exposed as a named landmark
  In order to jump straight to the secondary navigation
  As a user of assistive technology
  I need it to be a navigation landmark whose name says which context it belongs to

  Background:
    Given the following "categories" exist:
      | name       | category | idnumber |
      | Category 1 | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And the following "activities" exist:
      | activity | name       | course | idnumber |
      | forum    | Test forum | C1     | forum1   |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: The course secondary navigation is a landmark named for the course context
    Given I am on the "Course 1" course page logged in as "teacher1"
    Then "Course menu" "navigation" should exist
    And "Participants" "link" should exist in the "Course menu" "navigation"

  @javascript
  Scenario: The category secondary navigation is a landmark named for the category context
    Given I am on the "CAT1" "category" page logged in as "admin"
    Then "Category menu" "navigation" should exist
    And "Category" "link" should exist in the "Category menu" "navigation"

  @javascript
  Scenario: The site administration secondary navigation is a landmark named for the admin context
    Given I am on the "Admin notifications" page logged in as "admin"
    Then "Site administration menu" "navigation" should exist
    And "General" "link" should exist in the "Site administration menu" "navigation"

  @javascript @accessibility
  Scenario: Each navigation landmark on the activity page can be told apart by its name
    # The navbar and the secondary navigation are both navigation landmarks, so each needs its
    # own name. That is axe's landmark-unique rule, and as it compares every landmark on the page
    # it has to run over the whole page rather than over the landmark alone.
    # cat.semantics is the narrowest tag which carries landmark-unique. best-practice carries it
    # too, but also pulls in region (tagged cat.keyboard), which the activity page fails for
    # reasons unrelated to this landmark - see MDL-86380.
    Given I am on the "Test forum" "forum activity" page logged in as "teacher1"
    Then "Site navigation" "navigation" should exist
    And "Activity menu" "navigation" should exist
    And "Settings" "link" should exist in the "Activity menu" "navigation"
    And the page should meet "cat.semantics" accessibility standards

  Scenario: The secondary navigation landmark is present without JavaScript
    # Without JavaScript the Nav component never mounts and the server-rendered fallback is the
    # only markup on the page, so the fallback has to render the same landmark the component does.
    Given I am on the "Course 1" course page logged in as "teacher1"
    Then "Course menu" "navigation" should exist
    And "Participants" "link" should exist in the "Course menu" "navigation"
    # The fallback markup must not nest a second navigation landmark inside the named one.
    # Note this has to be an xpath: a scoped css_element search is descendant-or-self, so "nav"
    # would match the landmark we are searching inside and always report a nested landmark.
    And ".//nav" "xpath_element" should not exist in the "Course menu" "navigation"
