@core @verify_age_location
Feature: Test the 'Digital age of consent verification' feature works.
  In order to self-register on the site
  As an user
  I need be to be over the age of digital consent

  Background:
    Given the following config values are set as admin:
      | registerauth | email |
      | agedigitalconsentverification | 1 |

  Scenario: User that is not considered a digital minor attempts to self-register on the site.
    # Try to access the sign up page.
    Given I am on homepage
    When I click on "Log in" "link" in the ".logininfo" "css_element"
    And I click on "Create new account" "link"
    Then I should see "Age and location verification"
    When I set the field "What is your age?" to "16"
    And I set the field "In which country do you live?" to "DZ"
    And I press "Proceed"
    Then I should see "New account"
    And I should see "Username"
    # Try to access the sign up page again.
    When I press "Cancel"
    And I click on "Create new account" "link"
    Then I should see "New account"
    And I should see "Username"

  Scenario: User that is considered a digital minor attempts to self-register on the site.
    # Try to access the sign up page.
    Given I am on homepage
    When I click on "Log in" "link" in the ".logininfo" "css_element"
    And I click on "Create new account" "link"
    Then I should see "Age and location verification"
    When I set the field "What is your age?" to "12"
    And I set the field "In which country do you live?" to "AT"
    And I press "Proceed"
    Then I should see "You are too young to create an account on this site."
    And I should see "Please ask your parent/guardian to contact:"
    # Try to access the sign up page again.
    When I click on "Back to the site home" "link"
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I click on "Create new account" "link"
    Then I should see "You are too young to create an account on this site."
    And I should see "Please ask your parent/guardian to contact:"
