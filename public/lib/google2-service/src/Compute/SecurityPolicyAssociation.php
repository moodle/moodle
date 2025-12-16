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

class SecurityPolicyAssociation extends \Google\Collection
{
  protected $collection_key = 'excludedProjects';
  /**
   * The resource that the security policy is attached to.
   *
   * @var string
   */
  public $attachmentId;
  /**
   * Output only. [Output Only] The display name of the security policy of the
   * association.
   *
   * @deprecated
   * @var string
   */
  public $displayName;
  /**
   * A list of folders to exclude from the security policy.
   *
   * @var string[]
   */
  public $excludedFolders;
  /**
   * A list of projects to exclude from the security policy.
   *
   * @var string[]
   */
  public $excludedProjects;
  /**
   * The name for an association.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The security policy ID of the association.
   *
   * @var string
   */
  public $securityPolicyId;
  /**
   * Output only. [Output Only] The short name of the security policy of the
   * association.
   *
   * @var string
   */
  public $shortName;

  /**
   * The resource that the security policy is attached to.
   *
   * @param string $attachmentId
   */
  public function setAttachmentId($attachmentId)
  {
    $this->attachmentId = $attachmentId;
  }
  /**
   * @return string
   */
  public function getAttachmentId()
  {
    return $this->attachmentId;
  }
  /**
   * Output only. [Output Only] The display name of the security policy of the
   * association.
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
   * A list of folders to exclude from the security policy.
   *
   * @param string[] $excludedFolders
   */
  public function setExcludedFolders($excludedFolders)
  {
    $this->excludedFolders = $excludedFolders;
  }
  /**
   * @return string[]
   */
  public function getExcludedFolders()
  {
    return $this->excludedFolders;
  }
  /**
   * A list of projects to exclude from the security policy.
   *
   * @param string[] $excludedProjects
   */
  public function setExcludedProjects($excludedProjects)
  {
    $this->excludedProjects = $excludedProjects;
  }
  /**
   * @return string[]
   */
  public function getExcludedProjects()
  {
    return $this->excludedProjects;
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
   * Output only. [Output Only] The security policy ID of the association.
   *
   * @param string $securityPolicyId
   */
  public function setSecurityPolicyId($securityPolicyId)
  {
    $this->securityPolicyId = $securityPolicyId;
  }
  /**
   * @return string
   */
  public function getSecurityPolicyId()
  {
    return $this->securityPolicyId;
  }
  /**
   * Output only. [Output Only] The short name of the security policy of the
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
class_alias(SecurityPolicyAssociation::class, 'Google_Service_Compute_SecurityPolicyAssociation');
