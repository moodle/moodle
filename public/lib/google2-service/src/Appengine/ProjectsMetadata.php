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

namespace Google\Service\Appengine;

class ProjectsMetadata extends \Google\Collection
{
  /**
   * A container should never be in an unknown state. Receipt of a container
   * with this state is an error.
   */
  public const CONSUMER_PROJECT_STATE_UNKNOWN_STATE = 'UNKNOWN_STATE';
  /**
   * CCFE considers the container to be serving or transitioning into serving.
   */
  public const CONSUMER_PROJECT_STATE_ON = 'ON';
  /**
   * CCFE considers the container to be in an OFF state. This could occur due to
   * various factors. The state could be triggered by Google-internal audits
   * (ex. abuse suspension, billing closed) or cleanups trigged by compliance
   * systems (ex. data governance hide). User-initiated events such as service
   * management deactivation trigger a container to an OFF state.CLHs might
   * choose to do nothing in this case or to turn off costly resources. CLHs
   * need to consider the customer experience if an ON/OFF/ON sequence of state
   * transitions occurs vs. the cost of deleting resources, keeping metadata
   * about resources, or even keeping resources live for a period of time.CCFE
   * will not send any new customer requests to the CLH when the container is in
   * an OFF state. However, CCFE will allow all previous customer requests
   * relayed to CLH to complete.
   */
  public const CONSUMER_PROJECT_STATE_OFF = 'OFF';
  /**
   * This state indicates that the container has been (or is being) completely
   * removed. This is often due to a data governance purge request and therefore
   * resources should be deleted when this state is reached.
   */
  public const CONSUMER_PROJECT_STATE_DELETED = 'DELETED';
  protected $collection_key = 'gceTag';
  /**
   * The consumer project id.
   *
   * @var string
   */
  public $consumerProjectId;
  /**
   * The consumer project number.
   *
   * @var string
   */
  public $consumerProjectNumber;
  /**
   * The CCFE state of the consumer project. It is the same state that is
   * communicated to the CLH during project events. Notice that this field is
   * not set in the DB, it is only set in this proto when communicated to CLH in
   * the side channel.
   *
   * @var string
   */
  public $consumerProjectState;
  protected $gceTagType = GceTag::class;
  protected $gceTagDataType = 'array';
  /**
   * The service account authorized to operate on the consumer project. Note:
   * CCFE only propagates P4SA with default tag to CLH.
   *
   * @var string
   */
  public $p4ServiceAccount;
  /**
   * The producer project id.
   *
   * @var string
   */
  public $producerProjectId;
  /**
   * The producer project number.
   *
   * @var string
   */
  public $producerProjectNumber;
  /**
   * The tenant project id.
   *
   * @var string
   */
  public $tenantProjectId;
  /**
   * The tenant project number.
   *
   * @var string
   */
  public $tenantProjectNumber;

  /**
   * The consumer project id.
   *
   * @param string $consumerProjectId
   */
  public function setConsumerProjectId($consumerProjectId)
  {
    $this->consumerProjectId = $consumerProjectId;
  }
  /**
   * @return string
   */
  public function getConsumerProjectId()
  {
    return $this->consumerProjectId;
  }
  /**
   * The consumer project number.
   *
   * @param string $consumerProjectNumber
   */
  public function setConsumerProjectNumber($consumerProjectNumber)
  {
    $this->consumerProjectNumber = $consumerProjectNumber;
  }
  /**
   * @return string
   */
  public function getConsumerProjectNumber()
  {
    return $this->consumerProjectNumber;
  }
  /**
   * The CCFE state of the consumer project. It is the same state that is
   * communicated to the CLH during project events. Notice that this field is
   * not set in the DB, it is only set in this proto when communicated to CLH in
   * the side channel.
   *
   * Accepted values: UNKNOWN_STATE, ON, OFF, DELETED
   *
   * @param self::CONSUMER_PROJECT_STATE_* $consumerProjectState
   */
  public function setConsumerProjectState($consumerProjectState)
  {
    $this->consumerProjectState = $consumerProjectState;
  }
  /**
   * @return self::CONSUMER_PROJECT_STATE_*
   */
  public function getConsumerProjectState()
  {
    return $this->consumerProjectState;
  }
  /**
   * The GCE tags associated with the consumer project and those inherited due
   * to their ancestry, if any. Not supported by CCFE.
   *
   * @param GceTag[] $gceTag
   */
  public function setGceTag($gceTag)
  {
    $this->gceTag = $gceTag;
  }
  /**
   * @return GceTag[]
   */
  public function getGceTag()
  {
    return $this->gceTag;
  }
  /**
   * The service account authorized to operate on the consumer project. Note:
   * CCFE only propagates P4SA with default tag to CLH.
   *
   * @param string $p4ServiceAccount
   */
  public function setP4ServiceAccount($p4ServiceAccount)
  {
    $this->p4ServiceAccount = $p4ServiceAccount;
  }
  /**
   * @return string
   */
  public function getP4ServiceAccount()
  {
    return $this->p4ServiceAccount;
  }
  /**
   * The producer project id.
   *
   * @param string $producerProjectId
   */
  public function setProducerProjectId($producerProjectId)
  {
    $this->producerProjectId = $producerProjectId;
  }
  /**
   * @return string
   */
  public function getProducerProjectId()
  {
    return $this->producerProjectId;
  }
  /**
   * The producer project number.
   *
   * @param string $producerProjectNumber
   */
  public function setProducerProjectNumber($producerProjectNumber)
  {
    $this->producerProjectNumber = $producerProjectNumber;
  }
  /**
   * @return string
   */
  public function getProducerProjectNumber()
  {
    return $this->producerProjectNumber;
  }
  /**
   * The tenant project id.
   *
   * @param string $tenantProjectId
   */
  public function setTenantProjectId($tenantProjectId)
  {
    $this->tenantProjectId = $tenantProjectId;
  }
  /**
   * @return string
   */
  public function getTenantProjectId()
  {
    return $this->tenantProjectId;
  }
  /**
   * The tenant project number.
   *
   * @param string $tenantProjectNumber
   */
  public function setTenantProjectNumber($tenantProjectNumber)
  {
    $this->tenantProjectNumber = $tenantProjectNumber;
  }
  /**
   * @return string
   */
  public function getTenantProjectNumber()
  {
    return $this->tenantProjectNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsMetadata::class, 'Google_Service_Appengine_ProjectsMetadata');
