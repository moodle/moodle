@core
Feature: Focus lock in modal popups
  In order to navigate a modal popup with keyboard
  As a user
  The tab key should cycle through elements in the form and not go outside it

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | name       | course | idnumber |
      | forum    | Test forum | C1     | forum1   |
    And I am on the "forum1" "Activity" page logged in as "admin"
    And I follow "Add discussion topic"
    And I click on "Image" "button"

  @javascript
  Scenario: Tab cycles through elements in modal, using image popup in Tiny as an example
    # Repeated tabs just to get to the last element. This may need changing if controls are added
    # or removed to the form.
    When I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "Browse repositories" "button"

    # Tab past last element should go back to the first one, which is the modal itself, then the close button.
    And I press the tab key
    And I press the tab key
    Then the focused element is "Close" "button" in the "Insert image" "dialogue"

    And I press the shift tab key
    And I press the shift tab key
    And the focused element is "Browse repositories" "button"

  @javascript
  Scenario: Focus continues to be locked to modal even after closing nested modal
    # Open 'Browse repositories' nested modal, then close it again.
    When I press "Browse repositories"
    And I click on "Close" "button" in the "File picker" "dialogue"
    And the focused element is "Browse repositories" "button"

    # Focus should still wrap around to the start of the image modal.
    When I press the tab key
    And I press the tab key
    Then the focused element is "Close" "button" in the "Insert image" "dialogue"
