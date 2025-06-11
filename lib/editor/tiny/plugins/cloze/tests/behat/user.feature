@editor @tiny @editor_tiny @tiny_cloze @javascript
Feature: Check that the user edit page appears and no exception is thrown.

  Background:
    Given the following "courses" exist:
      | fullname        | shortname   |
      | Course fullname | C_shortname |
    And the following "user preferences" exist:
      | user  | preference | value |
      | admin | htmleditor | tiny  |
    And I log in as "admin"

  @javascript
  Scenario: Open the page to add a user
    When I navigate to "Users > Add a new user" in site administration
    Then I should see "First name"
    And I click on the "Image" button for the "Description" TinyMCE editor
