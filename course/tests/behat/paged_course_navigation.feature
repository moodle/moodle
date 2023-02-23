@core @core_course
Feature: Course paged mode
  In order to split the course in parts
  As a teacher
  I need to display the course in a paged mode and navigate through the different sections

  @javascript @_cross_browser
  Scenario Outline: Weekly and topics course formats with Javascript enabled
    Given the following "courses" exist:
      | fullname | shortname | category | format | coursedisplay | numsections | startdate |
      | Course 1 | C1 | 0 | <courseformat> | 1 | 3 | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    Then I click on <section2> "link" in the <section2> "section"
    And I am on "Course 1" course homepage
    And I click on <section3> "link" in the <section3> "section"
    And I am on "Course 1" course homepage
    And I click on <section1> "link" in the <section1> "section"
    And I should see <section1> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I click on <section2> "link" in the ".single-section" "css_element"
    And I should see <section2> in the "div.single-section" "css_element"
    And I should see <section1> in the ".single-section div.prevsection" "css_element"
    And I should see <section3> in the ".single-section div.nextsection" "css_element"
    And I click on <section1> "link" in the ".single-section" "css_element"
    And I should see <section1> in the "div.single-section" "css_element"
    And I click on <section2> "link" in the ".single-section" "css_element"
    And I click on <section3> "link" in the ".single-section" "css_element"
    And I should see <section3> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.prevsection" "css_element"
    And I should not see <section1> in the ".single-section .section-navigation" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I should not see <nextunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | section1 | section2 | section3 | prevunexistingsection | nextunexistingsection |
      | topics | "Topic 1" | "Topic 2" | "Topic 3" | "Topic 0" | "Topic 4" |
      | weeks | "1 January - 7 January" | "8 January - 14 January" | "15 January - 21 January" | "25 December - 31 December" | "22 January - 28 January" |

  @javascript
  Scenario Outline: Paged section redirect after creating an activity
    Given the following "courses" exist:
      | fullname | shortname | category | format | coursedisplay | numsections | startdate |
      | Course 1 | C1        | 0        | <courseformat> | 1     | 3           | 0         |
    And the following "activities" exist:
      | activity | course | name      |
      | chat     | C1     | Chat room |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I click on <section1> "link" in the <section1> "section"
    And I should see <section1> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    Then I should see <section1> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | section1 | section2 | prevunexistingsection |
      | topics       | "Topic 1" | "Topic 2" | "Topic 0"          |
      | weeks        | "1 January - 7 January" | "8 January - 14 January" | "25 December - 31 December" |

  Scenario Outline: Weekly and topics course formats with Javascript disabled
    Given the following "courses" exist:
      | fullname | shortname | category | format | coursedisplay | numsections | startdate |
      | Course 1 | C1 | 0 | <courseformat> | 1 | 3 | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    Then I click on <section2> "link" in the <section2> "section"
    And I am on "Course 1" course homepage
    And I click on <section3> "link" in the <section3> "section"
    And I am on "Course 1" course homepage
    And I click on <section1> "link" in the <section1> "section"
    And I should see <section1> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I click on <section2> "link" in the ".single-section" "css_element"
    And I should see <section2> in the "div.single-section" "css_element"
    And I should see <section1> in the ".single-section div.prevsection" "css_element"
    And I should see <section3> in the ".single-section div.nextsection" "css_element"
    And I click on <section1> "link" in the ".single-section" "css_element"
    And I should see <section1> in the "div.single-section" "css_element"
    And I click on <section2> "link" in the ".single-section" "css_element"
    And I click on <section3> "link" in the ".single-section" "css_element"
    And I should see <section3> in the "div.single-section" "css_element"
    And I should see <section2> in the ".single-section div.prevsection" "css_element"
    And I should not see <section1> in the ".single-section .section-navigation" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I should not see <nextunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | section1 | section2 | section3 | prevunexistingsection | nextunexistingsection |
      | topics | "Topic 1" | "Topic 2" | "Topic 3" | "Topic 0" | "Topic 4" |
      | weeks | "1 January - 7 January" | "8 January - 14 January" | "15 January - 21 January" | "25 December - 31 December" | "22 January - 28 January" |
