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

class AnalyzeEntitiesResponse extends \Google\Collection
{
  protected $collection_key = 'relationships';
  protected $entitiesType = Entity::class;
  protected $entitiesDataType = 'array';
  protected $entityMentionsType = EntityMention::class;
  protected $entityMentionsDataType = 'array';
  /**
   * The FHIR bundle ([`R4`](http://hl7.org/fhir/R4/bundle.html)) that includes
   * all the entities, the entity mentions, and the relationships in JSON
   * format.
   *
   * @var string
   */
  public $fhirBundle;
  protected $relationshipsType = EntityMentionRelationship::class;
  protected $relationshipsDataType = 'array';

  /**
   * The union of all the candidate entities that the entity_mentions in this
   * response could link to. These are UMLS concepts or normalized mention
   * content.
   *
   * @param Entity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return Entity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * The `entity_mentions` field contains all the annotated medical entities
   * that were mentioned in the provided document.
   *
   * @param EntityMention[] $entityMentions
   */
  public function setEntityMentions($entityMentions)
  {
    $this->entityMentions = $entityMentions;
  }
  /**
   * @return EntityMention[]
   */
  public function getEntityMentions()
  {
    return $this->entityMentions;
  }
  /**
   * The FHIR bundle ([`R4`](http://hl7.org/fhir/R4/bundle.html)) that includes
   * all the entities, the entity mentions, and the relationships in JSON
   * format.
   *
   * @param string $fhirBundle
   */
  public function setFhirBundle($fhirBundle)
  {
    $this->fhirBundle = $fhirBundle;
  }
  /**
   * @return string
   */
  public function getFhirBundle()
  {
    return $this->fhirBundle;
  }
  /**
   * relationships contains all the binary relationships that were identified
   * between entity mentions within the provided document.
   *
   * @param EntityMentionRelationship[] $relationships
   */
  public function setRelationships($relationships)
  {
    $this->relationships = $relationships;
  }
  /**
   * @return EntityMentionRelationship[]
   */
  public function getRelationships()
  {
    return $this->relationships;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeEntitiesResponse::class, 'Google_Service_CloudHealthcare_AnalyzeEntitiesResponse');
