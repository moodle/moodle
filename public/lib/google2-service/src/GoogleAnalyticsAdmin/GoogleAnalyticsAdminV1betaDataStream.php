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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaDataStream extends \Google\Model
{
  /**
   * Type unknown or not specified.
   */
  public const TYPE_DATA_STREAM_TYPE_UNSPECIFIED = 'DATA_STREAM_TYPE_UNSPECIFIED';
  /**
   * Web data stream.
   */
  public const TYPE_WEB_DATA_STREAM = 'WEB_DATA_STREAM';
  /**
   * Android app data stream.
   */
  public const TYPE_ANDROID_APP_DATA_STREAM = 'ANDROID_APP_DATA_STREAM';
  /**
   * iOS app data stream.
   */
  public const TYPE_IOS_APP_DATA_STREAM = 'IOS_APP_DATA_STREAM';
  protected $androidAppStreamDataType = GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData::class;
  protected $androidAppStreamDataDataType = '';
  /**
   * Output only. Time when this stream was originally created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Human-readable display name for the Data Stream. Required for web data
   * streams. The max allowed display name length is 255 UTF-16 code units.
   *
   * @var string
   */
  public $displayName;
  protected $iosAppStreamDataType = GoogleAnalyticsAdminV1betaDataStreamIosAppStreamData::class;
  protected $iosAppStreamDataDataType = '';
  /**
   * Output only. Resource name of this Data Stream. Format:
   * properties/{property_id}/dataStreams/{stream_id} Example:
   * "properties/1000/dataStreams/2000"
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The type of this DataStream resource.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Time when stream payload fields were last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $webStreamDataType = GoogleAnalyticsAdminV1betaDataStreamWebStreamData::class;
  protected $webStreamDataDataType = '';

  /**
   * Data specific to Android app streams. Must be populated if type is
   * ANDROID_APP_DATA_STREAM.
   *
   * @param GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData $androidAppStreamData
   */
  public function setAndroidAppStreamData(GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData $androidAppStreamData)
  {
    $this->androidAppStreamData = $androidAppStreamData;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaDataStreamAndroidAppStreamData
   */
  public function getAndroidAppStreamData()
  {
    return $this->androidAppStreamData;
  }
  /**
   * Output only. Time when this stream was originally created.
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
   * Human-readable display name for the Data Stream. Required for web data
   * streams. The max allowed display name length is 255 UTF-16 code units.
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
   * Data specific to iOS app streams. Must be populated if type is
   * IOS_APP_DATA_STREAM.
   *
   * @param GoogleAnalyticsAdminV1betaDataStreamIosAppStreamData $iosAppStreamData
   */
  public function setIosAppStreamData(GoogleAnalyticsAdminV1betaDataStreamIosAppStreamData $iosAppStreamData)
  {
    $this->iosAppStreamData = $iosAppStreamData;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaDataStreamIosAppStreamData
   */
  public function getIosAppStreamData()
  {
    return $this->iosAppStreamData;
  }
  /**
   * Output only. Resource name of this Data Stream. Format:
   * properties/{property_id}/dataStreams/{stream_id} Example:
   * "properties/1000/dataStreams/2000"
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
   * Required. Immutable. The type of this DataStream resource.
   *
   * Accepted values: DATA_STREAM_TYPE_UNSPECIFIED, WEB_DATA_STREAM,
   * ANDROID_APP_DATA_STREAM, IOS_APP_DATA_STREAM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Time when stream payload fields were last updated.
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
  /**
   * Data specific to web streams. Must be populated if type is WEB_DATA_STREAM.
   *
   * @param GoogleAnalyticsAdminV1betaDataStreamWebStreamData $webStreamData
   */
  public function setWebStreamData(GoogleAnalyticsAdminV1betaDataStreamWebStreamData $webStreamData)
  {
    $this->webStreamData = $webStreamData;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaDataStreamWebStreamData
   */
  public function getWebStreamData()
  {
    return $this->webStreamData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaDataStream::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaDataStream');
