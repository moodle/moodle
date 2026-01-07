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

namespace Google\Service\SQLAdmin;

class DemoteContext extends \Google\Model
{
  /**
   * This is always `sql#demoteContext`.
   *
   * @var string
   */
  public $kind;
  /**
   * Required. The name of the instance which acts as the on-premises primary
   * instance in the replication setup.
   *
   * @var string
   */
  public $sourceRepresentativeInstanceName;

  /**
   * This is always `sql#demoteContext`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Required. The name of the instance which acts as the on-premises primary
   * instance in the replication setup.
   *
   * @param string $sourceRepresentativeInstanceName
   */
  public function setSourceRepresentativeInstanceName($sourceRepresentativeInstanceName)
  {
    $this->sourceRepresentativeInstanceName = $sourceRepresentativeInstanceName;
  }
  /**
   * @return string
   */
  public function getSourceRepresentativeInstanceName()
  {
    return $this->sourceRepresentativeInstanceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DemoteContext::class, 'Google_Service_SQLAdmin_DemoteContext');
