@mod @mod_workshop @javascript @_file_upload
Feature: Teachers can embed images into instructions and conclusion fields
  In order to display images as a part of instructions or conclusions in the workshop
  As a teacher
  I need to be able to embed images into the fields and they should display correctly

  Scenario: Embedding the image into the instructions and conclusions fields
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    # Upload an image into the private files.
    And I follow "Manage private files"
    And I upload "mod/workshop/tests/fixtures/moodlelogo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on homepage
    And I follow "Course 1"
    And I turn editing mode on
    # Create and edit the workshop.
    When I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Workshop with embedded images  |
    And I follow "Workshop with embedded images"
    # Embed the image into Instructions for submission.
    And I navigate to "Edit settings" node in "Workshop administration"
    And I expand all fieldsets
    And I set the field "Instructions for submission" to "<p>Image test</p>"
    And I select the text in the "Instructions for submission" Atto editor
    And I click on "Image" "button" in the "#fitem_id_instructauthorseditor" "css_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "How to submit"
    And I click on "Save image" "button"
    And I press "Save and display"
    # Embed the image into Instructions for assessment.
    And I navigate to "Edit settings" node in "Workshop administration"
    And I expand all fieldsets
    And I set the field "Instructions for assessment" to "<p>Image test</p>"
    And I select the text in the "Instructions for assessment" Atto editor
    And I click on "Image" "button" in the "#fitem_id_instructreviewerseditor" "css_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "How to assess"
    And I click on "Save image" "button"
    And I press "Save and display"
    # Embed the image into Conclusion.
    And I navigate to "Edit settings" node in "Workshop administration"
    And I expand all fieldsets
    And I set the field "Conclusion" to "<p>Image test</p>"
    And I select the text in the "Conclusion" Atto editor
    And I click on "Image" "button" in the "#fitem_id_conclusioneditor" "css_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Well done"
    And I click on "Save image" "button"
    And I press "Save and display"
    # Save the form and check the images are displayed in appropriate phases.
    And I change phase in workshop "Workshop with embedded images" to "Submission phase"
    Then "//*[contains(@class, 'instructions')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/mod_workshop/instructauthors/moodlelogo.png') and @alt='How to submit']" "xpath_element" should exist
    And I change phase in workshop "Workshop with embedded images" to "Assessment phase"
    And "//*[contains(@class, 'instructions')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/mod_workshop/instructreviewers/moodlelogo.png') and @alt='How to assess']" "xpath_element" should exist
    And I change phase in workshop "Workshop with embedded images" to "Closed"
    And "//*[contains(@class, 'conclusion')]//img[contains(@src, 'pluginfile.php') and contains(@src, '/mod_workshop/conclusion/moodlelogo.png') and @alt='Well done']" "xpath_element" should exist
