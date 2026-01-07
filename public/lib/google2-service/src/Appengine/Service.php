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

namespace Google\Service\Appengine;

class Service extends \Google\Model
{
  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetServiceRequest
   *
   * @var array[]
   */
  public $generatedCustomerMetadata;
  /**
   * Output only. Relative name of the service within the application. Example:
   * default.@OutputOnly
   *
   * @var string
   */
  public $id;
  /**
   * A set of labels to apply to this service. Labels are key/value pairs that
   * describe the service and all resources that belong to it (e.g., versions).
   * The labels can be used to search and group resources, and are propagated to
   * the usage and billing reports, enabling fine-grain analysis of costs. An
   * example of using labels is to tag resources belonging to different
   * environments (e.g., "env=prod", "env=qa"). Label keys and values can be no
   * longer than 63 characters and can only contain lowercase letters, numeric
   * characters, underscores, dashes, and international characters. Label keys
   * must start with a lowercase letter or an international character. Each
   * service can have at most 32 labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Full path to the Service resource in the API. Example:
   * apps/myapp/services/default.@OutputOnly
   *
   * @var string
   */
  public $name;
  protected $networkSettingsType = NetworkSettings::class;
  protected $networkSettingsDataType = '';
  protected $splitType = TrafficSplit::class;
  protected $splitDataType = '';

  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetServiceRequest
   *
   * @param array[] $generatedCustomerMetadata
   */
  public function setGeneratedCustomerMetadata($generatedCustomerMetadata)
  {
    $this->generatedCustomerMetadata = $generatedCustomerMetadata;
  }
  /**
   * @return array[]
   */
  public function getGeneratedCustomerMetadata()
  {
    return $this->generatedCustomerMetadata;
  }
  /**
   * Output only. Relative name of the service within the application. Example:
   * default.@OutputOnly
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A set of labels to apply to this service. Labels are key/value pairs that
   * describe the service and all resources that belong to it (e.g., versions).
   * The labels can be used to search and group resources, and are propagated to
   * the usage and billing reports, enabling fine-grain analysis of costs. An
   * example of using labels is to tag resources belonging to different
   * environments (e.g., "env=prod", "env=qa"). Label keys and values can be no
   * longer than 63 characters and can only contain lowercase letters, numeric
   * characters, underscores, dashes, and international characters. Label keys
   * must start with a lowercase letter or an international character. Each
   * service can have at most 32 labels.
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
  /**
   * Output only. Full path to the Service resource in the API. Example:
   * apps/myapp/services/default.@OutputOnly
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
   * Ingress settings for this service. Will apply to all versions.
   *
   * @param NetworkSettings $networkSettings
   */
  public function setNetworkSettings(NetworkSettings $networkSettings)
  {
    $this->networkSettings = $networkSettings;
  }
  /**
   * @return NetworkSettings
   */
  public function getNetworkSettings()
  {
    return $this->networkSettings;
  }
  /**
   * Mapping that defines fractional HTTP traffic diversion to different
   * versions within the service.
   *
   * @param TrafficSplit $split
   */
  public function setSplit(TrafficSplit $split)
  {
    $this->split = $split;
  }
  /**
   * @return TrafficSplit
   */
  public function getSplit()
  {
    return $this->split;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_Appengine_Service');
