@core @core_course
Feature: Course paged mode
  In order to split the course in parts
  As a teacher
  I need to display the course in a paged mode and navigate through the different sections

  @javascript @_cross_browser
  Scenario Outline: Weekly and topics course formats with Javascript enabled
    Given the following "courses" exist:
      | fullname | shortname | category | format         | coursedisplay | numsections | startdate | initsections   |
      | Course 1 | C1        | 0        | <courseformat> | 1             | 2           | 0         | <initsections> |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on <section1> "link" in the <section1> "section"
    And I am on "Course 1" course homepage
    And I click on <section2> "link" in the <section2> "section"
    And I am on "Course 1" course homepage
    When I click on <general> "link" in the <general> "section"
    Then I should see <general> in the "div.page-context-header" "css_element"
    And I should see <section1> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I click on <section1> "link" in the ".single-section" "css_element"
    And I should see <section1> in the "div.page-context-header" "css_element"
    And I should see <general> in the ".single-section div.prevsection" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I click on <section2> "link" in the ".single-section" "css_element"
    And I should see <section2> in the "div.page-context-header" "css_element"
    And I should not see <general> in the ".single-section .section-navigation" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I should not see <nextunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | initsections | general   | section1                | section2                 | prevunexistingsection       | nextunexistingsection     |
      | topics       | 1            | "General" | "Section 1"             | "Section 2"              | "Section 0"                 | "Section 3"               |
      | weeks        | 0            | "General" | "1 January - 7 January" | "8 January - 14 January" | "25 December - 31 December" | "15 January - 21 January" |

  @javascript
  Scenario Outline: Paged section redirect after creating an activity
    Given the following "courses" exist:
      | fullname | shortname | category | format | coursedisplay | numsections | startdate | initsections   |
      | Course 1 | C1        | 0        | <courseformat> | 1     | 3           | 0         | <initsections> |
    And the following "activities" exist:
      | activity | course | name       |
      | assign   | C1     | Assignment |
    When I am on the <courseandsection> "course > section" page logged in as "admin"
    And I turn editing mode on
    And I should see <section1> in the "div.page-context-header" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    Then I should see <section1> in the "div.page-context-header" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | initsections | courseandsection                   | section1                | section2                 | prevunexistingsection       |
      | topics       | 1            | "Course 1 > Section 1"             | "Section 1"             | "Section 2"              | "Section 0"                 |
      | weeks        | 0            | "Course 1 > 1 January - 7 January" | "1 January - 7 January" | "8 January - 14 January" | "25 December - 31 December" |

  Scenario Outline: Weekly and topics course formats with Javascript disabled
    Given the following "courses" exist:
      | fullname | shortname | category | format         | coursedisplay | numsections | startdate | initsections   |
      | Course 1 | C1        | 0        | <courseformat> | 1             | 2           | 0         | <initsections> |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    Then I click on <section1> "link" in the <section1> "section"
    And I am on "Course 1" course homepage
    And I click on <section2> "link" in the <section2> "section"
    And I am on "Course 1" course homepage
    And I click on <general> "link" in the <general> "section"
    And I should see <general> in the "div.page-context-header" "css_element"
    And I should see <section1> in the ".single-section div.nextsection" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I am on the <courseandsection1> "course > section" page
    And I should see <section1> in the "div.page-context-header" "css_element"
    And I should see <general> in the ".single-section div.prevsection" "css_element"
    And I should see <section2> in the ".single-section div.nextsection" "css_element"
    And I am on the <courseandsection2> "course > section" page
    And I should see <section2> in the "div.page-context-header" "css_element"
    And I should not see <general> in the ".single-section .section-navigation" "css_element"
    And I should not see <prevunexistingsection> in the ".single-section" "css_element"
    And I should not see <nextunexistingsection> in the ".single-section" "css_element"

    Examples:
      | courseformat | initsections | courseandsection1                  | courseandsection2                   | general   | section1                | section2                 | prevunexistingsection       | nextunexistingsection     |
      | topics       | 1            | "Course 1 > Section 1"             | "Course 1 > Section 2"              | "General" | "Section 1"             | "Section 2"              | "Section 0"                 | "Section 3"               |
      | weeks        | 0            | "Course 1 > 1 January - 7 January" | "Course 1 > 8 January - 14 January" | "General" | "1 January - 7 January" | "8 January - 14 January" | "25 December - 31 December" | "15 January - 21 January" |
