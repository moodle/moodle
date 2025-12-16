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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityAction extends \Google\Collection
{
  /**
   * The default value. This only exists for forward compatibility. A create
   * request with this value will be rejected.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * An ENABLED SecurityAction is actively enforced if the `expiration_time` is
   * in the future.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * A disabled SecurityAction is never enforced.
   */
  public const STATE_DISABLED = 'DISABLED';
  protected $collection_key = 'apiProxies';
  protected $allowType = GoogleCloudApigeeV1SecurityActionAllow::class;
  protected $allowDataType = '';
  /**
   * Optional. If unset, this would apply to all proxies in the environment. If
   * set, this action is enforced only if at least one proxy in the repeated
   * list is deployed at the time of enforcement. If set, several restrictions
   * are enforced on SecurityActions. There can be at most 100 enabled actions
   * with proxies set in an env. Several other restrictions apply on conditions
   * and are detailed later.
   *
   * @var string[]
   */
  public $apiProxies;
  protected $conditionConfigType = GoogleCloudApigeeV1SecurityActionConditionConfig::class;
  protected $conditionConfigDataType = '';
  /**
   * Output only. The create time for this SecurityAction.
   *
   * @var string
   */
  public $createTime;
  protected $denyType = GoogleCloudApigeeV1SecurityActionDeny::class;
  protected $denyDataType = '';
  /**
   * Optional. An optional user provided description of the SecurityAction.
   *
   * @var string
   */
  public $description;
  /**
   * The expiration for this SecurityAction.
   *
   * @var string
   */
  public $expireTime;
  protected $flagType = GoogleCloudApigeeV1SecurityActionFlag::class;
  protected $flagDataType = '';
  /**
   * Immutable. This field is ignored during creation as per AIP-133. Please set
   * the `security_action_id` field in the CreateSecurityActionRequest when
   * creating a new SecurityAction. Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Only an ENABLED SecurityAction is enforced. An ENABLED
   * SecurityAction past its expiration time will not be enforced.
   *
   * @var string
   */
  public $state;
  /**
   * Input only. The TTL for this SecurityAction.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. The update time for this SecurityAction. This reflects when
   * this SecurityAction changed states.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Allow a request through if it matches this SecurityAction.
   *
   * @param GoogleCloudApigeeV1SecurityActionAllow $allow
   */
  public function setAllow(GoogleCloudApigeeV1SecurityActionAllow $allow)
  {
    $this->allow = $allow;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityActionAllow
   */
  public function getAllow()
  {
    return $this->allow;
  }
  /**
   * Optional. If unset, this would apply to all proxies in the environment. If
   * set, this action is enforced only if at least one proxy in the repeated
   * list is deployed at the time of enforcement. If set, several restrictions
   * are enforced on SecurityActions. There can be at most 100 enabled actions
   * with proxies set in an env. Several other restrictions apply on conditions
   * and are detailed later.
   *
   * @param string[] $apiProxies
   */
  public function setApiProxies($apiProxies)
  {
    $this->apiProxies = $apiProxies;
  }
  /**
   * @return string[]
   */
  public function getApiProxies()
  {
    return $this->apiProxies;
  }
  /**
   * Required. A valid SecurityAction must contain at least one condition.
   *
   * @param GoogleCloudApigeeV1SecurityActionConditionConfig $conditionConfig
   */
  public function setConditionConfig(GoogleCloudApigeeV1SecurityActionConditionConfig $conditionConfig)
  {
    $this->conditionConfig = $conditionConfig;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityActionConditionConfig
   */
  public function getConditionConfig()
  {
    return $this->conditionConfig;
  }
  /**
   * Output only. The create time for this SecurityAction.
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
   * Deny a request through if it matches this SecurityAction.
   *
   * @param GoogleCloudApigeeV1SecurityActionDeny $deny
   */
  public function setDeny(GoogleCloudApigeeV1SecurityActionDeny $deny)
  {
    $this->deny = $deny;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityActionDeny
   */
  public function getDeny()
  {
    return $this->deny;
  }
  /**
   * Optional. An optional user provided description of the SecurityAction.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The expiration for this SecurityAction.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Flag a request through if it matches this SecurityAction.
   *
   * @param GoogleCloudApigeeV1SecurityActionFlag $flag
   */
  public function setFlag(GoogleCloudApigeeV1SecurityActionFlag $flag)
  {
    $this->flag = $flag;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityActionFlag
   */
  public function getFlag()
  {
    return $this->flag;
  }
  /**
   * Immutable. This field is ignored during creation as per AIP-133. Please set
   * the `security_action_id` field in the CreateSecurityActionRequest when
   * creating a new SecurityAction. Format:
   * organizations/{org}/environments/{env}/securityActions/{security_action}
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
   * Required. Only an ENABLED SecurityAction is enforced. An ENABLED
   * SecurityAction past its expiration time will not be enforced.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED
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
   * Input only. The TTL for this SecurityAction.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. The update time for this SecurityAction. This reflects when
   * this SecurityAction changed states.
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
class_alias(GoogleCloudApigeeV1SecurityAction::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAction');
