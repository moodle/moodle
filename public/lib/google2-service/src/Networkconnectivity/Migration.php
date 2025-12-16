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

class Migration extends \Google\Model
{
  /**
   * Immutable. Resource path as an URI of the source resource, for example a
   * subnet. The project for the source resource should match the project for
   * the InternalRange. An example:
   * /projects/{project}/regions/{region}/subnetworks/{subnet}
   *
   * @var string
   */
  public $source;
  /**
   * Immutable. Resource path of the target resource. The target project can be
   * different, as in the cases when migrating to peer networks. For example:
   * /projects/{project}/regions/{region}/subnetworks/{subnet}
   *
   * @var string
   */
  public $target;

  /**
   * Immutable. Resource path as an URI of the source resource, for example a
   * subnet. The project for the source resource should match the project for
   * the InternalRange. An example:
   * /projects/{project}/regions/{region}/subnetworks/{subnet}
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Immutable. Resource path of the target resource. The target project can be
   * different, as in the cases when migrating to peer networks. For example:
   * /projects/{project}/regions/{region}/subnetworks/{subnet}
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Migration::class, 'Google_Service_Networkconnectivity_Migration');
