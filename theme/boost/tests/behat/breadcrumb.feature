@javascript @theme_boost
Feature: Breadcrumbs navigation
  To navigate in boost theme
  As an admin user
  I should see breadcrumbs

  Scenario: Admin user navigates to site administrations plugins assignment settings
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Assignment > Assignment settings" in site administration
    Then I should see "Activity modules" in the ".breadcrumb" "css_element"
    And I should see "Assignment" in the ".breadcrumb" "css_element"
    And I should see "Assignment settings" in the ".breadcrumb" "css_element"

  Scenario: Admin user navigates to site adminsitrations plugins assignment feedback offline grading worksheet
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Assignment > Feedback plugins > Offline grading worksheet" in site administration
    Then I should see "Activity modules" in the ".breadcrumb" "css_element"
    And I should see "Assignment" in the ".breadcrumb" "css_element"
    And I should see "Feedback plugins" in the ".breadcrumb" "css_element"
    And I should see "Offline grading worksheet" in the ".breadcrumb" "css_element"

  Scenario: Admin user navigates to site adminsitrations plugins badges manage backpacks page
    Given I log in as "admin"
    When I navigate to "Badges > Manage backpacks" in site administration
    Then I should see "Badges" in the ".breadcrumb" "css_element"
    And I should see "Manage backpacks" in the ".breadcrumb" "css_element"

  Scenario: Admin user navigates to site adminsitrations plugins caching memcached page
    Given I log in as "admin"
    When I navigate to "Plugins > Caching > Cache stores > Memcached" in site administration
    Then I should see "Caching" in the ".breadcrumb" "css_element"
    Then I should see "Cache stores" in the ".breadcrumb" "css_element"
    And I should see "Memcached" in the ".breadcrumb" "css_element"
