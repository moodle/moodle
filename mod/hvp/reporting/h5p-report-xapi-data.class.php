<?php

class H5PReportXAPIData {

  private $statement, $onlyScore, $children, $parentID;

  /**
   * @param object $data Containing 'statement' and 'children'
   * @param int $parentID Optional parent identifier
   */
  public function __construct($data, $parentID = NULL) {
    // Keep track of statement and children
    if (isset($data->statement)) {
      $this->statement = $data->statement;
    }
    else if (isset($data->onlyScore)) {
      $this->onlyScore = $data->onlyScore;
    }

    $this->parentID = $parentID;

    if (!empty($data->children)) {
      $this->children = $data->children;
    }
  }

  /**
   * Check if the interaction has sub interactions with scoring.
   *
   * @return boolean
   */
  public function isCompound() {
    return ($this->getInteractionType() === 'compound');
  }

  /**
   * Get list of children with given parentID
   *
   * @param int $parentID
   * @return array
   */
  public function getChildren($parentID=NULL) {
    $children = array();

    // Parse children data
    if (!empty($this->children)) {
      foreach ($this->children as $child) {
        $children[] = new H5PReportXAPIData($child, $parentID);
      }
    }

    return $children;
  }

  /**
   * Get the ID of the parent statement.
   * Only works for statements part of a compound interaction.
   *
   * @return int
   */
  public function getParentID() {
    return $this->parentID;
  }

  /**
   * Get score of given type from statement result
   *
   * @param string $type
   * @return float
   */
  private function getScore($type) {
    return (isset($this->statement->result->score->{$type}) ? (float) $this->statement->result->score->{$type} : NULL);
  }

  /**
   * Get the optional scaled score.
   * Must be between -1 and 1.
   *
   * @return float
   */
  public function getScoreScaled() {
    if (isset($this->onlyScore)) {
      // Special case if we only have the scaled score.

      $score = 0.;
      if ($this->onlyScore !== 1 && is_numeric($this->onlyScore)) {
        // Let's "decrypt" it…
        $score = $this->onlyScore / 1.234 - 32.17;
      }
      if ($score < 0 || $score > 1) {
        // Invalid score
        $score = 0.;
      }
      return $score;
    }

    $score = $this->getScore('scaled');

    if ($score !== NULL) {
      if ($score < -1) {
        $score = -1.;
      }
      elseif ($score > 1) {
        $score = 1.;
      }
    }

    return $score;
  }

  /**
   * Get the required raw score for the interaction.
   * Can be anything between min and max.
   *
   * @return float
   */
  public function getScoreRaw() {
    return $this->getScore('raw');
  }

  /**
   * Get the optional min. score
   *
   * @return float
   */
  public function getScoreMin() {
    return $this->getScore('min');
  }

  /**
   * Get the optional max. score
   *
   * @return float
   */
  public function getScoreMax() {
    return $this->getScore('max');
  }

  /**
   * Get object definition property or default value if not set.
   *
   * @param string $property
   * @param mixed $default If not set. Default default is blank string.
   * @return mixed
   */
  private function getObjectDefinition($property, $default = '') {
    return (isset($this->statement->object->definition->{$property}) ? $this->statement->object->definition->{$property} : $default);
  }

  /**
   * Get the type of interaction.
   *
   * @return string
   */
  public function getInteractionType() {
    // Can be any string
    return $this->getObjectDefinition('interactionType');
  }

  /**
   * Get the description of the interaction.
   *
   * @return string
   */
  public function getDescription() {
    $description = $this->getObjectDefinition('description');
    if ($description !== '') {
      $description = (isset($description->{'en-US'}) ? $description->{'en-US'} : '');
    }

    return $description;
  }

  /**
   * Get the correct reponse patterns.
   *
   * @return string
   */
  public function getCorrectResponsesPattern() {
    $correctResponsesPattern = $this->getObjectDefinition('correctResponsesPattern');
    if (is_array($correctResponsesPattern)) {
      return json_encode($correctResponsesPattern);
    }

    return '';
  }

  /**
   * Get the user reponse.
   *
   * @return string
   */
  public function getResponse() {
    return (isset($this->statement->result->response) ? $this->statement->result->response : '');
  }

  /**
   * Get additonal data for some interaction types.
   *
   * @return string JSON
   */
  public function getAdditionals() {
    $additionals = array();

    switch ($this->getInteractionType()) {
      case 'choice':
        $additionals['choices'] = $this->getObjectDefinition('choices', array());
        $additionals['extensions'] = $this->getObjectDefinition('extensions', (object)array());
        break;

      case 'long-choice':
        $additionals['choices'] = $this->getObjectDefinition('choices', array());
        $additionals['extensions'] = $this->getObjectDefinition('extensions', (object)array());
        break;

      case 'matching':
        $additionals['source'] = $this->getObjectDefinition('source', array());
        $additionals['target'] = $this->getObjectDefinition('target', array());
        break;

      case 'long-fill-in':
        $additionals['longfillin'] = true;
        $additionals['extensions'] = $this->getObjectDefinition('extensions', (object)array());
        break;

      default:
        $additionals['extensions'] = $this->getObjectDefinition('extensions', (object)array());
    }

    // Add context extensions
    $additionals['contextExtensions'] = isset($this->statement->context->extensions)
      ? $this->statement->context->extensions : new stdClass();

    return (empty($additionals) ? '' : json_encode($additionals));
  }

  /**
   * Checks if data is valid
   *
   * @return bool True if valid data
   */
  public function validateData() {

    if ($this->getInteractionType() === '') {
      return false;
    }

    // Validate children
    $children = $this->getChildren();
    foreach ($children as $child) {
      if (!$child->validateData()) {
        return false;
      }
    }

    return true;
  }
}
