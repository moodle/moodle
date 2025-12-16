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

class DiscoveryNote extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const ANALYSIS_KIND_NOTE_KIND_UNSPECIFIED = 'NOTE_KIND_UNSPECIFIED';
  /**
   * The note and occurrence represent a package vulnerability.
   */
  public const ANALYSIS_KIND_VULNERABILITY = 'VULNERABILITY';
  /**
   * The note and occurrence assert build provenance.
   */
  public const ANALYSIS_KIND_BUILD = 'BUILD';
  /**
   * This represents an image basis relationship.
   */
  public const ANALYSIS_KIND_IMAGE = 'IMAGE';
  /**
   * This represents a package installed via a package manager.
   */
  public const ANALYSIS_KIND_PACKAGE = 'PACKAGE';
  /**
   * The note and occurrence track deployment events.
   */
  public const ANALYSIS_KIND_DEPLOYMENT = 'DEPLOYMENT';
  /**
   * The note and occurrence track the initial discovery status of a resource.
   */
  public const ANALYSIS_KIND_DISCOVERY = 'DISCOVERY';
  /**
   * This represents a logical "role" that can attest to artifacts.
   */
  public const ANALYSIS_KIND_ATTESTATION = 'ATTESTATION';
  /**
   * This represents an available package upgrade.
   */
  public const ANALYSIS_KIND_UPGRADE = 'UPGRADE';
  /**
   * This represents a Compliance Note
   */
  public const ANALYSIS_KIND_COMPLIANCE = 'COMPLIANCE';
  /**
   * This represents a DSSE attestation Note
   */
  public const ANALYSIS_KIND_DSSE_ATTESTATION = 'DSSE_ATTESTATION';
  /**
   * This represents a Vulnerability Assessment.
   */
  public const ANALYSIS_KIND_VULNERABILITY_ASSESSMENT = 'VULNERABILITY_ASSESSMENT';
  /**
   * This represents an SBOM Reference.
   */
  public const ANALYSIS_KIND_SBOM_REFERENCE = 'SBOM_REFERENCE';
  /**
   * This represents a secret.
   */
  public const ANALYSIS_KIND_SECRET = 'SECRET';
  /**
   * Required. Immutable. The kind of analysis that is handled by this
   * discovery.
   *
   * @var string
   */
  public $analysisKind;

  /**
   * Required. Immutable. The kind of analysis that is handled by this
   * discovery.
   *
   * Accepted values: NOTE_KIND_UNSPECIFIED, VULNERABILITY, BUILD, IMAGE,
   * PACKAGE, DEPLOYMENT, DISCOVERY, ATTESTATION, UPGRADE, COMPLIANCE,
   * DSSE_ATTESTATION, VULNERABILITY_ASSESSMENT, SBOM_REFERENCE, SECRET
   *
   * @param self::ANALYSIS_KIND_* $analysisKind
   */
  public function setAnalysisKind($analysisKind)
  {
    $this->analysisKind = $analysisKind;
  }
  /**
   * @return self::ANALYSIS_KIND_*
   */
  public function getAnalysisKind()
  {
    return $this->analysisKind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveryNote::class, 'Google_Service_ContainerAnalysis_DiscoveryNote');
