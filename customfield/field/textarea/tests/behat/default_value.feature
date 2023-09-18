@customfield @customfield_textarea @javascript @editor_tiny
Feature: Default value for the textarea custom field can contain images
  In order to see images on custom fields
  As a manager
  I need to be able to add images to the default value

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher  | Teacher   | 1        | teacher1@example.com |
      | manager  | Manager   | 1        | manager1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "system role assigns" exist:
      | user    | course               | role    |
      | manager | Acceptance test site | manager |
    And the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "user private files" exist:
      | user  | filepath                       | filename    |
      | admin | lib/tests/fixtures/gd-logo.png | gd-logo.png |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Text area" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    # Embed the image into Default value.
    And I click on "Image" "button" in the "Default value" "form_row"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "gd-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Example"
    And I click on "Save image" "button"
    And I click on "Save changes" "button" in the "Adding a new Text area" "dialogue"
    And I log out

  Scenario: For the courses that existed before the custom field was created the default value is displayed
    When I am on site homepage
    Then the image at "//*[contains(@class, 'frontpage-course-list-all')]//*[contains(@class, 'customfield_textarea')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/customfield_textarea/defaultvalue/') and @alt='Example']" "xpath_element" should be identical to "lib/tests/fixtures/gd-logo.png"

  Scenario: Teacher will see textarea default value when editing a course created before custom field was created
     # Teacher will see the image when editing existing course.
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I switch to the "Test field" TinyMCE editor iframe
    Then "//img[contains(@src, 'draftfile.php') and contains(@src, '/gd-logo.png') and @alt='Example']" "xpath_element" should exist
    And I switch to the main frame
    # Save the course without changing the default value.
    And I press "Save and display"
    And I log out
    # Now the same image is displayed as "value" and not as "defaultvalue".
    And I am on site homepage
    Then "//img[contains(@src, '/customfield_textarea/defaultvalue/')]" "xpath_element" should not exist
    And the image at "//*[contains(@class, 'frontpage-course-list-all')]//*[contains(@class, 'customfield_textarea')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/customfield_textarea/value/') and @alt='Example']" "xpath_element" should be identical to "lib/tests/fixtures/gd-logo.png"

  Scenario: Manager can create a course and the default value for textarea custom field will apply.
    When I log in as "manager"
    And I go to the courses management page
    And I click on "Create new course" "link" in the "#course-listing" "css_element"
    And I set the following fields to these values:
      | Course full name      | Course 2     |
      | Course short name     | C2           |
    And I expand all fieldsets
    And I switch to the "Test field" TinyMCE editor iframe
    Then "//img[contains(@src, 'draftfile.php') and contains(@src, '/gd-logo.png') and @alt='Example']" "xpath_element" should exist
    And I switch to the main frame
    And I press "Save and display"
    And I log out
    # Now the same image is displayed as "value" and not as "defaultvalue".
    And I am on site homepage
    Then the image at "//*[contains(@class, 'frontpage-course-list-all')]//*[contains(@class, 'customfield_textarea')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/customfield_textarea/value/') and @alt='Example']" "xpath_element" should be identical to "lib/tests/fixtures/gd-logo.png"
