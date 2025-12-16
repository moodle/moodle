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

namespace Google\Service\Eventarc;

class GoogleApiSource extends \Google\Model
{
  /**
   * Optional. Resource annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * Required. Destination is the message bus that the GoogleApiSource is
   * delivering to. It must be point to the full resource name of a MessageBus.
   * Format:
   * "projects/{PROJECT_ID}/locations/{region}/messagesBuses/{MESSAGE_BUS_ID)
   *
   * @var string
   */
  public $destination;
  /**
   * Optional. Resource display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on update and delete requests to
   * ensure that the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Resource labels.
   *
   * @var string[]
   */
  public $labels;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Identifier. Resource name of the form projects/{project}/locations/{locatio
   * n}/googleApiSources/{google_api_source}
   *
   * @var string
   */
  public $name;
  protected $organizationSubscriptionType = OrganizationSubscription::class;
  protected $organizationSubscriptionDataType = '';
  protected $projectSubscriptionsType = ProjectSubscriptions::class;
  protected $projectSubscriptionsDataType = '';
  /**
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Resource annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The creation time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Required. Destination is the message bus that the GoogleApiSource is
   * delivering to. It must be point to the full resource name of a MessageBus.
   * Format:
   * "projects/{PROJECT_ID}/locations/{region}/messagesBuses/{MESSAGE_BUS_ID)
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Optional. Resource display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on update and delete requests to
   * ensure that the client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Resource labels.
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
   * Optional. Config to control Platform logging for the GoogleApiSource.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * Identifier. Resource name of the form projects/{project}/locations/{locatio
   * n}/googleApiSources/{google_api_source}
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
   * Optional. Config to enable subscribing to events from all projects in the
   * GoogleApiSource's org.
   *
   * @param OrganizationSubscription $organizationSubscription
   */
  public function setOrganizationSubscription(OrganizationSubscription $organizationSubscription)
  {
    $this->organizationSubscription = $organizationSubscription;
  }
  /**
   * @return OrganizationSubscription
   */
  public function getOrganizationSubscription()
  {
    return $this->organizationSubscription;
  }
  /**
   * Optional. Config to enable subscribing to all events from a list of
   * projects. All the projects must be in the same org as the GoogleApiSource.
   *
   * @param ProjectSubscriptions $projectSubscriptions
   */
  public function setProjectSubscriptions(ProjectSubscriptions $projectSubscriptions)
  {
    $this->projectSubscriptions = $projectSubscriptions;
  }
  /**
   * @return ProjectSubscriptions
   */
  public function getProjectSubscriptions()
  {
    return $this->projectSubscriptions;
  }
  /**
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The last-modified time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiSource::class, 'Google_Service_Eventarc_GoogleApiSource');
