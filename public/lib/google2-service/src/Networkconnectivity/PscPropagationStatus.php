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

namespace Google\Service\Networkconnectivity;

class PscPropagationStatus extends \Google\Model
{
  /**
   * The code is unspecified.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The propagated Private Service Connect connection is ready.
   */
  public const CODE_READY = 'READY';
  /**
   * The Private Service Connect connection is propagating. This is a transient
   * state.
   */
  public const CODE_PROPAGATING = 'PROPAGATING';
  /**
   * The Private Service Connect connection propagation failed because the VPC
   * network or the project of the target spoke has exceeded the connection
   * limit set by the producer.
   */
  public const CODE_ERROR_PRODUCER_PROPAGATED_CONNECTION_LIMIT_EXCEEDED = 'ERROR_PRODUCER_PROPAGATED_CONNECTION_LIMIT_EXCEEDED';
  /**
   * The Private Service Connect connection propagation failed because the NAT
   * IP subnet space has been exhausted. It is equivalent to the `Needs
   * attention` status of the Private Service Connect connection. See
   * https://cloud.google.com/vpc/docs/about-accessing-vpc-hosted-services-
   * endpoints#connection-statuses.
   */
  public const CODE_ERROR_PRODUCER_NAT_IP_SPACE_EXHAUSTED = 'ERROR_PRODUCER_NAT_IP_SPACE_EXHAUSTED';
  /**
   * The Private Service Connect connection propagation failed because the
   * `PSC_ILB_CONSUMER_FORWARDING_RULES_PER_PRODUCER_NETWORK` quota in the
   * producer VPC network has been exceeded.
   */
  public const CODE_ERROR_PRODUCER_QUOTA_EXCEEDED = 'ERROR_PRODUCER_QUOTA_EXCEEDED';
  /**
   * The Private Service Connect connection propagation failed because the
   * `PSC_PROPAGATED_CONNECTIONS_PER_VPC_NETWORK` quota in the consumer VPC
   * network has been exceeded.
   */
  public const CODE_ERROR_CONSUMER_QUOTA_EXCEEDED = 'ERROR_CONSUMER_QUOTA_EXCEEDED';
  /**
   * The propagation status.
   *
   * @var string
   */
  public $code;
  /**
   * The human-readable summary of the Private Service Connect connection
   * propagation status.
   *
   * @var string
   */
  public $message;
  /**
   * The name of the forwarding rule exported to the hub.
   *
   * @var string
   */
  public $sourceForwardingRule;
  /**
   * The name of the group that the source spoke belongs to.
   *
   * @var string
   */
  public $sourceGroup;
  /**
   * The name of the spoke that the source forwarding rule belongs to.
   *
   * @var string
   */
  public $sourceSpoke;
  /**
   * The name of the group that the target spoke belongs to.
   *
   * @var string
   */
  public $targetGroup;
  /**
   * The name of the spoke that the source forwarding rule propagates to.
   *
   * @var string
   */
  public $targetSpoke;

  /**
   * The propagation status.
   *
   * Accepted values: CODE_UNSPECIFIED, READY, PROPAGATING,
   * ERROR_PRODUCER_PROPAGATED_CONNECTION_LIMIT_EXCEEDED,
   * ERROR_PRODUCER_NAT_IP_SPACE_EXHAUSTED, ERROR_PRODUCER_QUOTA_EXCEEDED,
   * ERROR_CONSUMER_QUOTA_EXCEEDED
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The human-readable summary of the Private Service Connect connection
   * propagation status.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The name of the forwarding rule exported to the hub.
   *
   * @param string $sourceForwardingRule
   */
  public function setSourceForwardingRule($sourceForwardingRule)
  {
    $this->sourceForwardingRule = $sourceForwardingRule;
  }
  /**
   * @return string
   */
  public function getSourceForwardingRule()
  {
    return $this->sourceForwardingRule;
  }
  /**
   * The name of the group that the source spoke belongs to.
   *
   * @param string $sourceGroup
   */
  public function setSourceGroup($sourceGroup)
  {
    $this->sourceGroup = $sourceGroup;
  }
  /**
   * @return string
   */
  public function getSourceGroup()
  {
    return $this->sourceGroup;
  }
  /**
   * The name of the spoke that the source forwarding rule belongs to.
   *
   * @param string $sourceSpoke
   */
  public function setSourceSpoke($sourceSpoke)
  {
    $this->sourceSpoke = $sourceSpoke;
  }
  /**
   * @return string
   */
  public function getSourceSpoke()
  {
    return $this->sourceSpoke;
  }
  /**
   * The name of the group that the target spoke belongs to.
   *
   * @param string $targetGroup
   */
  public function setTargetGroup($targetGroup)
  {
    $this->targetGroup = $targetGroup;
  }
  /**
   * @return string
   */
  public function getTargetGroup()
  {
    return $this->targetGroup;
  }
  /**
   * The name of the spoke that the source forwarding rule propagates to.
   *
   * @param string $targetSpoke
   */
  public function setTargetSpoke($targetSpoke)
  {
    $this->targetSpoke = $targetSpoke;
  }
  /**
   * @return string
   */
  public function getTargetSpoke()
  {
    return $this->targetSpoke;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscPropagationStatus::class, 'Google_Service_Networkconnectivity_PscPropagationStatus');
