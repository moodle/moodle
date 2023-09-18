# This feature has Atto-specific steps. See MDL-75913 for further details.
@core @core_course @core_customfield @javascript
Feature: Teachers can edit course custom fields
  In order to have additional data on the course
  As a teacher
  I need to edit data for custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And the following "custom fields" exist:
      | name    | category          | type     | shortname | description | configdata            |
      | Field 1 | Category for test | text     | f1        | d1          |                       |
      | Field 2 | Category for test | textarea | f2        | d2          |                       |
      | Field 3 | Category for test | checkbox | f3        | d3          |                       |
      | Field 4 | Category for test | date     | f4        | d4          |                       |
      | Field 5 | Category for test | select   | f5        | d5          | {"options":"a\nb\nc"} |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "user private files" exist:
      | user  | filepath                       | filename    |
      | admin | lib/tests/fixtures/gd-logo.png | gd-logo.png |

  Scenario: Display custom fields on course edit form
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then I should see "Category for test"
    And I should see "Field 1"
    And I should see "Field 2"
    And I should see "Field 3"
    And I should see "Field 4"
    And I should see "Field 5"
    And I log out

  Scenario: Create a course with custom fields from the management interface
    When I log in as "admin"
    And I go to the courses management page
    And I should see the "Categories" management page
    And I click on category "Category 1" in the management interface
    And I should see the "Course categories and courses" management page
    And I click on "Create new course" "link" in the "#course-listing" "css_element"
    And I set the following fields to these values:
      | Course full name      | Course 2     |
      | Course short name     | C2           |
      | Field 1               | testcontent1 |
      | Field 2               | testcontent2 |
      | Field 3               | 1            |
      | customfield_f4[enabled] | 1          |
      | customfield_f4[day]   | 1            |
      | customfield_f4[month] | January      |
      | customfield_f4[year]  | 2019         |
      | Field 5               | b            |
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    And the following fields match these values:
      | Course full name      | Course 2     |
      | Course short name     | C2           |
      | Field 1               | testcontent1 |
      | Field 2               | testcontent2 |
      | Field 3               | 1            |
      | customfield_f4[day]   | 1            |
      | customfield_f4[month] | January      |
      | customfield_f4[year]  | 2019         |
      | Field 5               | b            |
    And I log out

  @javascript @editor_tiny
  Scenario: Use images in the custom field description
    When I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Edit" "link" in the "Field 1" "table_row"
    And I click on "Image" "button" in the "Description" "form_row"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "gd-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Example"
    And I click on "Save image" "button"
    And I click on "Save changes" "button" in the "Updating Field 1" "dialogue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the image at "//div[contains(@class, 'fitem')][contains(., 'Field 1')]/following-sibling::div[1]//img[contains(@src, 'pluginfile.php') and contains(@src, '/core_customfield/description/') and @alt='Example']" "xpath_element" should be identical to "lib/tests/fixtures/gd-logo.png"
    And I log out

  @javascript
  Scenario: Custom field short name must be present and unique
    When I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then I should see "You must supply a value here" in the "Short name" "form_row"
    And I set the field "Short name" to "short name"
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then I should see "The short name can only contain alphanumeric lowercase characters and underscores (_)." in the "Short name" "form_row"
    And I set the field "Short name" to "f1"
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then I should see "Short name already exists" in the "Short name" "form_row"
    And I click on "Cancel" "button" in the "Adding a new Short text" "dialogue"
    And I log out
