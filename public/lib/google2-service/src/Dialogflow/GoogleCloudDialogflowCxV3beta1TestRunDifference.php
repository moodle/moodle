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

class GoogleCloudDialogflowCxV3beta1TestRunDifference extends \Google\Model
{
  /**
   * Should never be used.
   */
  public const TYPE_DIFF_TYPE_UNSPECIFIED = 'DIFF_TYPE_UNSPECIFIED';
  /**
   * The intent.
   */
  public const TYPE_INTENT = 'INTENT';
  /**
   * The page.
   */
  public const TYPE_PAGE = 'PAGE';
  /**
   * The parameters.
   */
  public const TYPE_PARAMETERS = 'PARAMETERS';
  /**
   * The message utterance.
   */
  public const TYPE_UTTERANCE = 'UTTERANCE';
  /**
   * The flow.
   */
  public const TYPE_FLOW = 'FLOW';
  /**
   * A human readable description of the diff, showing the actual output vs
   * expected output.
   *
   * @var string
   */
  public $description;
  /**
   * The type of diff.
   *
   * @var string
   */
  public $type;

  /**
   * A human readable description of the diff, showing the actual output vs
   * expected output.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The type of diff.
   *
   * Accepted values: DIFF_TYPE_UNSPECIFIED, INTENT, PAGE, PARAMETERS,
   * UTTERANCE, FLOW
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
class_alias(GoogleCloudDialogflowCxV3beta1TestRunDifference::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1TestRunDifference');
