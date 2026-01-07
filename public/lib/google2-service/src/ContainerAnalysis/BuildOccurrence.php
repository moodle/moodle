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

class BuildOccurrence extends \Google\Model
{
  protected $inTotoSlsaProvenanceV1Type = InTotoSlsaProvenanceV1::class;
  protected $inTotoSlsaProvenanceV1DataType = '';
  protected $intotoProvenanceType = InTotoProvenance::class;
  protected $intotoProvenanceDataType = '';
  protected $intotoStatementType = InTotoStatement::class;
  protected $intotoStatementDataType = '';
  protected $provenanceType = BuildProvenance::class;
  protected $provenanceDataType = '';
  /**
   * Serialized JSON representation of the provenance, used in generating the
   * build signature in the corresponding build note. After verifying the
   * signature, `provenance_bytes` can be unmarshalled and compared to the
   * provenance to confirm that it is unchanged. A base64-encoded string
   * representation of the provenance bytes is used for the signature in order
   * to interoperate with openssl which expects this format for signature
   * verification. The serialized form is captured both to avoid ambiguity in
   * how the provenance is marshalled to json as well to prevent
   * incompatibilities with future changes.
   *
   * @var string
   */
  public $provenanceBytes;

  /**
   * In-Toto Slsa Provenance V1 represents a slsa provenance meeting the slsa
   * spec, wrapped in an in-toto statement. This allows for direct jsonification
   * of a to-spec in-toto slsa statement with a to-spec slsa provenance.
   *
   * @param InTotoSlsaProvenanceV1 $inTotoSlsaProvenanceV1
   */
  public function setInTotoSlsaProvenanceV1(InTotoSlsaProvenanceV1 $inTotoSlsaProvenanceV1)
  {
    $this->inTotoSlsaProvenanceV1 = $inTotoSlsaProvenanceV1;
  }
  /**
   * @return InTotoSlsaProvenanceV1
   */
  public function getInTotoSlsaProvenanceV1()
  {
    return $this->inTotoSlsaProvenanceV1;
  }
  /**
   * Deprecated. See InTotoStatement for the replacement. In-toto Provenance
   * representation as defined in spec.
   *
   * @param InTotoProvenance $intotoProvenance
   */
  public function setIntotoProvenance(InTotoProvenance $intotoProvenance)
  {
    $this->intotoProvenance = $intotoProvenance;
  }
  /**
   * @return InTotoProvenance
   */
  public function getIntotoProvenance()
  {
    return $this->intotoProvenance;
  }
  /**
   * In-toto Statement representation as defined in spec. The intoto_statement
   * can contain any type of provenance. The serialized payload of the statement
   * can be stored and signed in the Occurrence's envelope.
   *
   * @param InTotoStatement $intotoStatement
   */
  public function setIntotoStatement(InTotoStatement $intotoStatement)
  {
    $this->intotoStatement = $intotoStatement;
  }
  /**
   * @return InTotoStatement
   */
  public function getIntotoStatement()
  {
    return $this->intotoStatement;
  }
  /**
   * The actual provenance for the build.
   *
   * @param BuildProvenance $provenance
   */
  public function setProvenance(BuildProvenance $provenance)
  {
    $this->provenance = $provenance;
  }
  /**
   * @return BuildProvenance
   */
  public function getProvenance()
  {
    return $this->provenance;
  }
  /**
   * Serialized JSON representation of the provenance, used in generating the
   * build signature in the corresponding build note. After verifying the
   * signature, `provenance_bytes` can be unmarshalled and compared to the
   * provenance to confirm that it is unchanged. A base64-encoded string
   * representation of the provenance bytes is used for the signature in order
   * to interoperate with openssl which expects this format for signature
   * verification. The serialized form is captured both to avoid ambiguity in
   * how the provenance is marshalled to json as well to prevent
   * incompatibilities with future changes.
   *
   * @param string $provenanceBytes
   */
  public function setProvenanceBytes($provenanceBytes)
  {
    $this->provenanceBytes = $provenanceBytes;
  }
  /**
   * @return string
   */
  public function getProvenanceBytes()
  {
    return $this->provenanceBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildOccurrence::class, 'Google_Service_ContainerAnalysis_BuildOccurrence');
