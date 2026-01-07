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

class GoogleCloudDialogflowCxV3IntentTrainingPhrase extends \Google\Collection
{
  protected $collection_key = 'parts';
  /**
   * Output only. The unique identifier of the training phrase.
   *
   * @var string
   */
  public $id;
  protected $partsType = GoogleCloudDialogflowCxV3IntentTrainingPhrasePart::class;
  protected $partsDataType = 'array';
  /**
   * Indicates how many times this example was added to the intent.
   *
   * @var int
   */
  public $repeatCount;

  /**
   * Output only. The unique identifier of the training phrase.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
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
   * part of the phrase that you want to annotate, and the `parameter_id` field
   * is set.
   *
   * @param GoogleCloudDialogflowCxV3IntentTrainingPhrasePart[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return GoogleCloudDialogflowCxV3IntentTrainingPhrasePart[]
   */
  public function getParts()
  {
    return $this->parts;
  }
  /**
   * Indicates how many times this example was added to the intent.
   *
   * @param int $repeatCount
   */
  public function setRepeatCount($repeatCount)
  {
    $this->repeatCount = $repeatCount;
  }
  /**
   * @return int
   */
  public function getRepeatCount()
  {
    return $this->repeatCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3IntentTrainingPhrase::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3IntentTrainingPhrase');
