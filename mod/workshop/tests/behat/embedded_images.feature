@mod @mod_workshop @javascript @_file_upload
Feature: Teachers can embed images into instructions and conclusion fields
  In order to display images as a part of instructions or conclusions in the workshop
  As a teacher
  I need to be able to embed images into the fields and they should display correctly

  # This scenario has Atto-specific steps. See MDL-75913 for further details.
  @editor_atto
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
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "activities" exist:
      | activity | course | name                          |
      | workshop | C1     | Workshop with embedded images |
    And I am on the "Workshop with embedded images" "workshop activity editing" page logged in as admin
    And I set the following fields to these values:
      | Instructions for submission format  | 1 |
      | Instructions for assessment format  | 1 |
      | Conclusion format                   | 1 |
    And I press "Save and display"
    And I log out
    And I log in as "teacher1"
    # Upload an image into the private files.
    And I follow "Manage private files"
    And I upload "mod/workshop/tests/fixtures/moodlelogo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    # Edit the workshop.
    When I am on the "Workshop with embedded images" "workshop activity editing" page
    And I expand all fieldsets
    And I set the field "Instructions for submission" to "<p>Image test</p>"
    And I select the text in the "Instructions for submission" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']/*[descendant::*[@id='id_instructauthorseditor']]" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "How to submit"
    And I click on "Save image" "button"
    And I press "Save and display"
    # Embed the image into Instructions for assessment.
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Instructions for assessment" to "<p>Image test</p>"
    And I select the text in the "Instructions for assessment" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']/*[descendant::*[@id='id_instructreviewerseditor']]" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "How to assess"
    And I click on "Save image" "button"
    And I press "Save and display"
    # Embed the image into Conclusion.
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Conclusion" to "<p>Image test</p>"
    And I select the text in the "Conclusion" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']/*[descendant::*[@id='id_conclusioneditor']]" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
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
