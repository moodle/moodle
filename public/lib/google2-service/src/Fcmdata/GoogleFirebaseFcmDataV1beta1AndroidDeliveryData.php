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

namespace Google\Service\Fcmdata;

class GoogleFirebaseFcmDataV1beta1AndroidDeliveryData extends \Google\Model
{
  /**
   * The analytics label associated with the messages sent. All messages sent
   * without an analytics label will be grouped together in a single entry.
   *
   * @var string
   */
  public $analyticsLabel;
  /**
   * The app ID to which the messages were sent.
   *
   * @var string
   */
  public $appId;
  protected $dataType = GoogleFirebaseFcmDataV1beta1Data::class;
  protected $dataDataType = '';
  protected $dateType = GoogleTypeDate::class;
  protected $dateDataType = '';

  /**
   * The analytics label associated with the messages sent. All messages sent
   * without an analytics label will be grouped together in a single entry.
   *
   * @param string $analyticsLabel
   */
  public function setAnalyticsLabel($analyticsLabel)
  {
    $this->analyticsLabel = $analyticsLabel;
  }
  /**
   * @return string
   */
  public function getAnalyticsLabel()
  {
    return $this->analyticsLabel;
  }
  /**
   * The app ID to which the messages were sent.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * The data for the specified appId, date, and analyticsLabel.
   *
   * @param GoogleFirebaseFcmDataV1beta1Data $data
   */
  public function setData(GoogleFirebaseFcmDataV1beta1Data $data)
  {
    $this->data = $data;
  }
  /**
   * @return GoogleFirebaseFcmDataV1beta1Data
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The date represented by this entry.
   *
   * @param GoogleTypeDate $date
   */
  public function setDate(GoogleTypeDate $date)
  {
    $this->date = $date;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getDate()
  {
    return $this->date;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseFcmDataV1beta1AndroidDeliveryData::class, 'Google_Service_Fcmdata_GoogleFirebaseFcmDataV1beta1AndroidDeliveryData');
