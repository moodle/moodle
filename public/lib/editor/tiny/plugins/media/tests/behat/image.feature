@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload an image
  In order to work with images
  As a user
  I need to be able to upload and manipulate images

  Scenario: Clicking on the Image button in the TinyMCE editor opens the image dialog
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    Then "Insert image" "dialogue" should exist

  Scenario: Browsing repositories in the TinyMCE editor opens the image dialog and shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    Then "File picker" "dialogue" should exist

  Scenario: Focus returns to the correct location after closing a nested FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I press "Browse repositories"
    When I press the escape key
    Then the focused element is "Browse repositories" "button"

  @_file_upload @test_tiny
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker and upload url image
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/tinyscreenshot.png" to the file picker for TinyMCE
    # Note: This needs to be replaced with a label.
    Then ".tiny_image_preview" "css_element" should be visible

  @_file_upload
  Scenario: Insert image to the TinyMCE editor
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I set the field "How would you describe this image to someone who can't see it?" to "It's the Moodle"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "How would you describe this image to someone who can't see it?" matches value "It's the Moodle"
    # Note: This needs to be replaced with a label.
    And ".tiny_image_preview" "css_element" should be visible

  @_file_upload
  Scenario: Resizing the image uses the original and custom sizes and the keep proportion checkbox
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I click on "This image is decorative only" "checkbox"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "Original size" matches value "1"
    And I click on "Custom size" "radio"
    Then the field "Keep proportion" matches value "1"
    And I click on "Keep proportion" "checkbox"
    And I set the field "Width" to "102"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "Custom size" matches value "1"
    And the field "Width" matches value "102"
    And the field "Keep proportion" matches value "0"

  @_file_upload
  Scenario: Set the alt text to the maximum and below the maximum length
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/tinyscreenshot.png" to the file picker for TinyMCE
    When I set the field "How would you describe this image to someone who can't see it?" to "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet."
    Then I should see "750" in the "#currentcount" "css_element"
    And I set the field "How would you describe this image to someone who can't see it?" to "Lorem ipsum dolor sit amet."
    And I should see "27" in the "#currentcount" "css_element"

  @javascript
  Scenario: Permissions can be configured to control access to media
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "roles" exist:
      | name           | shortname | description         | archetype      |
      | Custom teacher | custom1   | Limited permissions | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | custom1        |
    And the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Test assignment |
    And the following "permission overrides" exist:
      | capability     | permission | role    | contextlevel | reference |
      | tiny/media:use | Prohibit   | custom1 | Course       | C1        |
    # Check plugin access as a role with prohibited permissions.
    And I log in as "teacher2"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    When I click on the "Insert" menu item for the "Activity instructions" TinyMCE editor
    Then I should not see "Multimedia"
    # Check plugin access as a role with allowed permissions.
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I click on the "Insert" menu item for the "Activity instructions" TinyMCE editor
    And I should see "Multimedia"
