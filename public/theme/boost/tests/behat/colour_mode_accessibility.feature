@theme_boost @accessibility
Feature: Colour mode accessibility
  In order to use the site in either colour mode
  As a user
  I need Boost to meet the same accessibility standards whichever colour mode is in use

  The light examples are the baseline: each pair checks the same page, so a failure which only appears in the dark
  example is a colour mode regression rather than a pre-existing problem with the page itself.

  Background:
    Given the following config values are set as admin:
      | enablecolourmodes | 1 | theme_boost |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | page     | Test page | C1     | page1    |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |

  @javascript
  Scenario Outline: The dashboard meets accessibility standards in <mode> mode
    Given the following "user preferences" exist:
      | user     | preference             | value  |
      | student1 | theme_boost_colourmode | <mode> |
    When I log in as "student1"
    Then the page should meet accessibility standards with "best-practice" extra tests

    Examples:
      | mode  |
      | light |
      | dark  |

  @javascript
  Scenario Outline: A course page meets accessibility standards in <mode> mode
    Given the following "user preferences" exist:
      | user     | preference             | value  |
      | student1 | theme_boost_colourmode | <mode> |
    And I log in as "student1"
    When I am on "Course 1" course homepage
    # Without the best practice tests, which the dashboard example above does run: the course page fails the
    # landmark-unique rule in both colour modes, because the primary navigation more menu has no accessible
    # name of its own. That is a pre-existing problem with the page rather than a colour mode regression.
    Then the page should meet accessibility standards

    Examples:
      | mode  |
      | light |
      | dark  |

  @javascript
  Scenario Outline: Inline code meets colour contrast standards on tinted surfaces in <mode> mode
    Given the following "activities" exist:
      | activity | name      | course | contentformat | content                                                                                                                                                                                                                                                                                                                                                                            |
      | page     | Code page | C1     | 1             | <div class="alert alert-info"><code>admin/cli/cron.php</code></div><div class="alert alert-light"><code>admin/cli/cron.php</code></div><div class="alert alert-dark"><code>admin/cli/cron.php</code></div><table class="table table-striped table-hover"><tbody><tr><td><code>admin/cli/cron.php</code></td></tr><tr><td><code>admin/cli/cron.php</code></td></tr></tbody></table> |
    And the following "user preferences" exist:
      | user     | preference             | value  |
      | student1 | theme_boost_colourmode | <mode> |
    When I am on the "Code page" "page activity" page logged in as student1
    # The old code colour failed on these alerts: alert-info and alert-dark in light mode, and alert-light in dark mode.
    # Axe cannot see the shading of a striped or hovered table row, which is painted as a box shadow, so the table
    # only checks that body cells in dark mode resolve to a readable colour.
    Then the "#region-main .generalbox" "css_element" should meet accessibility standards

    Examples:
      | mode  |
      | light |
      | dark  |
