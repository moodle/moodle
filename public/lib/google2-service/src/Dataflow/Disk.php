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

namespace Google\Service\Dataflow;

class Disk extends \Google\Model
{
  /**
   * Disk storage type, as defined by Google Compute Engine. This must be a disk
   * type appropriate to the project and zone in which the workers will run. If
   * unknown or unspecified, the service will attempt to choose a reasonable
   * default. For example, the standard persistent disk type is a resource name
   * typically ending in "pd-standard". If SSD persistent disks are available,
   * the resource name typically ends with "pd-ssd". The actual valid values are
   * defined the Google Compute Engine API, not by the Cloud Dataflow API;
   * consult the Google Compute Engine documentation for more information about
   * determining the set of available disk types for a particular project and
   * zone. Google Compute Engine Disk types are local to a particular project in
   * a particular zone, and so the resource name will typically look something
   * like this: compute.googleapis.com/projects/project-
   * id/zones/zone/diskTypes/pd-standard
   *
   * @var string
   */
  public $diskType;
  /**
   * Directory in a VM where disk is mounted.
   *
   * @var string
   */
  public $mountPoint;
  /**
   * Size of disk in GB. If zero or unspecified, the service will attempt to
   * choose a reasonable default.
   *
   * @var int
   */
  public $sizeGb;

  /**
   * Disk storage type, as defined by Google Compute Engine. This must be a disk
   * type appropriate to the project and zone in which the workers will run. If
   * unknown or unspecified, the service will attempt to choose a reasonable
   * default. For example, the standard persistent disk type is a resource name
   * typically ending in "pd-standard". If SSD persistent disks are available,
   * the resource name typically ends with "pd-ssd". The actual valid values are
   * defined the Google Compute Engine API, not by the Cloud Dataflow API;
   * consult the Google Compute Engine documentation for more information about
   * determining the set of available disk types for a particular project and
   * zone. Google Compute Engine Disk types are local to a particular project in
   * a particular zone, and so the resource name will typically look something
   * like this: compute.googleapis.com/projects/project-
   * id/zones/zone/diskTypes/pd-standard
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Directory in a VM where disk is mounted.
   *
   * @param string $mountPoint
   */
  public function setMountPoint($mountPoint)
  {
    $this->mountPoint = $mountPoint;
  }
  /**
   * @return string
   */
  public function getMountPoint()
  {
    return $this->mountPoint;
  }
  /**
   * Size of disk in GB. If zero or unspecified, the service will attempt to
   * choose a reasonable default.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Disk::class, 'Google_Service_Dataflow_Disk');
