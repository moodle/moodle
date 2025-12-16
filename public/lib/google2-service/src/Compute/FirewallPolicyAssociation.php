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

namespace Google\Service\Compute;

class FirewallPolicyAssociation extends \Google\Model
{
  /**
   * The target that the firewall policy is attached to.
   *
   * @var string
   */
  public $attachmentTarget;
  /**
   * [Output Only] Deprecated, please use short name instead. The display name
   * of the firewall policy of the association.
   *
   * @deprecated
   * @var string
   */
  public $displayName;
  /**
   * Output only. [Output Only] The firewall policy ID of the association.
   *
   * @var string
   */
  public $firewallPolicyId;
  /**
   * The name for an association.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The short name of the firewall policy of the
   * association.
   *
   * @var string
   */
  public $shortName;

  /**
   * The target that the firewall policy is attached to.
   *
   * @param string $attachmentTarget
   */
  public function setAttachmentTarget($attachmentTarget)
  {
    $this->attachmentTarget = $attachmentTarget;
  }
  /**
   * @return string
   */
  public function getAttachmentTarget()
  {
    return $this->attachmentTarget;
  }
  /**
   * [Output Only] Deprecated, please use short name instead. The display name
   * of the firewall policy of the association.
   *
   * @deprecated
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. [Output Only] The firewall policy ID of the association.
   *
   * @param string $firewallPolicyId
   */
  public function setFirewallPolicyId($firewallPolicyId)
  {
    $this->firewallPolicyId = $firewallPolicyId;
  }
  /**
   * @return string
   */
  public function getFirewallPolicyId()
  {
    return $this->firewallPolicyId;
  }
  /**
   * The name for an association.
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
   * Output only. [Output Only] The short name of the firewall policy of the
   * association.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyAssociation::class, 'Google_Service_Compute_FirewallPolicyAssociation');
