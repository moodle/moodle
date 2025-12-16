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

class GoogleCloudDialogflowV2beta1IntentTrainingPhrase extends \Google\Collection
{
  /**
   * Not specified. This value should never be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Examples do not contain @-prefixed entity type names, but example parts can
   * be annotated with entity types.
   */
  public const TYPE_EXAMPLE = 'EXAMPLE';
  /**
   * Templates are not annotated with entity types, but they can contain
   * @-prefixed entity type names as substrings. Note: Template mode has been
   * deprecated. Example mode is the only supported way to create new training
   * phrases. If you have existing training phrases in template mode, they will
   * be removed during training and it can cause a drop in agent performance.
   *
   * @deprecated
   */
  public const TYPE_TEMPLATE = 'TEMPLATE';
  protected $collection_key = 'parts';
  /**
   * Output only. The unique identifier of this training phrase.
   *
   * @var string
   */
  public $name;
  protected $partsType = GoogleCloudDialogflowV2beta1IntentTrainingPhrasePart::class;
  protected $partsDataType = 'array';
  /**
   * Optional. Indicates how many times this example was added to the intent.
   * Each time a developer adds an existing sample by editing an intent or
   * training, this counter is increased.
   *
   * @var int
   */
  public $timesAddedCount;
  /**
   * Required. The type of the training phrase.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The unique identifier of this training phrase.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The ordered list of training phrase parts. The parts are
   * concatenated in order to form the training phrase. Note: The API does not
   * automatically annotate training phrases like the Dialogflow Console does.
   * Note: Do not forget to include whitespace at part boundaries, so the
   * training phrase is well formatted when the parts are concatenated. If the
   * training phrase does not need to be annotated with parameters, you just
   * need a single part with only the Part.text field set. If you want to
   * annotate the training phrase, you must create multiple parts, where the
   * fields of each part are populated in one of two ways: - `Part.text` is set
   * to a part of the phrase that has no parameters. - `Part.text` is set to a
   * part of the phrase that you want to annotate, and the `entity_type`,
   * `alias`, and `user_defined` fields are all set.
   *
   * @param GoogleCloudDialogflowV2beta1IntentTrainingPhrasePart[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentTrainingPhrasePart[]
   */
  public function getParts()
  {
    return $this->parts;
  }
  /**
   * Optional. Indicates how many times this example was added to the intent.
   * Each time a developer adds an existing sample by editing an intent or
   * training, this counter is increased.
   *
   * @param int $timesAddedCount
   */
  public function setTimesAddedCount($timesAddedCount)
  {
    $this->timesAddedCount = $timesAddedCount;
  }
  /**
   * @return int
   */
  public function getTimesAddedCount()
  {
    return $this->timesAddedCount;
  }
  /**
   * Required. The type of the training phrase.
   *
   * Accepted values: TYPE_UNSPECIFIED, EXAMPLE, TEMPLATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentTrainingPhrase::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentTrainingPhrase');
