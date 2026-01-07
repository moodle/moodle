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

namespace Google\Service\Dataproc;

class ManagedCluster extends \Google\Model
{
  /**
   * Required. The cluster name prefix. A unique cluster name will be formed by
   * appending a random suffix.The name must contain only lower-case letters
   * (a-z), numbers (0-9), and hyphens (-). Must begin with a letter. Cannot
   * begin or end with hyphen. Must consist of between 2 and 35 characters.
   *
   * @var string
   */
  public $clusterName;
  protected $configType = ClusterConfig::class;
  protected $configDataType = '';
  /**
   * Optional. The labels to associate with this cluster.Label keys must be
   * between 1 and 63 characters long, and must conform to the following PCRE
   * regular expression: \p{Ll}\p{Lo}{0,62}Label values must be between 1 and 63
   * characters long, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}\p{N}_-{0,63}No more than 32 labels can be associated with a
   * given cluster.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Required. The cluster name prefix. A unique cluster name will be formed by
   * appending a random suffix.The name must contain only lower-case letters
   * (a-z), numbers (0-9), and hyphens (-). Must begin with a letter. Cannot
   * begin or end with hyphen. Must consist of between 2 and 35 characters.
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
   * Required. The cluster configuration.
   *
   * @param ClusterConfig $config
   */
  public function setConfig(ClusterConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return ClusterConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Optional. The labels to associate with this cluster.Label keys must be
   * between 1 and 63 characters long, and must conform to the following PCRE
   * regular expression: \p{Ll}\p{Lo}{0,62}Label values must be between 1 and 63
   * characters long, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}\p{N}_-{0,63}No more than 32 labels can be associated with a
   * given cluster.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedCluster::class, 'Google_Service_Dataproc_ManagedCluster');
