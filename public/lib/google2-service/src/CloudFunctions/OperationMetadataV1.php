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

namespace Google\Service\CloudFunctions;

class OperationMetadataV1 extends \Google\Model
{
  /**
   * Unknown operation type.
   */
  public const TYPE_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  /**
   * Triggered by CreateFunction call
   */
  public const TYPE_CREATE_FUNCTION = 'CREATE_FUNCTION';
  /**
   * Triggered by UpdateFunction call
   */
  public const TYPE_UPDATE_FUNCTION = 'UPDATE_FUNCTION';
  /**
   * Triggered by DeleteFunction call.
   */
  public const TYPE_DELETE_FUNCTION = 'DELETE_FUNCTION';
  /**
   * The Cloud Build ID of the function created or updated by an API call. This
   * field is only populated for Create and Update operations.
   *
   * @var string
   */
  public $buildId;
  /**
   * The Cloud Build Name of the function deployment. This field is only
   * populated for Create and Update operations. `projects//locations//builds/`.
   *
   * @var string
   */
  public $buildName;
  /**
   * The original request that started the operation.
   *
   * @var array[]
   */
  public $request;
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @var string
   */
  public $sourceToken;
  /**
   * Target of the operation - for example
   * `projects/project-1/locations/region-1/functions/function-1`
   *
   * @var string
   */
  public $target;
  /**
   * Type of operation.
   *
   * @var string
   */
  public $type;
  /**
   * The last update timestamp of the operation.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Version id of the function created or updated by an API call. This field is
   * only populated for Create and Update operations.
   *
   * @var string
   */
  public $versionId;

  /**
   * The Cloud Build ID of the function created or updated by an API call. This
   * field is only populated for Create and Update operations.
   *
   * @param string $buildId
   */
  public function setBuildId($buildId)
  {
    $this->buildId = $buildId;
  }
  /**
   * @return string
   */
  public function getBuildId()
  {
    return $this->buildId;
  }
  /**
   * The Cloud Build Name of the function deployment. This field is only
   * populated for Create and Update operations. `projects//locations//builds/`.
   *
   * @param string $buildName
   */
  public function setBuildName($buildName)
  {
    $this->buildName = $buildName;
  }
  /**
   * @return string
   */
  public function getBuildName()
  {
    return $this->buildName;
  }
  /**
   * The original request that started the operation.
   *
   * @param array[] $request
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }
  /**
   * @return array[]
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @param string $sourceToken
   */
  public function setSourceToken($sourceToken)
  {
    $this->sourceToken = $sourceToken;
  }
  /**
   * @return string
   */
  public function getSourceToken()
  {
    return $this->sourceToken;
  }
  /**
   * Target of the operation - for example
   * `projects/project-1/locations/region-1/functions/function-1`
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Type of operation.
   *
   * Accepted values: OPERATION_UNSPECIFIED, CREATE_FUNCTION, UPDATE_FUNCTION,
   * DELETE_FUNCTION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The last update timestamp of the operation.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Version id of the function created or updated by an API call. This field is
   * only populated for Create and Update operations.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadataV1::class, 'Google_Service_CloudFunctions_OperationMetadataV1');
