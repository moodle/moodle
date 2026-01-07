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

class GoogleCloudDialogflowV2IntentTrainingPhrasePart extends \Google\Model
{
  /**
   * Optional. The parameter name for the value extracted from the annotated
   * part of the example. This field is required for annotated parts of the
   * training phrase.
   *
   * @var string
   */
  public $alias;
  /**
   * Optional. The entity type name prefixed with `@`. This field is required
   * for annotated parts of the training phrase.
   *
   * @var string
   */
  public $entityType;
  /**
   * Required. The text for this part.
   *
   * @var string
   */
  public $text;
  /**
   * Optional. Indicates whether the text was manually annotated. This field is
   * set to true when the Dialogflow Console is used to manually annotate the
   * part. When creating an annotated part with the API, you must set this to
   * true.
   *
   * @var bool
   */
  public $userDefined;

  /**
   * Optional. The parameter name for the value extracted from the annotated
   * part of the example. This field is required for annotated parts of the
   * training phrase.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }
  /**
   * Optional. The entity type name prefixed with `@`. This field is required
   * for annotated parts of the training phrase.
   *
   * @param string $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return string
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Required. The text for this part.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Optional. Indicates whether the text was manually annotated. This field is
   * set to true when the Dialogflow Console is used to manually annotate the
   * part. When creating an annotated part with the API, you must set this to
   * true.
   *
   * @param bool $userDefined
   */
  public function setUserDefined($userDefined)
  {
    $this->userDefined = $userDefined;
  }
  /**
   * @return bool
   */
  public function getUserDefined()
  {
    return $this->userDefined;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2IntentTrainingPhrasePart::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2IntentTrainingPhrasePart');
