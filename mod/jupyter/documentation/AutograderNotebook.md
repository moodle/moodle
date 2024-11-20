# Autograder notebook Dokumentation / Autograder notebook Documentation

## Deutsch

### Aufbau der Datei

Die Notebooks müssen folgende Struktur haben:

```shell
# ASSIGNMENT CONFIG

HIER KOMMEN DIE AUFGABEN KONFIGURATIONEN REIN

# BEGIN QUESTION

HIER KOMMEN DIE GESTELLTE FRAGEN REIN

# BEGIN SOLUTION

HIER KOMMT DIE MUSTERLÖSUNG REIN

# END SOLUTION

# BEGIN TEST

HIER KOMMEN DIE TESTS REIN

# END TEST

# END QUESTION
```

### Konfiguration der Aufgaben

Starte das Notebook mit `# ASSIGNMENT CONFIG` und gib der Aufgabe die entsprechenden Konfigurationen. Die verschiedenen Konfigurationsoptionen sind unten aufgeführt.

```python
name: null                     # ein Name für das Notebook (um zu überprüfen, ob die Schüler zur richtigen Autograder-Instanz einreichen)
init_cell: true                # ob eine Otter-Initialisierungszeile in den Ausgabedateien enthalten sein soll
check_all_cell: false          # ob eine Otter-Check-all-cell in den Ausgabedateien enthalten sein soll
export_cell:                   # ob eine Otter-Exportcell in den Ausgabedateien enthalten sein soll
  instructions: ''             # zusätzliche Anweisungen zur Abgabe, die in der Exportcell enthalten sein sollen
  force_save: false            # ob das Notebook mit JavaScript erzwungen gespeichert werden soll (funktioniert nur in klassischem Notebook)
  run_tests: true              # ob die Studentenabgaben bei der Exportierung gegen lokale Tests ausgeführt werden sollen
seed:                          # Konfigurationen für Intercell seeding
  variable: null               # der Name einer Variablen, die während der Bewertung durch den Autograder seed überschrieben werden soll
  autograder_value: null       # der Wert des Autograder seed
  student_value: null          # der Wert des Studenten seed
generate: false                # Konfigurationen zur automatischen Erstellung mit Otter Generate als otter_config.json; wenn false, ist Otter Generate deaktiviert
variables: null                # eine Zuordnung von Variablennamen zu Typzeichenketten für die Serialisierung von Umgebungen
ignore_modules: []             # eine Liste von Modulen, deren Variablen bei der Serialisierung der Umgebung ignoriert werden sollen
tests:                         # Informationen zur Struktur und Speicherung der Tests
  files: false                 # ob die Tests in separaten Dateien gespeichert werden sollen, anstatt in den Metadaten des Notebooks
  ok_format: true              # ob die Testfälle im OK-Format vorliegen (anstelle des auf Ausnahmen basierten Formats)
  url_prefix: null             # ein URL-Präfix, unter dem Testdateien für den Gebrauch durch Studenten gefunden werden können
show_question_points: false    # ob die Punktwerte der Fragen zur letzten Zeile jeder Frage hinzugefügt werden sollen
runs_on: default               # der Interpreter, auf dem dieses Notebook ausgeführt wird, falls er sich vom Standardinterpreter unterscheidet (eine der folgenden Optionen: {'default', 'colab', 'jupyterlite'})
python_version: null           # die Version von Python, die beim Bewerten benutzt wird (muss 3.6+ sein)
```

### Konfiguration der Fragen

Die Konfigurationsoptionen für Aufgaben können nach `# BEGIN QUESTION` festgelegt werden.

```python
name: null        # (erforderlich) der Pfad zu einer requirements.txt-Datei
manual: false     # ob es sich um eine manuell bewertete Frage handelt
points: null      # wie viele Punkte diese Frage wert ist; intern standardmäßig auf 1 gesetzt
check_cell: true  # ob eine Überprüfungszeile nach dieser Frage enthalten sein soll (nur für automatisch bewertete Fragen)
export: false     # ob diese Frage erzwungen in das exportierte PDF aufgenommen werden soll
```

Beispiel:

```python
# BEGIN QUESTION
name: q1
export: true
```

Nach der Konfiguration kann der Lehrer die Aufgabe erstellen. Damit der Autograder funktioniert, muss die Lösung ebenfalls in der Aufgabe enthalten sein. Otter-Grader erstellt die Sicht der Notebook für die Schüler, wenn der Lehrer die Aktivität erstellt und die Datei hochlädt.

Hier ist ein Beispiel, wie Otter-Grader Lösungen entfernen wird.

