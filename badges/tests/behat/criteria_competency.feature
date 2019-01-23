@core @core_badges @_file_upload
Feature: Award badges based on competency completion
  In order to award badges to users based on competency completion
  As an admin
  I need to add competency completion criteria to badges in the system

  Background: Setup the competency framework and the course
    Given the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | student |
    And the following lp "frameworks" exist:
      | shortname | idnumber |
      | Framework 1 | sc-y-2 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | comp1 | sc-y-2 |
    And I log in as "admin"

  @javascript
  Scenario: Award badge for completing a competency in a course
    # Add a competency to the course
    When I am on "Course 1" course homepage
    And I follow "Competencies"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I click on "Edit" "link" in the "[data-region='configurecoursecompetencies']" "css_element"
    And I wait until the page is ready
    And I click on "Rating a competency only updates the competency in this course" "text"
    And I click on "Save changes" "button" in the "Configure course competencies" "dialogue"
    # Add a badge to the course
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
      | issuername | Tester of course badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    # Set the competency as a criteria for the badge
    And I set the field "type" to "Competencies"
    When I open the autocomplete suggestions list
    And I click on "ul[class='form-autocomplete-suggestions'] li" "css_element"
    And I wait until the page is ready
    And I press "Save"
    And I wait until the page is ready
    # Enable the badge
    And I press "Enable access"
    And I press "Continue"
    # Rate the competency in the course
    And I am on "Course 1" course homepage
    And I follow "Competencies"
    And I click on "comp1" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I log out
    # See if we got the badge
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Course Badge"

  @javascript
  Scenario: Award badge for completing a competency in the site
    # Add a competency to the course
    When I am on "Course 1" course homepage
    And I follow "Competencies"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    # Add a badge to the site
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    # Set the competency as a criteria for the badge
    And I set the field "type" to "Competencies"
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I press "Save"
    # Enable the badge
    And I wait until the page is ready
    And I press "Enable access"
    And I press "Continue"
    # Rate the competency in the course
    And I am on "Course 1" course homepage
    And I follow "Competencies"
    And I click on "comp1" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I wait until the page is ready
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I log out
    # See if we got the badge
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
