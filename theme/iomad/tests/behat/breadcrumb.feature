@javascript @theme_iomad
Feature: Breadcrumbs navigation
  To navigate in iomad theme
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

  Scenario: Admin user changes the default home page and navigates to 'course category management' page
    Given the following config values are set as admin:
      | defaulthomepage | 3 |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    When I follow "Cat 1"
    Then I should not see "My courses" in the ".breadcrumb" "css_element"
    And I should see "Cat 1" in the ".breadcrumb" "css_element"
    And I should see "Manage courses and categories" in the ".breadcrumb" "css_element"

  Scenario: Admin user sets the default home page to 'Site' and navigates to its 'Preferences' and 'Private files' page
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    And I log in as "admin"
    When I follow "Preferences" in the user menu
    # There should be no breadcrumbs on this page.
    Then ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"

  Scenario: Admin user sets the default home page to 'Dashboard' and navigates to its 'Preferences' and 'Private files' page
    Given the following config values are set as admin:
      | defaulthomepage | 1 |
    And I log in as "admin"
    When I follow "Preferences" in the user menu
    # There should be no breadcrumbs on this page.
    Then ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"
    And I follow "Private files" in the user menu
    # There should be no breadcrumbs on this page.
    And ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"

  Scenario: Admin user sets the default home page to 'User preference' and navigates to its 'Preferences' and 'Private files' page
    Given the following config values are set as admin:
      | defaulthomepage | 2 |
    And I log in as "admin"
    When I follow "Preferences" in the user menu
    # There should be no breadcrumbs on this page.
    Then ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"
    And I follow "Private files" in the user menu
    # There should be no breadcrumbs on this page.
    And ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"

  Scenario: Admin user sets the default home page to 'My courses' and navigates to its 'Preferences' and 'Private files' page
    Given the following config values are set as admin:
      | defaulthomepage | 3 |
    And I log in as "admin"
    When I follow "Preferences" in the user menu
    # There should be no breadcrumbs on this page.
    Then ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"
    And I follow "Private files" in the user menu
    # There should be no breadcrumbs on this page.
    And ".breadcrumb-item" "css_element" should not exist in the ".breadcrumb" "css_element"
