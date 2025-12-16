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

namespace Google\Service\Monitoring;

class GkeWorkload extends \Google\Model
{
  /**
   * The name of the parent cluster.
   *
   * @var string
   */
  public $clusterName;
  /**
   * The location of the parent cluster. This may be a zone or region.
   *
   * @var string
   */
  public $location;
  /**
   * The name of the parent namespace.
   *
   * @var string
   */
  public $namespaceName;
  /**
   * Output only. The project this resource lives in. For legacy services
   * migrated from the Custom type, this may be a distinct project from the one
   * parenting the service itself.
   *
   * @var string
   */
  public $projectId;
  /**
   * The name of this workload.
   *
   * @var string
   */
  public $topLevelControllerName;
  /**
   * The type of this workload (for example, "Deployment" or "DaemonSet")
   *
   * @var string
   */
  public $topLevelControllerType;

  /**
   * The name of the parent cluster.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * The location of the parent cluster. This may be a zone or region.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The name of the parent namespace.
   *
   * @param string $namespaceName
   */
  public function setNamespaceName($namespaceName)
  {
    $this->namespaceName = $namespaceName;
  }
  /**
   * @return string
   */
  public function getNamespaceName()
  {
    return $this->namespaceName;
  }
  /**
   * Output only. The project this resource lives in. For legacy services
   * migrated from the Custom type, this may be a distinct project from the one
   * parenting the service itself.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The name of this workload.
   *
   * @param string $topLevelControllerName
   */
  public function setTopLevelControllerName($topLevelControllerName)
  {
    $this->topLevelControllerName = $topLevelControllerName;
  }
  /**
   * @return string
   */
  public function getTopLevelControllerName()
  {
    return $this->topLevelControllerName;
  }
  /**
   * The type of this workload (for example, "Deployment" or "DaemonSet")
   *
   * @param string $topLevelControllerType
   */
  public function setTopLevelControllerType($topLevelControllerType)
  {
    $this->topLevelControllerType = $topLevelControllerType;
  }
  /**
   * @return string
   */
  public function getTopLevelControllerType()
  {
    return $this->topLevelControllerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeWorkload::class, 'Google_Service_Monitoring_GkeWorkload');
