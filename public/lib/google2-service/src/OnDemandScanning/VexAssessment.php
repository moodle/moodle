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

namespace Google\Service\OnDemandScanning;

class VexAssessment extends \Google\Collection
{
  /**
   * No state is specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * This product is known to be affected by this vulnerability.
   */
  public const STATE_AFFECTED = 'AFFECTED';
  /**
   * This product is known to be not affected by this vulnerability.
   */
  public const STATE_NOT_AFFECTED = 'NOT_AFFECTED';
  /**
   * This product contains a fix for this vulnerability.
   */
  public const STATE_FIXED = 'FIXED';
  /**
   * It is not known yet whether these versions are or are not affected by the
   * vulnerability. However, it is still under investigation.
   */
  public const STATE_UNDER_INVESTIGATION = 'UNDER_INVESTIGATION';
  protected $collection_key = 'remediations';
  /**
   * Holds the MITRE standard Common Vulnerabilities and Exposures (CVE)
   * tracking number for the vulnerability. Deprecated: Use vulnerability_id
   * instead to denote CVEs.
   *
   * @deprecated
   * @var string
   */
  public $cve;
  /**
   * Contains information about the impact of this vulnerability, this will
   * change with time.
   *
   * @var string[]
   */
  public $impacts;
  protected $justificationType = Justification::class;
  protected $justificationDataType = '';
  /**
   * The VulnerabilityAssessment note from which this VexAssessment was
   * generated. This will be of the form:
   * `projects/[PROJECT_ID]/notes/[NOTE_ID]`.
   *
   * @var string
   */
  public $noteName;
  protected $relatedUrisType = RelatedUrl::class;
  protected $relatedUrisDataType = 'array';
  protected $remediationsType = Remediation::class;
  protected $remediationsDataType = 'array';
  /**
   * Provides the state of this Vulnerability assessment.
   *
   * @var string
   */
  public $state;
  /**
   * The vulnerability identifier for this Assessment. Will hold one of common
   * identifiers e.g. CVE, GHSA etc.
   *
   * @var string
   */
  public $vulnerabilityId;

  /**
   * Holds the MITRE standard Common Vulnerabilities and Exposures (CVE)
   * tracking number for the vulnerability. Deprecated: Use vulnerability_id
   * instead to denote CVEs.
   *
   * @deprecated
   * @param string $cve
   */
  public function setCve($cve)
  {
    $this->cve = $cve;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCve()
  {
    return $this->cve;
  }
  /**
   * Contains information about the impact of this vulnerability, this will
   * change with time.
   *
   * @param string[] $impacts
   */
  public function setImpacts($impacts)
  {
    $this->impacts = $impacts;
  }
  /**
   * @return string[]
   */
  public function getImpacts()
  {
    return $this->impacts;
  }
  /**
   * Justification provides the justification when the state of the assessment
   * if NOT_AFFECTED.
   *
   * @param Justification $justification
   */
  public function setJustification(Justification $justification)
  {
    $this->justification = $justification;
  }
  /**
   * @return Justification
   */
  public function getJustification()
  {
    return $this->justification;
  }
  /**
   * The VulnerabilityAssessment note from which this VexAssessment was
   * generated. This will be of the form:
   * `projects/[PROJECT_ID]/notes/[NOTE_ID]`.
   *
   * @param string $noteName
   */
  public function setNoteName($noteName)
  {
    $this->noteName = $noteName;
  }
  /**
   * @return string
   */
  public function getNoteName()
  {
    return $this->noteName;
  }
  /**
   * Holds a list of references associated with this vulnerability item and
   * assessment.
   *
   * @param RelatedUrl[] $relatedUris
   */
  public function setRelatedUris($relatedUris)
  {
    $this->relatedUris = $relatedUris;
  }
  /**
   * @return RelatedUrl[]
   */
  public function getRelatedUris()
  {
    return $this->relatedUris;
  }
  /**
   * Specifies details on how to handle (and presumably, fix) a vulnerability.
   *
   * @param Remediation[] $remediations
   */
  public function setRemediations($remediations)
  {
    $this->remediations = $remediations;
  }
  /**
   * @return Remediation[]
   */
  public function getRemediations()
  {
    return $this->remediations;
  }
  /**
   * Provides the state of this Vulnerability assessment.
   *
   * Accepted values: STATE_UNSPECIFIED, AFFECTED, NOT_AFFECTED, FIXED,
   * UNDER_INVESTIGATION
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The vulnerability identifier for this Assessment. Will hold one of common
   * identifiers e.g. CVE, GHSA etc.
   *
   * @param string $vulnerabilityId
   */
  public function setVulnerabilityId($vulnerabilityId)
  {
    $this->vulnerabilityId = $vulnerabilityId;
  }
  /**
   * @return string
   */
  public function getVulnerabilityId()
  {
    return $this->vulnerabilityId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VexAssessment::class, 'Google_Service_OnDemandScanning_VexAssessment');
