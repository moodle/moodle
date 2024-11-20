@core @core_user
Feature: Contact site support method and availability can be customised
  In order to effectively support people using my Moodle site
  As an admin
  I need to be able to configure the site support method and who has access to it

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |

  Scenario: Contact site support can be made available to all site visitors
    Given the following config values are set as admin:
      | supportavailability | 2 |
    # Confirm unauthenticated visitor has access to the contact form.
    When I am on site homepage
    Then I should see "Contact site support" in the "page-footer" "region"
    And I click on "Contact site support" "link" in the "page-footer" "region"
    And I should see "Contact site support" in the "page-header" "region"
    # Confirm someone logged in as guest has access to the contact form.
    And I log in as "guest"
    And I should see "Contact site support" in the "page-footer" "region"
    And I click on "Contact site support" "link" in the "page-footer" "region"
    And I should see "Contact site support" in the "page-header" "region"
    And I log out
    # Confirm logged in user has access to the contact form.
    And I log in as "user1"
    And I should see "Contact site support" in the "page-footer" "region"
    And I click on "Contact site support" "link" in the "page-footer" "region"
    And I should see "Contact site support" in the "page-header" "region"

  Scenario: Contact site support can be limited to authenticated users
    Given the following config values are set as admin:
      | supportavailability | 1 |
    # Confirm unauthenticated visitor cannot see the option or directly access the page.
    When I am on site homepage
    Then I should not see "Contact site support" in the "page-footer" "region"
    And I am on the "user > Contact Site Support" page
    And I should not see "Contact site support" in the "page-header" "region"
    # Confirm someone logged in as guest cannot see the option or directly access the page.
    And I log in as "guest"
    And I should not see "Contact site support" in the "page-footer" "region"
    And I am on the "user > Contact Site Support" page
    And I should not see "Contact site support" in the "page-header" "region"
    And I log out
    # Confirm logged in user has access to the contact form.
    And I log in as "user1"
    And I should see "Contact site support" in the "page-footer" "region"
    And I click on "Contact site support" "link" in the "page-footer" "region"
    And I should see "Contact site support" in the "page-header" "region"

  Scenario: Contact site support can be disabled
    Given the following config values are set as admin:
      | supportavailability | 0    |
      | defaulthomepage     | 0    |
    # Confirm unauthenticated visitor cannot see the option.
    When I am on site homepage
    Then I should not see "Contact site support" in the "page-footer" "region"
    # Confirm someone logged in as guest cannot see the option.
    And I log in as "guest"
    And I should not see "Contact site support" in the "page-footer" "region"
    And I log out
    # Confirm logged in user cannot see the option.
    And I log in as "user1"
    And I should not see "Contact site support" in the "page-footer" "region"
    And I log out
    # Confirm admin cannot see the option.
    And I log in as "admin"
    And I should not see "Contact site support" in the "page-footer" "region"
    # Confirm visiting the contact form directly without permission redirects to the homepage.
    And I am on the "user > Contact Site Support" page
    And I should see "Acceptance test site" in the "page-header" "region"
    And I should not see "Contact site support" in the "page-header" "region"

  @javascript
  Scenario: Contact site support link opens a custom support page URL if set
    Given the following config values are set as admin:
      | supportavailability | 1                |
      | supportpage         | user/profile.php |
    When I log in as "user1"
    And I am on site homepage
    And I click on "Contact site support" "link" in the "page-footer" "region"
    And I switch to a second window
    Then I should see "User One" in the "page-header" "region"
    And I should not see "Contact site support" in the "page-header" "region"
    And I close all opened windows

  Scenario: Visiting the contact site support page directly will redirect to the custom support page if set
    Given the following config values are set as admin:
      | supportavailability | 2           |
      | supportpage         | profile.php |
    When I log in as "user1"
    And I am on the "user > Contact Site Support" page
    Then I should see "User One" in the "page-header" "region"
    And I should not see "Contact site support" in the "page-header" "region"

  Scenario: Visiting the contact site support page still redirects to homepage if access to support is disabled
    Given the following config values are set as admin:
      | supportavailability | 0           |
      | supportpage         | profile.php |
      | defaulthomepage     | 0           |
    When I log in as "user1"
    And I am on the "user > Contact Site Support" page
    Then I should see "Acceptance test site" in the "page-header" "region"
    And I should not see "Contact site support" in the "page-header" "region"
