# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @author     Rafael Becerra rafael.becerrarodriguez@openlms.net
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: In Open forums while using Snap, the student should see the options
  to manage forum subscriptions, export the post, view the posters and subscribe or
  unsubscribe from the post.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name            | intro      | course | idnumber | groupmode |
      | hsuforum | Test forum name | Test forum | C1     | hsuforum | 0         |

  @javascript
  Scenario: Check that the links for Open forums options exists and can be activated

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on ".modtype_hsuforum .mod-link" "css_element"
    And I add a new discussion to "Test forum name" Open Forum with:
      | Subject | Forum discussion 1                    |
      | Message | How awesome is this forum discussion? |
    And I should see "Export"
    And I should see "View posters"
    And I should see "Unsubscribe from this forum"
    And I click on "li.exportdiscussions-url a" "css_element"
    And I should see "Export attachments"
    And I click on "Cancel" "button"
    And I click on "li.subscribeforum-url a" "css_element"
    And I should see "Subscribe to this forum"
    And I click on "li.subscribeforum-url a" "css_element"
    And I should see "Unsubscribe from this forum"
    And I click on "li.viewposters-url a" "css_element"
    And I should see "View posters"

  @javascript
  Scenario: An user not enrolled to the course should not be able to subscribe to a post in a forum
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on ".modtype_hsuforum .mod-link" "css_element"
    And I should see "Subscribe to this forum"
    And I click on "li.subscribeforum-url a" "css_element"
    And I should see "Subscribe to this forum"
    And I should see "Sorry, only enrolled users are allowed to subscribe to forum post notifications."
