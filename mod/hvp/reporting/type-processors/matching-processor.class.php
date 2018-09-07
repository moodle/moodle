<?php

/**
 * Class MatchingProcessor
 */
class MatchingProcessor extends TypeProcessor  {

  /**
   * Pattern for separating between expressions.
   */
  const EXPRESSION_SEPARATOR = '[,]';

  /**
   * Pattern for separating between matching elements
   */
  const MATCH_SEPARATOR = '[.]';


  /**
   * Processes xAPI data and returns a human readable HTML report
   *
   * @inheritdoc
   */
  function generateHTML($description, $crp, $response, $extras = NULL, $scoreSettings = NULL) {
    // We need some style for our report
    $this->setStyle('styles/matching.css');

    $dropzones = $this->getDropzones($extras);
    $draggables = $this->getDraggables($extras);

    $mappedCRP = $this->mapPatternIDsToIndexes($crp[0],
      $dropzones,
      $draggables);

    $mappedResponse = $this->mapPatternIDsToIndexes($response,
      $dropzones,
      $draggables);

    if (empty($mappedCRP) && empty($mappedResponse)) {
      return '';
    }

    $header = $this->generateHeader($description, $scoreSettings);
    $tableHTML = $this->generateTable($mappedCRP,
      $mappedResponse,
      $dropzones,
      $draggables
    );
    $container = '<div class="h5p-reporting-container h5p-matching-container">' .
                   $header . $tableHTML .
                 '</div>';

    return $container;
  }

  /**
   * Generate header element
   *
   * @param $description
   * @param $scoreSettings
   *
   * @return string
   */
  private function generateHeader($description, $scoreSettings) {
    $descriptionHtml = $this->generateDescription($description);
    $scoreHtml = $this->generateScoreHtml($scoreSettings);

    return
      "<div class='h5p-matching-header'>" .
        $descriptionHtml . $scoreHtml .
      "</div>";
  }

  /**
   * Generate description element
   *
   * @param string $description
   *
   * @return string Description element as a string
   */
  private function generateDescription($description) {
    return
      '<p class="h5p-reporting-description h5p-matching-task-description">' .
        $description .
      '</p>';
  }

  /**
   * Create a map that links IDs from pattern to indexes in the droppable and
   * draggable arrays.
   *
   * @param string $pattern
   * @param array $dropzoneIds
   * @param array $draggableIds
   *
   * @return array Pattern mapped to indexes instead of IDs
   */
  function mapPatternIDsToIndexes($pattern, $dropzoneIds, $draggableIds) {
    $mappedMatches = array();
    if (empty($pattern)) {
      return $mappedMatches;
    }

    $singlePatterns = explode(self::EXPRESSION_SEPARATOR, $pattern);
    foreach($singlePatterns as $singlePattern) {
      $matches = explode(self::MATCH_SEPARATOR, $singlePattern);

      // ID does not necessarily map to index, so we must remap it
      $dropzoneId = $this->findIndexOfItemWithId($dropzoneIds, $matches[0]);
      $draggableId = $this->findIndexOfItemWithId($draggableIds, $matches[1]);

      if (!isset($mappedMatches[$dropzoneId])) {
        $mappedMatches[$dropzoneId] = array();
      }

      $mappedMatches[$dropzoneId][] = $draggableId;
    }

    return $mappedMatches;
  }

  /**
   * Find id of an item with a given index inside given array
   *
   * @param array $haystack
   * @param number $id
   *
   * @return number Id of mapped item
   */
  function findIndexOfItemWithId($haystack, $id) {
    return (isset($haystack[$id]) ? $haystack[$id]->id : NULL);
  }

  /**
   * Generate table from user response, correct response pattern, dropzones and
   * draggables
   *
   * @param array $mappedCRP
   * @param array $mappedResponse
   * @param array $dropzones
   * @param array $draggables
   *
   * @return string Table element
   */
  function generateTable($mappedCRP, $mappedResponse, $dropzones, $draggables) {
    $header = $this->generateTableHeader();
    $rows = $this->generateRows($mappedCRP, $mappedResponse, $dropzones,
      $draggables);

    return '<table class="h5p-matching-table">' . $header . $rows . '</table>';
  }

  /**
   * Generate rows of table
   *
   * @param array $mappedCRP
   * @param array $mappedResponse
   * @param array $dropzones
   * @param array $draggables
   *
   * @return string HTML for generated table rows
   */
  function generateRows($mappedCRP, $mappedResponse, $dropzones, $draggables) {
    $html = '';
    foreach($dropzones as $index => $value) {
      $html .= $this->generateDropzoneRows($value,
        $draggables,
        isset($mappedCRP[$index]) ? $mappedCRP[$index] : array(),
        isset($mappedResponse[$index]) ? $mappedResponse[$index] : array()
      );
    }
    return $html;
  }

  /**
   * Sort handler for comparing result rows
   *
   * @param stdClass $a
   * @param stdClass $b
   * @return int
   */
  private static function rowcmp($a, $b) {
    if ($a->isCorrect && $b->isCorrect || !$a->isCorrect && !$b->isCorrect) {
      return strcmp($a->response, $b->response);
    }
    if ($a->isCorrect && !$b->isCorrect) {
      return -1;
    }
    if (!$a->isCorrect && $b->isCorrect) {
      return 1;
    }
  }