```python
def square(x):
    y = x * x # SOLUTION NO PROMPT
    return y # SOLUTION

nine = square(3) # SOLUTION
```

Unten ist die Sicht des Schüler.

```python
def square(x):
    ...

nine = ...
```

1. Eine Zeile, die mit `# SOLUTION` endet, wird durch `...` ersetzt, ordnungsgemäß eingerückt. Wenn diese Zeile eine Zuweisungsanweisung ist, werden nur die Ausdrücke nach dem "=" Symbol ersetzt.
1. Eine Zeile, die mit `# SOLUTION NO PROMPT` oder `# SEED` endet, wird entfernt.
1. Eine Zeile `# BEGIN SOLUTION` oder `# BEGIN SOLUTION NO PROMPT` muss mit einer späteren Zeile `# END SOLUTION` gepaart sein. Alle Zeilen dazwischen werden durch `...` ersetzt oder bei `NO PROMPT` vollständig entfernt.
1. Eine Zeile `""" # BEGIN PROMPT` muss mit einer späteren Zeile `""" # END PROMPT` gepaart sein. Der Inhalt dieses mehrzeiligen Strings (ohne `# BEGIN PROMPT`) erscheint in der Schülerzeile. Einzel- oder doppelte Anführungszeichen sind erlaubt. Optional kann ein Semikolon verwendet werden, um die Ausgabe zu unterdrücken: `"""; # END PROMPT`

Ein weiteres Beispiel findet man unten.<br>
Sicht des Lehrers:

```python
pi = 3.14
if True:
    # BEGIN SOLUTION
    radius = 3
    area = radius * pi * pi
    # END SOLUTION
    print('A circle with radius', radius, 'has area', area)

def circumference(r):
    # BEGIN SOLUTION NO PROMPT
    return 2 * pi * r
    # END SOLUTION
    """ # BEGIN PROMPT
    # Next, define a circumference function.
    pass
    """; # END PROMPT
```

Sicht des Schülers:

```python
pi = 3.14
if True:
    ...
    print('A circle with radius', radius, 'has area', area)

def circumference(r):
    # Next, define a circumference function.
    pass
```

### Konfiguration der Tests

Alle Zeilen innerhalb der Begrenzungszeilen `# BEGIN TESTS` und `# END TESTS` gelten als Testzeilen. Jede Testzeile entspricht einem einzelnen Testfall. Es gibt zwei Arten von Tests: öffentliche Tests und versteckte Tests. Tests sind standardmäßig öffentlich, können aber durch das Hinzufügen des Kommentars `# HIDDEN` versteckt werden. Ein versteckter Test wird nicht an die Schüler verteilt, sondern dient zur zusätzlichen Bewertung ihrer Arbeit.
<br>
Folgende Testkonfigurationen werden unterstützt.

```python
hidden: false          # ob der Test versteckt ist
points: null           # der Punktwert des Tests
success_message: null  # eine Nachricht, die dem Schüler angezeigt wird, wenn der Testfall erfolgreich ist
failure_message: null  # eine Nachricht, die dem Schüler angezeigt wird, wenn der Testfall fehlschlägt
```

Beachten Sie, dass wenn eine Frage keine öffentliche Tests enthält und nur versteckte Tests hat, wird die Frage vollständig aus der Ausgabe entfernt.
<br>
Es gibt zwei Arten von Tests.

#### OK-Formatted Tests

Um OK-formatted Tests zu verwenden, die standardmäßig für Otter Assign verwendet werden, können Sie den Testcode in einer Testzeile schreiben. Otter Assign analysiert die Ausgabe der Zeile, um einen doctest für die Frage zu erstellen, der für den Testfall verwendet wird. Stellen Sie sicher, dass nur die letzte Zeile eine Ausgabe erzeugt, da der Test andernfalls fehlschlägt.

#### Exception-Based Tests

Um die Exception-Based Tests von Otter zu verwenden, müssen Sie `tests: ok_format: false` in Ihrer Anfangskonfiguration (`# ASSIGNMENT CONFIG`) festlegen. Ihre Testzeilen sollten eine Testfallfunktion definieren. Sie können den Test im Notebook ausführen, indem Sie die Funktion aufrufen, stellen Sie jedoch sicher, dass dieser Aufruf von Otter Assign "ignoriert" wird, damit er nicht in die Testdatei aufgenommen wird. Hängen Sie dazu `# IGNORE` am Ende der Zeile an. Sie sollten keinen `test_case` hinzufügen. Otter Assign erledigt es.

Zum Beispiel:

