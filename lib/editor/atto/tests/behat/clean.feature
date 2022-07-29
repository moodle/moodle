@editor @editor_atto @atto @editor_moodleform
Feature: Atto HTML cleanup.
  In order to test html cleaning functionality, I write in a HTML atto text field.

  @javascript
  Scenario: Extra UL close and orphan LI items
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I set the field "Description" to multiline:
    """
        <li>A</li>
        <li>B</li>
    </ol>
    <ul>
        <li>C</li>
    </ul></ul>
    <li class="someclass ul UL">D</li>
    <li>E</li>
    """
    And I click on "HTML" "button"
    Then the field "Description" matches multiline:
    """
        <ol><li>A</li>
        <li>B</li>
    </ol>
    <ul>
        <li>C</li>
    </ul>
    <ul><li class="someclass ul UL">D</li>
    <li>E</li></ul>
    """

  @javascript
  Scenario: Missing LI close tags, extra closing OL, missing closing UL tag
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I set the field "Description" to multiline:
    """
    <div class="ol"><ol>
        <li>A</li>
        <li>B
    </ol></div>
    <ul>
        <li>C
        <li>D</li>
    </ol>
    """
    And I click on "HTML" "button"
    Then the field "Description" matches multiline:
    """
    <div class="ol"><ol>
        <li>A</li>
        <li>B
    </li></ol></div>
    <ul>
        <li>C
        </li><li>D</li></ul>

    """

  @javascript
  Scenario: Missing beginning OL tag, empty LI close tag
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I set the field "Description" to multiline:
    """
    <p>Before</p>
        <li>A</li></li>
        <li>B</li>
    </ol>
    <p>After</p>
    <ul data-info="UL ul OL ol">
        <ul>
            C</li>
            <li>D</li>
            <li>E
        </ul>
    </ul><ul>
    <p>After 2</p>
    """
    And I click on "HTML" "button"
    Then the field "Description" matches multiline:
    """
    <p>Before</p>
        <ol><li>A</li>
        <li>B</li>
    </ol>
    <p>After</p>
    <ul data-info="UL ul OL ol">
        <ul><li>
            C</li>
            <li>D</li>
            <li>E
        </li></ul>
    </ul>
    <p>After 2</p>
    """

  @javascript
  Scenario: Random close LI tag, extra LI open tag, missing OL tag
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I set the field "Description" to multiline:
    """
    <p>Before</p></li><ul>
    <ul>
        <li>A</li>
        B</li>
        <li>C</li>
    <ol>
        <li>D</li>
        <li>E
    <p>After</p>
    """
    And I click on "HTML" "button"
    Then the field "Description" matches multiline:
    """
    <p>Before</p>
    <ul>
        <li>A</li><li>
        B</li>
        <li>C</li></ul>
    <ol>
        <li>D</li></ol>
        E
    <p>After</p>
    """

  @javascript
  Scenario: Missing opening LI tags, missing closing UL tag
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I set the field "Description" to multiline:
    """
    <li>Before</li>
    <ul>
        <li>A</li>
        B</li>
        <ol>
            1</li>
        </ol>
        <li>C
        <li>D</li>
    <p>After</p>
    """
    And I click on "HTML" "button"
    Then the field "Description" matches multiline:
    """
    <ul><li>Before</li></ul>
    <ul>
        <li>A</li><li>
        B</li>
        <ol><li>
            1</li>
        </ol>
        <li>C
        </li><li>D</li></ul>
    <p>After</p>
    """
