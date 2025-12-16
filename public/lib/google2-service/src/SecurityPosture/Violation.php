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

namespace Google\Service\SecurityPosture;

class Violation extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Critical severity.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * High severity.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Medium severity.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Low severity.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * The full resource name of the asset that caused the violation. For details
   * about the format of the full resource name for each asset type, see
   * [Resource name format](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format).
   *
   * @var string
   */
  public $assetId;
  /**
   * A description of the steps that you can take to fix the violation.
   *
   * @var string
   */
  public $nextSteps;
  /**
   * The policy that was violated.
   *
   * @var string
   */
  public $policyId;
  /**
   * The severity of the violation.
   *
   * @var string
   */
  public $severity;
  protected $violatedAssetType = AssetDetails::class;
  protected $violatedAssetDataType = '';
  protected $violatedPolicyType = PolicyDetails::class;
  protected $violatedPolicyDataType = '';
  protected $violatedPostureType = PostureDetails::class;
  protected $violatedPostureDataType = '';

  /**
   * The full resource name of the asset that caused the violation. For details
   * about the format of the full resource name for each asset type, see
   * [Resource name format](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format).
   *
   * @param string $assetId
   */
  public function setAssetId($assetId)
  {
    $this->assetId = $assetId;
  }
  /**
   * @return string
   */
  public function getAssetId()
  {
    return $this->assetId;
  }
  /**
   * A description of the steps that you can take to fix the violation.
   *
   * @param string $nextSteps
   */
  public function setNextSteps($nextSteps)
  {
    $this->nextSteps = $nextSteps;
  }
  /**
   * @return string
   */
  public function getNextSteps()
  {
    return $this->nextSteps;
  }
  /**
   * The policy that was violated.
   *
   * @param string $policyId
   */
  public function setPolicyId($policyId)
  {
    $this->policyId = $policyId;
  }
  /**
   * @return string
   */
  public function getPolicyId()
  {
    return $this->policyId;
  }
  /**
   * The severity of the violation.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, CRITICAL, HIGH, MEDIUM, LOW
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Details of the Cloud Asset Inventory asset that caused the violation.
   *
   * @param AssetDetails $violatedAsset
   */
  public function setViolatedAsset(AssetDetails $violatedAsset)
  {
    $this->violatedAsset = $violatedAsset;
  }
  /**
   * @return AssetDetails
   */
  public function getViolatedAsset()
  {
    return $this->violatedAsset;
  }
  /**
   * Details of the policy that was violated.
   *
   * @param PolicyDetails $violatedPolicy
   */
  public function setViolatedPolicy(PolicyDetails $violatedPolicy)
  {
    $this->violatedPolicy = $violatedPolicy;
  }
  /**
   * @return PolicyDetails
   */
  public function getViolatedPolicy()
  {
    return $this->violatedPolicy;
  }
  /**
   * Details for the posture that was violated. This field is present only if
   * the violated policy belongs to a deployed posture.
   *
   * @param PostureDetails $violatedPosture
   */
  public function setViolatedPosture(PostureDetails $violatedPosture)
  {
    $this->violatedPosture = $violatedPosture;
  }
  /**
   * @return PostureDetails
   */
  public function getViolatedPosture()
  {
    return $this->violatedPosture;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Violation::class, 'Google_Service_SecurityPosture_Violation');
