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

namespace Google\Service\ContainerAnalysis;

class InTotoSlsaProvenanceV1 extends \Google\Collection
{
  protected $collection_key = 'subject';
  protected $internal_gapi_mappings = [
        "type" => "_type",
  ];
  /**
   * InToto spec defined at https://github.com/in-
   * toto/attestation/tree/main/spec#statement
   *
   * @var string
   */
  public $type;
  protected $predicateDataType = '';
  /**
   * @var string
   */
  public $predicateType;
  protected $subjectType = Subject::class;
  protected $subjectDataType = 'array';

  /**
   * InToto spec defined at https://github.com/in-
   * toto/attestation/tree/main/spec#statement
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * @param SlsaProvenanceV1 $predicate
   */
  public function setPredicate(SlsaProvenanceV1 $predicate)
  {
    $this->predicate = $predicate;
  }
  /**
   * @return SlsaProvenanceV1
   */
  public function getPredicate()
  {
    return $this->predicate;
  }
  /**
   * @param string $predicateType
   */
  public function setPredicateType($predicateType)
  {
    $this->predicateType = $predicateType;
  }
  /**
   * @return string
   */
  public function getPredicateType()
  {
    return $this->predicateType;
  }
  /**
   * @param Subject[] $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return Subject[]
   */
  public function getSubject()
  {
    return $this->subject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InTotoSlsaProvenanceV1::class, 'Google_Service_ContainerAnalysis_InTotoSlsaProvenanceV1');