```python
""" # BEGIN TEST CONFIG
points: 0.5
""" # END TEST CONFIG
def test_validity(arr):
    assert len(arr) == 10
    assert (0 <= arr <= 1).all()

test_validity(arr)  # IGNORE
```

Es ist wichtig zu beachten, dass die auf Exception-Based Testdateien vor der Bereitstellung der globalen Umgebung des Studenten ausgeführt werden. Daher sollten keine Arbeiten außerhalb der Testfallfunktion durchgeführt werden, die auf Studentencode angewiesen sind. Alle Bibliotheken oder anderen Variablen, die in der Umgebung des Studenten deklariert sind, müssen als Argumente übergeben werden, sonst schlägt der Test fehl.

Zum Beispiel:

```python
def test_values(arr):
    assert np.allclose(arr, [1.2, 3.4, 5.6])  # funktioniert nicht, weil np ist nicht in der Testdatei

def test_values(np, arr):
    assert np.allclose(arr, [1.2, 3.4, 5.6])  # funktioniert

def test_values(env):
    assert env["np"].allclose(env["arr"], [1.2, 3.4, 5.6])  # funktioniert
```

### Weitere Informationen

Weitere Informationen sind [hier](https://otter-grader.readthedocs.io/en/latest/otter_assign/v1/notebook_format.html) zu finden.
<br>
Bitte beachten, dass nicht alle Konfiguration, welche man auf der Homepage von Otter-Grader findet, im Plugin zur Verfügung stehen.

#### Zeilen ignorieren

Für jede Zeile, die man nicht im ausgegebenen Notebook stehen haben will, schreibt man darüber ein `## Ignore ##` Kommentar wie bei den Tests.

```python
## Ignore ##
print("This cell won't appear in the output.")
```

#### Beispiel Notebook

Ein Beispiel eines Notebooks, welches automatisch korrigiert wird, kann in der Datei [demo.ipynb](./demo.ipynb) gefunden werden.

## English

### File Structure

Notebooks must have the following structure:

```shell
# ASSIGNMENT CONFIG

WRITE HERE YOUR ASSIGNMENT CONFIGURATIONS

# BEGIN QUESTION

WRITE HERE YOUR QUESTION/TASK

# BEGIN SOLUTION

WRITE HERE YOUR SOLUTION

# END SOLUTION

# BEGIN TEST

WRITE HERE YOUR TESTS

# END TEST

# END QUESTION
```

### Assignment configuration

Start the notebook with `# ASSIGNMENT CONFIG` and give the assignment the proper configurations. The different configuration options can be found below.

```python
name: null                     # a name for the assignment (to validate that students submit to the correct autograder)
init_cell: true                # whether to include an Otter initialization cell in the output notebooks
check_all_cell: false          # whether to include an Otter check-all cell in the output notebooks
export_cell:                   # whether to include an Otter export cell in the output notebooks
  instructions: ''             # additional submission instructions to include in the export cell
  force_save: false            # whether to force-save the notebook with JavaScript (only works in classic notebook)
  run_tests: true              # whether to run student submissions against local tests during export
seed:                          # intercell seeding configurations
  variable: null               # a variable name to override with the autograder seed during grading
  autograder_value: null       # the value of the autograder seed
  student_value: null          # the value of the student seed
generate: false                # grading configurations to be passed to Otter Generate as an otter_config.json; if false, Otter Generate is disabled
variables: null                # a mapping of variable names to type strings for serializing environments
ignore_modules: []             # a list of modules to ignore variables from during environment serialization
tests:                         # information about the structure and storage of tests
  files: false                 # whether to store tests in separate files, instead of the notebook metadata
  ok_format: true              # whether the test cases are in OK-format (instead of the exception-based format)
  url_prefix: null             # a URL prefix for where test files can be found for student use
show_question_points: false    # whether to add the question point values to the last cell of each question
runs_on: default               # the interpreter this notebook will be run on if different from the default interpreter (one of {'default', 'colab', 'jupyterlite'})
python_version: null           # the version of Python to use in the grading image (must be 3.6+)
```

### Question configurations

Configuration options for tasks can be set after `# BEGIN QUESTION`.

```python
name: null        # (required) the path to a requirements.txt file
manual: false     # whether this is a manually-graded question
points: null      # how many points this question is worth; defaults to 1 internally
check_cell: true  # whether to include a check cell after this question (for autograded questions only)
export: false     # whether to force-include this question in the exported PDF
```

Example:

```python
# BEGIN QUESTION
name: q1
export: true
```

After the configuration the teacher can create the assignment. For the autograder to work, the solution must also be in the assignment. Otter-Grader will create the proper assignment for the students when the teacher create the activity and upload the file.
As an Example, the following code snippet shows how Otter-Grader will remove solutions

```python
def square(x):
    y = x * x # SOLUTION NO PROMPT
    return y # SOLUTION

nine = square(3) # SOLUTION
```

would be presented to the student as

```python
def square(x):
    ...

nine = ...
```

1. A line ending in `# SOLUTION` will be replaced by `...` , properly indented. If that line is an assignment statement, then only the expression(s) after the = symbol will be replaced.
1. A line ending in `# SOLUTION NO PROMPT` or `# SEED `will be removed.
1. A line `# BEGIN SOLUTION` or `# BEGIN SOLUTION NO PROMPT` must be paired with a later line `# END SOLUTION`. All lines in between are replaced with `...` or removed completely in the case of `NO PROMPT`.
1. A line `""" # BEGIN PROMPT` must be paired with a later line `""" # END PROMPT`. The contents of this multiline string (excluding the `# BEGIN PROMPT`) appears in the student cell. Single or double quotes are allowed. Optionally, a semicolon can be used to suppress output: `"""; # END PROMPT`

Another Example is shown below.<br>
Teacher View:

```python
pi = 3.14
if True:
    # BEGIN SOLUTION
    radius = 3
    area = radius * pi * pi
    # END SOLUTION
    print('A circle with radius', radius, 'has area', area)

def circumference(r):
    # BEGIN SOLUTION NO PROMPT
    return 2 * pi * r
    # END SOLUTION
    """ # BEGIN PROMPT
    # Next, define a circumference function.
    pass
    """; # END PROMPT
```

Students View:

```python
pi = 3.14
if True:
    ...
    print('A circle with radius', radius, 'has area', area)

def circumference(r):
    # Next, define a circumference function.
    pass
```

### Test Configurations

Any cells within the `# BEGIN TESTS` and `# END TESTS` boundary cells are considered test cells. Each test cell corresponds to a single test case. There are two types of tests: public and hidden tests. Tests are public by default but can be hidden by adding the `# HIDDEN` comment as the first line of the cell. A hidden test is not distributed to students, but is used for scoring their work.
<br>
Following test configuration are supported:

```python
hidden: false          # whether the test is hidden
points: null           # the point value of the test
success_message: null  # a messsge to show to the student when the test case passes
failure_message: null  # a messsge to show to the student when the test case fails
```

Note that if a question has no solution cell provided, the question will either be removed from the output notebook entirely if it has only hidden tests.
<br>
There are two types of tests.

#### OK-Formatted Test Cells

To use OK-formatted tests, which are the default for Otter Assign, you can write the test code in a test cell; Otter Assign will parse the output of the cell to write a doctest for the question, which will be used for the test case. **Make sure that only the last line of the cell produces any output, otherwise the test will fail.**

#### Exception-Based Test Cells

To use Otter’s exception-based tests, you must set `tests: ok_format: false` in your assignment config. Your test cells should define a test case function. You can run the test in the master notebook by calling the function, but you should make sure that this call is “ignored” by Otter Assign so that it’s not included in the test file by appending `# IGNORE` to the end of line. You should not add the `test_case` decorator; Otter Assign will do this for you.

For example,

```python
""" # BEGIN TEST CONFIG
points: 0.5
""" # END TEST CONFIG
def test_validity(arr):
    assert len(arr) == 10
    assert (0 <= arr <= 1).all()

test_validity(arr)  # IGNORE
```

It is important to note that the exception-based test files are executed before the student’s global environment is provided, so no work should be performed outside the test case function that relies on student code, and any libraries or other variables declared in the student’s environment must be passed in as arguments, otherwise the test will fail.

For example,

```python
def test_values(arr):
    assert np.allclose(arr, [1.2, 3.4, 5.6])  # this will fail, because np is not in the test file

def test_values(np, arr):
    assert np.allclose(arr, [1.2, 3.4, 5.6])  # this works

def test_values(env):
    assert env["np"].allclose(env["arr"], [1.2, 3.4, 5.6])  # this also works
```

### Further Information

For further information look [here](https://otter-grader.readthedocs.io/en/latest/otter_assign/v1/notebook_format.html).
<br>
Note that **NOT** every configuration options on Otter-Grader Homepage can be used in our plugin.

#### Ignoring cells

For any cells that you don’t want to be included in either of the output notebooks that are present in the master notebook, include a line at the top of the cell with the ## Ignore ## comment (case insensitive) just like with test cells.

```python
## Ignore ##
print("This cell won't appear in the output.")
```

#### Sample Notebook

An example of an notebook that can be autograde can be found in [demo.ipynb](./demo.ipynb).
