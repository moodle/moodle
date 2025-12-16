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

namespace Google\Service\CloudBuild;

class SecurityContext extends \Google\Model
{
  /**
   * Optional. AllowPrivilegeEscalation controls whether a process can gain more
   * privileges than its parent process. This bool directly controls if the
   * no_new_privs flag will be set on the container process.
   * AllowPrivilegeEscalation is true always when the container is: 1) run as
   * Privileged 2) has CAP_SYS_ADMIN Note that this field cannot be set when
   * spec.os.name is windows. +optional
   *
   * @var bool
   */
  public $allowPrivilegeEscalation;
  /**
   * Run container in privileged mode.
   *
   * @var bool
   */
  public $privileged;
  /**
   * Optional. The GID to run the entrypoint of the container process. Uses
   * runtime default if unset. May also be set in PodSecurityContext. If set in
   * both SecurityContext and PodSecurityContext, the value specified in
   * SecurityContext takes precedence. Note that this field cannot be set when
   * spec.os.name is windows. +optional
   *
   * @var string
   */
  public $runAsGroup;
  /**
   * Optional. Indicates that the container must run as a non-root user. If
   * true, the Kubelet will validate the image at runtime to ensure that it does
   * not run as UID 0 (root) and fail to start the container if it does. If
   * unset or false, no such validation will be performed. May also be set in
   * PodSecurityContext. If set in both SecurityContext and PodSecurityContext,
   * the value specified in SecurityContext takes precedence. +optional
   *
   * @var bool
   */
  public $runAsNonRoot;
  /**
   * Optional. The UID to run the entrypoint of the container process. Defaults
   * to user specified in image metadata if unspecified. May also be set in
   * PodSecurityContext. If set in both SecurityContext and PodSecurityContext,
   * the value specified in SecurityContext takes precedence. Note that this
   * field cannot be set when spec.os.name is windows. +optional
   *
   * @var string
   */
  public $runAsUser;

  /**
   * Optional. AllowPrivilegeEscalation controls whether a process can gain more
   * privileges than its parent process. This bool directly controls if the
   * no_new_privs flag will be set on the container process.
   * AllowPrivilegeEscalation is true always when the container is: 1) run as
   * Privileged 2) has CAP_SYS_ADMIN Note that this field cannot be set when
   * spec.os.name is windows. +optional
   *
   * @param bool $allowPrivilegeEscalation
   */
  public function setAllowPrivilegeEscalation($allowPrivilegeEscalation)
  {
    $this->allowPrivilegeEscalation = $allowPrivilegeEscalation;
  }
  /**
   * @return bool
   */
  public function getAllowPrivilegeEscalation()
  {
    return $this->allowPrivilegeEscalation;
  }
  /**
   * Run container in privileged mode.
   *
   * @param bool $privileged
   */
  public function setPrivileged($privileged)
  {
    $this->privileged = $privileged;
  }
  /**
   * @return bool
   */
  public function getPrivileged()
  {
    return $this->privileged;
  }
  /**
   * Optional. The GID to run the entrypoint of the container process. Uses
   * runtime default if unset. May also be set in PodSecurityContext. If set in
   * both SecurityContext and PodSecurityContext, the value specified in
   * SecurityContext takes precedence. Note that this field cannot be set when
   * spec.os.name is windows. +optional
   *
   * @param string $runAsGroup
   */
  public function setRunAsGroup($runAsGroup)
  {
    $this->runAsGroup = $runAsGroup;
  }
  /**
   * @return string
   */
  public function getRunAsGroup()
  {
    return $this->runAsGroup;
  }
  /**
   * Optional. Indicates that the container must run as a non-root user. If
   * true, the Kubelet will validate the image at runtime to ensure that it does
   * not run as UID 0 (root) and fail to start the container if it does. If
   * unset or false, no such validation will be performed. May also be set in
   * PodSecurityContext. If set in both SecurityContext and PodSecurityContext,
   * the value specified in SecurityContext takes precedence. +optional
   *
   * @param bool $runAsNonRoot
   */
  public function setRunAsNonRoot($runAsNonRoot)
  {
    $this->runAsNonRoot = $runAsNonRoot;
  }
  /**
   * @return bool
   */
  public function getRunAsNonRoot()
  {
    return $this->runAsNonRoot;
  }
  /**
   * Optional. The UID to run the entrypoint of the container process. Defaults
   * to user specified in image metadata if unspecified. May also be set in
   * PodSecurityContext. If set in both SecurityContext and PodSecurityContext,
   * the value specified in SecurityContext takes precedence. Note that this
   * field cannot be set when spec.os.name is windows. +optional
   *
   * @param string $runAsUser
   */
  public function setRunAsUser($runAsUser)
  {
    $this->runAsUser = $runAsUser;
  }
  /**
   * @return string
   */
  public function getRunAsUser()
  {
    return $this->runAsUser;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityContext::class, 'Google_Service_CloudBuild_SecurityContext');
