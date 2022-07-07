@core @core_contentbank @contenttype_h5p @_file_upload @_switch_iframe @javascript
Feature: Replace H5P file from an existing content
  In order to replace an H5P content from the content bank
  As an admin
  I need to be able to replace the content with a new .h5p file

  Background:
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname       | filepath                              |
      | System       |           | contenttype_h5p | admin | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
    And I log in as "admin"
    And I press "Customise this page"
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"

  Scenario: Admins can replace the original .h5p file with a new one
    Given I click on "filltheblanks.h5p" "link"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Of which countries"
    And I switch to the main frame
    When I open the action menu in "region-main-settings-menu" "region"
    And I choose "Replace with file" in the open action menu
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Upload content" filemanager
    And I click on "Save changes" "button"
    Then I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Lorum ipsum"
    And I switch to the main frame
