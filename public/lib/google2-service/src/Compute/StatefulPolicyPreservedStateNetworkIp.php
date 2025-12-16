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

class StatefulPolicyPreservedStateNetworkIp extends \Google\Model
{
  public const AUTO_DELETE_NEVER = 'NEVER';
  public const AUTO_DELETE_ON_PERMANENT_INSTANCE_DELETION = 'ON_PERMANENT_INSTANCE_DELETION';
  /**
   * These stateful IPs will never be released during autohealing, update or VM
   * instance recreate operations. This flag is used to configure if the IP
   * reservation should be deleted after it is no longer used by the group, e.g.
   * when the given instance or the whole group is deleted.
   *
   * @var string
   */
  public $autoDelete;

  /**
   * These stateful IPs will never be released during autohealing, update or VM
   * instance recreate operations. This flag is used to configure if the IP
   * reservation should be deleted after it is no longer used by the group, e.g.
   * when the given instance or the whole group is deleted.
   *
   * Accepted values: NEVER, ON_PERMANENT_INSTANCE_DELETION
   *
   * @param self::AUTO_DELETE_* $autoDelete
   */
  public function setAutoDelete($autoDelete)
  {
    $this->autoDelete = $autoDelete;
  }
  /**
   * @return self::AUTO_DELETE_*
   */
  public function getAutoDelete()
  {
    return $this->autoDelete;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatefulPolicyPreservedStateNetworkIp::class, 'Google_Service_Compute_StatefulPolicyPreservedStateNetworkIp');
