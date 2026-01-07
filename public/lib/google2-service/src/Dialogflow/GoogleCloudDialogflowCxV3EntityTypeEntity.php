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

class GoogleCloudDialogflowCxV3EntityTypeEntity extends \Google\Collection
{
  protected $collection_key = 'synonyms';
  /**
   * Required. A collection of value synonyms. For example, if the entity type
   * is *vegetable*, and `value` is *scallions*, a synonym could be *green
   * onions*. For `KIND_LIST` entity types: * This collection must contain
   * exactly one synonym equal to `value`.
   *
   * @var string[]
   */
  public $synonyms;
  /**
   * Required. The primary value associated with this entity entry. For example,
   * if the entity type is *vegetable*, the value could be *scallions*. For
   * `KIND_MAP` entity types: * A canonical value to be used in place of
   * synonyms. For `KIND_LIST` entity types: * A string that can contain
   * references to other entity types (with or without aliases).
   *
   * @var string
   */
  public $value;

  /**
   * Required. A collection of value synonyms. For example, if the entity type
   * is *vegetable*, and `value` is *scallions*, a synonym could be *green
   * onions*. For `KIND_LIST` entity types: * This collection must contain
   * exactly one synonym equal to `value`.
   *
   * @param string[] $synonyms
   */
  public function setSynonyms($synonyms)
  {
    $this->synonyms = $synonyms;
  }
  /**
   * @return string[]
   */
  public function getSynonyms()
  {
    return $this->synonyms;
  }
  /**
   * Required. The primary value associated with this entity entry. For example,
   * if the entity type is *vegetable*, the value could be *scallions*. For
   * `KIND_MAP` entity types: * A canonical value to be used in place of
   * synonyms. For `KIND_LIST` entity types: * A string that can contain
   * references to other entity types (with or without aliases).
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3EntityTypeEntity::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3EntityTypeEntity');
