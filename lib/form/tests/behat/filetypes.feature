@core_form
Feature: There is a form element allowing to select filetypes
  In order to test the filetypes field
  As an admin
  I need a test form that makes use of the filetypes field

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                              | course | idnumber |
      | label      | L1   | <a href="../lib/form/tests/fixtures/filetypes.php">FixtureLink</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "FixtureLink"

  Scenario: File types can be provided via direct input with JavaScript disabled
    Given I set the field "Choose from all file types" to ".png .gif .jpg"
    When I press "Save changes"
    Then the field "Choose from all file types" matches value ".png .gif .jpg"

  @javascript
  Scenario: File types can be provided via direct input with JavaScript enabled
    Given I set the field "Choose from all file types" to ".png .gif .jpg"
    When I press "Save changes"
    Then the field "Choose from all file types" matches value ".png .gif .jpg"

  Scenario: File types are validated to be known, unless the field allows unknown be provided
    Given I set the field "Choose from all file types" to ".pdf .doesnoexist"
    And I set the field "Choose from a limited set" to "doc docx pdf rtf"
    And I set the field "Unknown file types are allowed here" to ".neverminditdoesnotexist"
    When I press "Save changes"
    Then I should see "Unknown file types: .doesnoexist"
    And I should see "These file types are not allowed here: .doc, .docx, .rtf"
    And I should see "It is not allowed to select 'All file types' here"
    And I should not see "Unknown file types: .neverminditdoesnotexist"

  @javascript @_file_upload
  Scenario: File manager element implicitly validates submitted files
    # We can't directly upload the invalid file here as the upload repository would throw an exception.
    # So instead we try to trick the filemanager, to be finally stopped by the implicit validation.
    And I upload "lib/tests/fixtures/empty.txt" file to "Picky file manager" filemanager
    And I follow "empty.txt"
    And I set the field "Name" to "renamed.exe"
    And I press "Update"
    When I press "Save changes"
    Then I should see "Some files (renamed.exe) cannot be uploaded. Only file types .txt are allowed."
