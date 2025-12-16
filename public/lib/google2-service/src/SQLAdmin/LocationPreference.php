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

class LocationPreference extends \Google\Model
{
  /**
   * The App Engine application to follow, it must be in the same region as the
   * Cloud SQL instance. WARNING: Changing this might restart the instance.
   *
   * @deprecated
   * @var string
   */
  public $followGaeApplication;
  /**
   * This is always `sql#locationPreference`.
   *
   * @var string
   */
  public $kind;
  /**
   * The preferred Compute Engine zone for the secondary/failover (for example:
   * us-central1-a, us-central1-b, etc.). To disable this field, set it to
   * 'no_secondary_zone'.
   *
   * @var string
   */
  public $secondaryZone;
  /**
   * The preferred Compute Engine zone (for example: us-central1-a, us-
   * central1-b, etc.). WARNING: Changing this might restart the instance.
   *
   * @var string
   */
  public $zone;

  /**
   * The App Engine application to follow, it must be in the same region as the
   * Cloud SQL instance. WARNING: Changing this might restart the instance.
   *
   * @deprecated
   * @param string $followGaeApplication
   */
  public function setFollowGaeApplication($followGaeApplication)
  {
    $this->followGaeApplication = $followGaeApplication;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getFollowGaeApplication()
  {
    return $this->followGaeApplication;
  }
  /**
   * This is always `sql#locationPreference`.
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
   * The preferred Compute Engine zone for the secondary/failover (for example:
   * us-central1-a, us-central1-b, etc.). To disable this field, set it to
   * 'no_secondary_zone'.
   *
   * @param string $secondaryZone
   */
  public function setSecondaryZone($secondaryZone)
  {
    $this->secondaryZone = $secondaryZone;
  }
  /**
   * @return string
   */
  public function getSecondaryZone()
  {
    return $this->secondaryZone;
  }
  /**
   * The preferred Compute Engine zone (for example: us-central1-a, us-
   * central1-b, etc.). WARNING: Changing this might restart the instance.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationPreference::class, 'Google_Service_SQLAdmin_LocationPreference');
