@core @core_badges
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
    And the following "core_competency > frameworks" exist:
      | shortname   | idnumber |
      | Framework 1 | sc-y-2   |
    And the following "core_competency > competencies" exist:
      | shortname | competencyframework |
      | comp1     | sc-y-2              |
      | comp2     | sc-y-2              |
    And I log in as "admin"

  @javascript
  Scenario: Award badge for completing a competency in a course
    Given the following "core_badges > Badge" exists:
      | name        | Course Badge                 |
      | status      | 0                            |
      | type        | 2                            |
      | course      | C1                           |
      | description | Course badge description     |
      | image       | badges/tests/behat/badge.png |
    # Add a competency to the course
    When I am on "Course 1" course homepage
    And I navigate to "Competencies > Add competencies to course" in current page administration
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
    And I change window size to "large"
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I select "Criteria" from the "jump" singleselect
    # Set the competency as a criteria for the badge
    And I set the field "type" to "Competencies"
    When I open the autocomplete suggestions list
    And I click on "ul[class='form-autocomplete-suggestions'] li" "css_element"
    And I wait until the page is ready
    And I press "Save"
    And I wait until the page is ready
    # Enable the badge
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Rate the competency in the course
    And I am on "Course 1" course homepage
    And I navigate to "Competencies" in current page administration
    And I click on "comp1" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I should see "The competency rating was manually set in the course"
    And I log out
    # See if we got the badge
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Course Badge"

  @javascript
  Scenario: Award badge for completing a competency in the site
    Given the following "core_badges > Badge" exists:
      | name        | Site Badge                   |
      | status      | 0                            |
      | description | Site badge description       |
      | image       | badges/tests/behat/badge.png |
    # Add a competency to the course
    When I am on "Course 1" course homepage
    And I navigate to "Competencies > Add competencies to course" in current page administration
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    # Add a badge to the site
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Site Badge" report row
    And I select "Criteria" from the "jump" singleselect
    # Set the competency as a criteria for the badge
    And I set the field "type" to "Competencies"
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I press "Save"
    # Enable the badge
    And I wait until the page is ready
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Rate the competency in the course
    And I am on "Course 1" course homepage
    And I navigate to "Competencies" in current page administration
    And I click on "comp1" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I should see "The competency rating was manually set in the course"
    And I log out
    # See if we got the badge
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"

  @javascript
  Scenario: Award badge for completing all competencies in the site
    Given the following "core_badges > Badge" exists:
      | name        | Site Badge                   |
      | status      | 0                            |
      | description | Site badge description       |
      | image       | badges/tests/behat/badge.png |
    # Add a competency to the course
    When I am on "Course 1" course homepage
    And I navigate to "Competencies > Add competencies to course" in current page administration
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    # Add a badge to the site
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Site Badge" report row
    And I select "Criteria" from the "jump" singleselect
    # Set the competency as a criteria for the badge
    And I set the field "type" to "Competencies"
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I wait until the page is ready
    And I click on "This criterion is complete when" "link"
    And I click on "All of the selected competencies have been completed" "radio"
    And I press "Save"
    # Enable the badge
    And I wait until the page is ready
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Rate the competency in the course
    And I am on "Course 1" course homepage
    And I navigate to "Competencies" in current page administration
    And I click on "comp1" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I should see "The competency rating was manually set in the course"
    And I log out
    # We should not get the badge yet.
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"
    And I log out
    # Rate the other competency.
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Competencies" in current page administration
    And I click on "comp2" "link" in the "[data-region='coursecompetencies']" "css_element"
    And I press "Rate"
    And I set the following fields to these values:
      | Rating | C |
    And I click on "Rate" "button" in the "Rate" "dialogue"
    And I should see "The competency rating was manually set in the course"
    And I log out
    # See if we got the badge now.
    Then I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
