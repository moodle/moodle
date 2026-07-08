@editor @editor_tiny @javascript
Feature: Help modal maintains keyboard operability
  In order to use TinyMCE's Help modal dialogue
  As a user
  The help modal dialogue must be operable with the keyboard

  Scenario: Tab cycles through TinyMCE's Help window controls without escaping the modal behind it
    Given I log in as "admin"
    And I am viewing site calendar
    And I click on "New event" "button"
    And I set the field "Event title" to "Focus test event"
    And I click on "Show more..." "link"
    And I switch to the "Description" TinyMCE editor iframe
    And I click on "body" "css_element"
    And I switch to the main frame
    And I click on the "Help > Help" menu item for the "Description" TinyMCE editor
    # Focus should land inside the Help window's tab list as soon as it opens, and stay there after
    # tabbing through the window's other controls (the shortcuts table, then its buttons), wrapping back
    # around instead of escaping to the "New event" page behind the modal.
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    When I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Keyboard navigation']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Plugins']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Version']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    # The "Event title" field's value should be unaffected by hiding/showing the modal behind the Help window.
    And the field "Event title" matches value "Focus test event"

  Scenario: Closing TinyMCE's Help window returns focus to the editor within the modal
    Given I log in as "admin"
    And I am viewing site calendar
    And I click on "New event" "button"
    And I set the field "Event title" to "Focus test event"
    And I click on "Show more..." "link"
    And I switch to the "Description" TinyMCE editor iframe
    And I click on "body" "css_element"
    And I switch to the main frame
    And I click on the "Help > Help" menu item for the "Description" TinyMCE editor
    When I press the escape key
    Then ".tox-dialog" "css_element" should not exist
    And I switch to the "Description" TinyMCE editor iframe
    And the focused element is "body" "css_element"
    And I switch to the main frame
    # The "Event title" field's value should be unaffected by hiding/showing the modal behind the Help window.
    And the field "Event title" matches value "Focus test event"

  Scenario: Tab cycles through TinyMCE's Help window controls when the editor is not inside a modal
    Given I am on the "Profile advanced editing" page logged in as "admin"
    And I set the field "First name" to "Focustest"
    And I switch to the "Description" TinyMCE editor iframe
    And I click on "body" "css_element"
    And I switch to the main frame
    And I click on the "Help > Help" menu item for the "Description" TinyMCE editor
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    When I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Keyboard navigation']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Plugins']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Version']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the down key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "//div[@role='tab' and normalize-space(.)='Handy shortcuts']" "xpath_element" in the ".tox-dialog" "css_element"
    # The "First name" field's value should be unaffected by opening/closing the Help window.
    And the field "First name" matches value "Focustest"
