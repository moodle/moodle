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

class Remediation extends \Google\Model
{
  /**
   * No remediation type specified.
   */
  public const REMEDIATION_TYPE_REMEDIATION_TYPE_UNSPECIFIED = 'REMEDIATION_TYPE_UNSPECIFIED';
  /**
   * A MITIGATION is available.
   */
  public const REMEDIATION_TYPE_MITIGATION = 'MITIGATION';
  /**
   * No fix is planned.
   */
  public const REMEDIATION_TYPE_NO_FIX_PLANNED = 'NO_FIX_PLANNED';
  /**
   * Not available.
   */
  public const REMEDIATION_TYPE_NONE_AVAILABLE = 'NONE_AVAILABLE';
  /**
   * A vendor fix is available.
   */
  public const REMEDIATION_TYPE_VENDOR_FIX = 'VENDOR_FIX';
  /**
   * A workaround is available.
   */
  public const REMEDIATION_TYPE_WORKAROUND = 'WORKAROUND';
  /**
   * Contains a comprehensive human-readable discussion of the remediation.
   *
   * @var string
   */
  public $details;
  /**
   * The type of remediation that can be applied.
   *
   * @var string
   */
  public $remediationType;
  protected $remediationUriType = RelatedUrl::class;
  protected $remediationUriDataType = '';

  /**
   * Contains a comprehensive human-readable discussion of the remediation.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The type of remediation that can be applied.
   *
   * Accepted values: REMEDIATION_TYPE_UNSPECIFIED, MITIGATION, NO_FIX_PLANNED,
   * NONE_AVAILABLE, VENDOR_FIX, WORKAROUND
   *
   * @param self::REMEDIATION_TYPE_* $remediationType
   */
  public function setRemediationType($remediationType)
  {
    $this->remediationType = $remediationType;
  }
  /**
   * @return self::REMEDIATION_TYPE_*
   */
  public function getRemediationType()
  {
    return $this->remediationType;
  }
  /**
   * Contains the URL where to obtain the remediation.
   *
   * @param RelatedUrl $remediationUri
   */
  public function setRemediationUri(RelatedUrl $remediationUri)
  {
    $this->remediationUri = $remediationUri;
  }
  /**
   * @return RelatedUrl
   */
  public function getRemediationUri()
  {
    return $this->remediationUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Remediation::class, 'Google_Service_OnDemandScanning_Remediation');
