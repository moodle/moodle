@core @core_contentbank @core_h5p @contentbank_h5p @javascript
Feature: Store the content bank view preference
  In order to consistantly view the content bank in icons or details view
  As an admin
  I need to be able to store my view preference

  Background:
    Given the following "user private files" exist:
      | user  | filepath                             |
      | admin | h5p/tests/fixtures/filltheblanks.h5p |
      | admin | h5p/tests/fixtures/greeting-card.h5p |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I set the field "Save as" to "fib.h5p"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I click on "Content bank" "link"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "greeting-card.h5p" "link"
    And I set the field "Save as" to "greetingcard.h5p"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"

  Scenario: There are several views for displaying contents into the content bank
    Given I am on site homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    When I click on "Display content bank with file details" "button"
    Then I should see "Last modified"
    And I follow "greetingcard.h5p"
    And I click on "Content bank" "link"
    And I should see "Last modified"
    And I click on "Display content bank with icons" "button"
    And I follow "greetingcard.h5p"
    And I click on "Content bank" "link"
    And I should not see "Last modified"

  Scenario: Display the number of times a content is used in file details view
    Given I am on the "My private files" page
    And I click on "Add..." "button"
    And I select "Content bank" repository in file picker
    And I click on "fib.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    When I am on site homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "Display content bank with file details" "button"
    Then I should see "1" in the "[data-file='fib.h5p'] .cb-uses" "css_element"
    And I should see "0" in the "[data-file='greetingcard.h5p'] .cb-uses" "css_element"
