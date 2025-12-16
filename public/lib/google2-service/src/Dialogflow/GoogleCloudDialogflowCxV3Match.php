<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3Match extends \Google\Model
{
  /**
   * Not specified. Should never be used.
   */
  public const MATCH_TYPE_MATCH_TYPE_UNSPECIFIED = 'MATCH_TYPE_UNSPECIFIED';
  /**
   * The query was matched to an intent.
   */
  public const MATCH_TYPE_INTENT = 'INTENT';
  /**
   * The query directly triggered an intent.
   */
  public const MATCH_TYPE_DIRECT_INTENT = 'DIRECT_INTENT';
  /**
   * The query was used for parameter filling.
   */
  public const MATCH_TYPE_PARAMETER_FILLING = 'PARAMETER_FILLING';
  /**
   * No match was found for the query.
   */
  public const MATCH_TYPE_NO_MATCH = 'NO_MATCH';
  /**
   * Indicates an empty query.
   */
  public const MATCH_TYPE_NO_INPUT = 'NO_INPUT';
  /**
   * The query directly triggered an event.
   */
  public const MATCH_TYPE_EVENT = 'EVENT';
  /**
   * The query was matched to a Knowledge Connector answer.
   */
  public const MATCH_TYPE_KNOWLEDGE_CONNECTOR = 'KNOWLEDGE_CONNECTOR';
  /**
   * The query was handled by a `Playbook`.
   */
  public const MATCH_TYPE_PLAYBOOK = 'PLAYBOOK';
  /**
   * The confidence of this match. Values range from 0.0 (completely uncertain)
   * to 1.0 (completely certain). This value is for informational purpose only
   * and is only used to help match the best intent within the classification
   * threshold. This value may change for the same end-user expression at any
   * time due to a model retraining or change in implementation.
   *
   * @var float
   */
  public $confidence;
  /**
   * The event that matched the query. Filled for `EVENT`, `NO_MATCH` and
   * `NO_INPUT` match types.
   *
   * @var string
   */
  public $event;
  protected $intentType = GoogleCloudDialogflowCxV3Intent::class;
  protected $intentDataType = '';
  /**
   * Type of this Match.
   *
   * @var string
   */
  public $matchType;
  /**
   * The collection of parameters extracted from the query. Depending on your
   * protocol or client library language, this is a map, associative array,
   * symbol table, dictionary, or JSON object composed of a collection of
   * (MapKey, MapValue) pairs: * MapKey type: string * MapKey value: parameter
   * name * MapValue type: If parameter's entity type is a composite entity then
   * use map, otherwise, depending on the parameter value type, it could be one
   * of string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @var array[]
   */
  public $parameters;
  /**
   * Final text input which was matched during MatchIntent. This value can be
   * different from original input sent in request because of spelling
   * correction or other processing.
   *
   * @var string
   */
  public $resolvedInput;

  /**
   * The confidence of this match. Values range from 0.0 (completely uncertain)
   * to 1.0 (completely certain). This value is for informational purpose only
   * and is only used to help match the best intent within the classification
   * threshold. This value may change for the same end-user expression at any
   * time due to a model retraining or change in implementation.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * The event that matched the query. Filled for `EVENT`, `NO_MATCH` and
   * `NO_INPUT` match types.
   *
   * @param string $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return string
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * The Intent that matched the query. Some, not all fields are filled in this
   * message, including but not limited to: `name` and `display_name`. Only
   * filled for `INTENT` match type.
   *
   * @param GoogleCloudDialogflowCxV3Intent $intent
   */
  public function setIntent(GoogleCloudDialogflowCxV3Intent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Intent
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Type of this Match.
   *
   * Accepted values: MATCH_TYPE_UNSPECIFIED, INTENT, DIRECT_INTENT,
   * PARAMETER_FILLING, NO_MATCH, NO_INPUT, EVENT, KNOWLEDGE_CONNECTOR, PLAYBOOK
   *
   * @param self::MATCH_TYPE_* $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return self::MATCH_TYPE_*
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * The collection of parameters extracted from the query. Depending on your
   * protocol or client library language, this is a map, associative array,
   * symbol table, dictionary, or JSON object composed of a collection of
   * (MapKey, MapValue) pairs: * MapKey type: string * MapKey value: parameter
   * name * MapValue type: If parameter's entity type is a composite entity then
   * use map, otherwise, depending on the parameter value type, it could be one
   * of string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Final text input which was matched during MatchIntent. This value can be
   * different from original input sent in request because of spelling
   * correction or other processing.
   *
   * @param string $resolvedInput
   */
  public function setResolvedInput($resolvedInput)
  {
    $this->resolvedInput = $resolvedInput;
  }
  /**
   * @return string
   */
  public function getResolvedInput()
  {
    return $this->resolvedInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Match::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Match');
