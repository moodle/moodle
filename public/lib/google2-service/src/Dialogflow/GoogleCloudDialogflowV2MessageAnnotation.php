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

class GoogleCloudDialogflowV2MessageAnnotation extends \Google\Collection
{
  protected $collection_key = 'parts';
  /**
   * Indicates whether the text message contains entities.
   *
   * @var bool
   */
  public $containEntities;
  protected $partsType = GoogleCloudDialogflowV2AnnotatedMessagePart::class;
  protected $partsDataType = 'array';

  /**
   * Indicates whether the text message contains entities.
   *
   * @param bool $containEntities
   */
  public function setContainEntities($containEntities)
  {
    $this->containEntities = $containEntities;
  }
  /**
   * @return bool
   */
  public function getContainEntities()
  {
    return $this->containEntities;
  }
  /**
   * The collection of annotated message parts ordered by their position in the
   * message. You can recover the annotated message by concatenating
   * [AnnotatedMessagePart.text].
   *
   * @param GoogleCloudDialogflowV2AnnotatedMessagePart[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return GoogleCloudDialogflowV2AnnotatedMessagePart[]
   */
  public function getParts()
  {
    return $this->parts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2MessageAnnotation::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2MessageAnnotation');