  /**
   * Creates the inital set of rows needed when generating the table HTML
   *
   * @param array $draggables
   * @param array $crp
   * @param array $response
   * @return array
   */
  private static function createUserAnswerRows(&$draggables, &$crp, &$response) {
    // Create list with all rows to display
    $rows = array();
    foreach ($response as $key => $answer) {

      $row = (object) array(
        'isCorrect' => in_array($answer, $crp),
        'crp' => '',
      );

      // Locate response label
      foreach ($draggables as $draggable) {
        if ($draggable->id === $answer) {
          $row->response = $draggable->value;
          break;
        }
      }

      $rows[] = $row;
    }

    // Sort rows
    usort($rows, array('MatchingProcessor', 'rowcmp'));
    return $rows;
  }

  /**
   * Creates a list of soluton labels.
   *
   * @param array $draggables
   * @param array $crp
   * @return array
   */
  private static function createSolutionRows(&$draggables, &$crp) {
    // Create list of solution labels
    $solutions = array();
    foreach ($crp as $pattern) {
      $solutions[] = $draggables[$pattern]->value;
    }
    sort($solutions);
    return $solutions;
  }

  /**
   * Puts the solutions labels into the approperiate rows.
   *
   * @param array $rows
   * @param array $solutions
   */
  private static function addSolutionsToRows(&$rows, &$solutions) {
    // Add solution labels to rows
    foreach ($rows as $key => &$row) {
      if (empty($solutions)) {
        break; // All solutions have been added
      }

      if ($row->isCorrect) {
        // Add solution if hasn't been added yet
        $index = array_search($row->response, $solutions);
        if ($index !== FALSE) {
          $row->crp = $solutions[$index];
          unset($solutions[$index]); // Prevent adding multiple times
        }
      }
      else {
        // Add the next solution
        $row->crp = array_shift($solutions);
      }
    }

    // In case we still have some solutions left, add extra rows for them
    foreach ($solutions as $solution) {
      $rows[] = (object) array(
        'isCorrect' => FALSE,
        'crp' => $solution,
        'response' => '',
      );
    }
  }

  /**
   * Generate row for a single dropzone and populate it with correct answers and
   * user answers
   *
   * @param object $dropzone
   * @param array $draggables
   * @param array $crp
   * @param array $response
   *
   * @return string Drop zone rows element
   */
  function generateDropzoneRows($dropzone, $draggables, $crp, $response) {
    if (!count($response) && !count($crp)) {
      return ''; // Skip if no correct or user answers
    }

    // Get rows needed to display user answers
    $rows = self::createUserAnswerRows($draggables, $crp, $response);

    // Get correct solutions labels for the task
    $solutions = self::createSolutionRows($draggables, $crp);

    // Merges the solutions into the correct rows
    self::addSolutionsToRows($rows, $solutions);

    // Ready to generate the HTML
    $rowsHtml = '';
    $lastCellInRow = 'h5p-matching-last-cell-in-row';
    $numRows = count($rows);

    foreach ($rows as $key => &$row) {
      $rowHtml = '';
      $tdClass = ($key >= $numRows - 1 ? $lastCellInRow : '');

      if ($key === 0) {
        // Print Drop Zone
        $rowHtml .=
          '<th class="' . 'h5p-matching-dropzone ' . $lastCellInRow . '"' .
            ' rowspan="' . $numRows . '"' .
          '>' .
            $dropzone->value .
          '</th>';
      }

      // Add correct response pattern
      $rowHtml .= '<td class="' . $tdClass . '">' .
                $row->crp .
              '</td>';

      // Add user reponse
      $correctClass = ($row->isCorrect ? 'h5p-matching-draggable-correct' : 'h5p-matching-draggable-wrong');
      $classes = $tdClass . ($tdClass !== '' && $correctClass !== '' ?  ' ' : '') . ($row->response !== '' ? $correctClass : '');
      $rowHtml .= '<td class="' . $classes . '">' .
                $row->response .
              '</td>';

      $rowsHtml .= '<tr>' . $rowHtml . '</tr>';
    }

    return $rowsHtml;
  }

  /**
   * Generate table header
   *
   * @return string Table header element as a string
   */
  function generateTableHeader() {
    // Empty first item
    $html = '<th class="h5p-matching-header-dropzone">Dropzone</th>' .
            '<th class="h5p-matching-header-correct">Correct Answers</th>' .
            '<th class="h5p-matching-header-user">Your answers</th>';

    return '<tr class="h5p-matching-table-heading">' . $html . '</tr>';
  }

  /**
   * Extract drop zones from extras parameters
   *
   * @param object $extras
   *
   * @return array Drop zones
   */
  function getDropzones($extras) {
    $dropzones = array();

    foreach($extras->target as $value) {
      $dropzones[] = (object) array(
        'id' => $value->id,
        'value' => $value->description->{'en-US'}
      );
    }

    return $dropzones;
  }

  /**
   * Extract draggables from extras parameters
   *
   * @param object $extras
   *
   * @return array Draggables
   */
  function getDraggables($extras) {
    $draggables = array();

    foreach($extras->source as $value) {
      $draggables[] = (object) array(
        'id' => $value->id,
        'value' => $value->description->{'en-US'}
      );
    }

    return $draggables;
  }
}
