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

namespace Google\Service\ServiceConsumerManagement;

class V1Beta1ServiceIdentity extends \Google\Model
{
  /**
   * The email address of the service identity.
   *
   * @var string
   */
  public $email;
  /**
   * P4 service identity resource name. An example name would be: `services/serv
   * iceconsumermanagement.googleapis.com/projects/123/serviceIdentities/default
   * `
   *
   * @var string
   */
  public $name;
  /**
   * The project-level IAM role defined in the service agent's grant
   * configuration. This is the standard role intended for this service agent.
   * This field is populated regardless of the `skip_role_attach` option in the
   * request. If `skip_role_attach` is true, the caller can use this value to
   * know which role they are responsible for granting.
   *
   * @var string
   */
  public $projectRole;
  /**
   * The P4 service identity configuration tag. This must be defined in
   * activation_grants. If not specified when creating the account, the tag is
   * set to "default".
   *
   * @var string
   */
  public $tag;
  /**
   * The unique and stable id of the service identity.
   *
   * @var string
   */
  public $uniqueId;

  /**
   * The email address of the service identity.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * P4 service identity resource name. An example name would be: `services/serv
   * iceconsumermanagement.googleapis.com/projects/123/serviceIdentities/default
   * `
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
   * The project-level IAM role defined in the service agent's grant
   * configuration. This is the standard role intended for this service agent.
   * This field is populated regardless of the `skip_role_attach` option in the
   * request. If `skip_role_attach` is true, the caller can use this value to
   * know which role they are responsible for granting.
   *
   * @param string $projectRole
   */
  public function setProjectRole($projectRole)
  {
    $this->projectRole = $projectRole;
  }
  /**
   * @return string
   */
  public function getProjectRole()
  {
    return $this->projectRole;
  }
  /**
   * The P4 service identity configuration tag. This must be defined in
   * activation_grants. If not specified when creating the account, the tag is
   * set to "default".
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * The unique and stable id of the service identity.
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }
  /**
   * @return string
   */
  public function getUniqueId()
  {
    return $this->uniqueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1Beta1ServiceIdentity::class, 'Google_Service_ServiceConsumerManagement_V1Beta1ServiceIdentity');
