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

namespace Google\Service\Apigateway;

class ApigatewayGateway extends \Google\Model
{
  /**
   * Gateway does not have a state yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Gateway is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Gateway is running and ready for requests.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Gateway creation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Gateway is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Gateway is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Required. Resource name of the API Config for this Gateway. Format:
   * projects/{project}/locations/global/apis/{api}/configs/{apiConfig}
   *
   * @var string
   */
  public $apiConfig;
  /**
   * Output only. Created time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The default API Gateway host name of the form
   * `{gateway_id}-{hash}.{region_code}.gateway.dev`.
   *
   * @var string
   */
  public $defaultHostname;
  /**
   * Optional. Display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Resource name of the Gateway. Format:
   * projects/{project}/locations/{location}/gateways/{gateway}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the Gateway.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Updated time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Resource name of the API Config for this Gateway. Format:
   * projects/{project}/locations/global/apis/{api}/configs/{apiConfig}
   *
   * @param string $apiConfig
   */
  public function setApiConfig($apiConfig)
  {
    $this->apiConfig = $apiConfig;
  }
  /**
   * @return string
   */
  public function getApiConfig()
  {
    return $this->apiConfig;
  }
  /**
   * Output only. Created time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The default API Gateway host name of the form
   * `{gateway_id}-{hash}.{region_code}.gateway.dev`.
   *
   * @param string $defaultHostname
   */
  public function setDefaultHostname($defaultHostname)
  {
    $this->defaultHostname = $defaultHostname;
  }
  /**
   * @return string
   */
  public function getDefaultHostname()
  {
    return $this->defaultHostname;
  }
  /**
   * Optional. Display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Resource labels to represent user-provided metadata. Refer to
   * cloud documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Resource name of the Gateway. Format:
   * projects/{project}/locations/{location}/gateways/{gateway}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The current state of the Gateway.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, DELETING,
   * UPDATING
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
   * Output only. Updated time.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApigatewayGateway::class, 'Google_Service_Apigateway_ApigatewayGateway');
