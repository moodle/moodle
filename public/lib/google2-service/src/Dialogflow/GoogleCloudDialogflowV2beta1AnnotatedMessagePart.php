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

class GoogleCloudDialogflowV2beta1AnnotatedMessagePart extends \Google\Model
{
  /**
   * Optional. The [Dialogflow system entity
   * type](https://cloud.google.com/dialogflow/docs/reference/system-entities)
   * of this message part. If this is empty, Dialogflow could not annotate the
   * phrase part with a system entity.
   *
   * @var string
   */
  public $entityType;
  /**
   * Optional. The [Dialogflow system entity formatted value
   * ](https://cloud.google.com/dialogflow/docs/reference/system-entities) of
   * this message part. For example for a system entity of type `@sys.unit-
   * currency`, this may contain: { "amount": 5, "currency": "USD" }
   *
   * @var array
   */
  public $formattedValue;
  /**
   * Required. A part of a message possibly annotated with an entity.
   *
   * @var string
   */
  public $text;

  /**
   * Optional. The [Dialogflow system entity
   * type](https://cloud.google.com/dialogflow/docs/reference/system-entities)
   * of this message part. If this is empty, Dialogflow could not annotate the
   * phrase part with a system entity.
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
   * Optional. The [Dialogflow system entity formatted value
   * ](https://cloud.google.com/dialogflow/docs/reference/system-entities) of
   * this message part. For example for a system entity of type `@sys.unit-
   * currency`, this may contain: { "amount": 5, "currency": "USD" }
   *
   * @param array $formattedValue
   */
  public function setFormattedValue($formattedValue)
  {
    $this->formattedValue = $formattedValue;
  }
  /**
   * @return array
   */
  public function getFormattedValue()
  {
    return $this->formattedValue;
  }
  /**
   * Required. A part of a message possibly annotated with an entity.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1AnnotatedMessagePart::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1AnnotatedMessagePart');
