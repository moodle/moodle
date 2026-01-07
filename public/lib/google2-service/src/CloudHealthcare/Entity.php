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

namespace Google\Service\CloudHealthcare;

class Entity extends \Google\Collection
{
  protected $collection_key = 'vocabularyCodes';
  /**
   * entity_id is a first class field entity_id uniquely identifies this concept
   * and its meta-vocabulary. For example, "UMLS/C0000970".
   *
   * @var string
   */
  public $entityId;
  /**
   * preferred_term is the preferred term for this concept. For example,
   * "Acetaminophen". For ad hoc entities formed by normalization, this is the
   * most popular unnormalized string.
   *
   * @var string
   */
  public $preferredTerm;
  /**
   * Vocabulary codes are first-class fields and differentiated from the concept
   * unique identifier (entity_id). vocabulary_codes contains the representation
   * of this concept in particular vocabularies, such as ICD-10, SNOMED-CT and
   * RxNORM. These are prefixed by the name of the vocabulary, followed by the
   * unique code within that vocabulary. For example, "RXNORM/A10334543".
   *
   * @var string[]
   */
  public $vocabularyCodes;

  /**
   * entity_id is a first class field entity_id uniquely identifies this concept
   * and its meta-vocabulary. For example, "UMLS/C0000970".
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * preferred_term is the preferred term for this concept. For example,
   * "Acetaminophen". For ad hoc entities formed by normalization, this is the
   * most popular unnormalized string.
   *
   * @param string $preferredTerm
   */
  public function setPreferredTerm($preferredTerm)
  {
    $this->preferredTerm = $preferredTerm;
  }
  /**
   * @return string
   */
  public function getPreferredTerm()
  {
    return $this->preferredTerm;
  }
  /**
   * Vocabulary codes are first-class fields and differentiated from the concept
   * unique identifier (entity_id). vocabulary_codes contains the representation
   * of this concept in particular vocabularies, such as ICD-10, SNOMED-CT and
   * RxNORM. These are prefixed by the name of the vocabulary, followed by the
   * unique code within that vocabulary. For example, "RXNORM/A10334543".
   *
   * @param string[] $vocabularyCodes
   */
  public function setVocabularyCodes($vocabularyCodes)
  {
    $this->vocabularyCodes = $vocabularyCodes;
  }
  /**
   * @return string[]
   */
  public function getVocabularyCodes()
  {
    return $this->vocabularyCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entity::class, 'Google_Service_CloudHealthcare_Entity');
