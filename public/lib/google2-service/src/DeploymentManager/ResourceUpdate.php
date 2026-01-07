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

namespace Google\Service\DeploymentManager;

class ResourceUpdate extends \Google\Collection
{
  /**
   * The resource is scheduled to be created, or if it already exists, acquired.
   */
  public const INTENT_CREATE_OR_ACQUIRE = 'CREATE_OR_ACQUIRE';
  /**
   * The resource is scheduled to be deleted.
   */
  public const INTENT_DELETE = 'DELETE';
  /**
   * The resource is scheduled to be acquired.
   */
  public const INTENT_ACQUIRE = 'ACQUIRE';
  /**
   * The resource is scheduled to be updated via the UPDATE method.
   */
  public const INTENT_UPDATE = 'UPDATE';
  /**
   * The resource is scheduled to be abandoned.
   */
  public const INTENT_ABANDON = 'ABANDON';
  /**
   * The resource is scheduled to be created.
   */
  public const INTENT_CREATE = 'CREATE';
  /**
   * There are changes pending for this resource.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The service is executing changes on the resource.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The service is previewing changes on the resource.
   */
  public const STATE_IN_PREVIEW = 'IN_PREVIEW';
  /**
   * The service has failed to change the resource.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The service has aborted trying to change the resource.
   */
  public const STATE_ABORTED = 'ABORTED';
  protected $collection_key = 'warnings';
  protected $accessControlType = ResourceAccessControl::class;
  protected $accessControlDataType = '';
  protected $errorType = ResourceUpdateError::class;
  protected $errorDataType = '';
  /**
   * Output only. The expanded properties of the resource with reference values
   * expanded. Returned as serialized YAML.
   *
   * @var string
   */
  public $finalProperties;
  /**
   * Output only. The intent of the resource: `PREVIEW`, `UPDATE`, or `CANCEL`.
   *
   * @var string
   */
  public $intent;
  /**
   * Output only. URL of the manifest representing the update configuration of
   * this resource.
   *
   * @var string
   */
  public $manifest;
  /**
   * Output only. The set of updated properties for this resource, before
   * references are expanded. Returned as serialized YAML.
   *
   * @var string
   */
  public $properties;
  /**
   * Output only. The state of the resource.
   *
   * @var string
   */
  public $state;
  protected $warningsType = ResourceUpdateWarnings::class;
  protected $warningsDataType = 'array';

  /**
   * The Access Control Policy to set on this resource after updating the
   * resource itself.
   *
   * @param ResourceAccessControl $accessControl
   */
  public function setAccessControl(ResourceAccessControl $accessControl)
  {
    $this->accessControl = $accessControl;
  }
  /**
   * @return ResourceAccessControl
   */
  public function getAccessControl()
  {
    return $this->accessControl;
  }
  /**
   * Output only. If errors are generated during update of the resource, this
   * field will be populated.
   *
   * @param ResourceUpdateError $error
   */
  public function setError(ResourceUpdateError $error)
  {
    $this->error = $error;
  }
  /**
   * @return ResourceUpdateError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The expanded properties of the resource with reference values
   * expanded. Returned as serialized YAML.
   *
   * @param string $finalProperties
   */
  public function setFinalProperties($finalProperties)
  {
    $this->finalProperties = $finalProperties;
  }
  /**
   * @return string
   */
  public function getFinalProperties()
  {
    return $this->finalProperties;
  }
  /**
   * Output only. The intent of the resource: `PREVIEW`, `UPDATE`, or `CANCEL`.
   *
   * Accepted values: CREATE_OR_ACQUIRE, DELETE, ACQUIRE, UPDATE, ABANDON,
   * CREATE
   *
   * @param self::INTENT_* $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return self::INTENT_*
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Output only. URL of the manifest representing the update configuration of
   * this resource.
   *
   * @param string $manifest
   */
  public function setManifest($manifest)
  {
    $this->manifest = $manifest;
  }
  /**
   * @return string
   */
  public function getManifest()
  {
    return $this->manifest;
  }
  /**
   * Output only. The set of updated properties for this resource, before
   * references are expanded. Returned as serialized YAML.
   *
   * @param string $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. The state of the resource.
   *
   * Accepted values: PENDING, IN_PROGRESS, IN_PREVIEW, FAILED, ABORTED
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
   * Output only. If warning messages are generated during processing of this
   * resource, this field will be populated.
   *
   * @param ResourceUpdateWarnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return ResourceUpdateWarnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceUpdate::class, 'Google_Service_DeploymentManager_ResourceUpdate');
