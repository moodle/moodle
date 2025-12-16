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

namespace Google\Service\FirebaseManagement;

class StreamMapping extends \Google\Model
{
  /**
   * The resource name of the Firebase App associated with the Google Analytics
   * data stream, in the format: projects/PROJECT_IDENTIFIER/androidApps/APP_ID
   * or projects/PROJECT_IDENTIFIER/iosApps/APP_ID or
   * projects/PROJECT_IDENTIFIER /webApps/APP_ID Refer to the `FirebaseProject`
   * [`name`](../projects#FirebaseProject.FIELDS.name) field for details about
   * PROJECT_IDENTIFIER values.
   *
   * @var string
   */
  public $app;
  /**
   * Applicable for Firebase Web Apps only. The unique Google-assigned
   * identifier of the Google Analytics web stream associated with the Firebase
   * Web App. Firebase SDKs use this ID to interact with Google Analytics APIs.
   * Learn more about this ID and Google Analytics web streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9304153).
   *
   * @var string
   */
  public $measurementId;
  /**
   * The unique Google-assigned identifier of the Google Analytics data stream
   * associated with the Firebase App. Learn more about Google Analytics data
   * streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9303323).
   *
   * @var string
   */
  public $streamId;

  /**
   * The resource name of the Firebase App associated with the Google Analytics
   * data stream, in the format: projects/PROJECT_IDENTIFIER/androidApps/APP_ID
   * or projects/PROJECT_IDENTIFIER/iosApps/APP_ID or
   * projects/PROJECT_IDENTIFIER /webApps/APP_ID Refer to the `FirebaseProject`
   * [`name`](../projects#FirebaseProject.FIELDS.name) field for details about
   * PROJECT_IDENTIFIER values.
   *
   * @param string $app
   */
  public function setApp($app)
  {
    $this->app = $app;
  }
  /**
   * @return string
   */
  public function getApp()
  {
    return $this->app;
  }
  /**
   * Applicable for Firebase Web Apps only. The unique Google-assigned
   * identifier of the Google Analytics web stream associated with the Firebase
   * Web App. Firebase SDKs use this ID to interact with Google Analytics APIs.
   * Learn more about this ID and Google Analytics web streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9304153).
   *
   * @param string $measurementId
   */
  public function setMeasurementId($measurementId)
  {
    $this->measurementId = $measurementId;
  }
  /**
   * @return string
   */
  public function getMeasurementId()
  {
    return $this->measurementId;
  }
  /**
   * The unique Google-assigned identifier of the Google Analytics data stream
   * associated with the Firebase App. Learn more about Google Analytics data
   * streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9303323).
   *
   * @param string $streamId
   */
  public function setStreamId($streamId)
  {
    $this->streamId = $streamId;
  }
  /**
   * @return string
   */
  public function getStreamId()
  {
    return $this->streamId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamMapping::class, 'Google_Service_FirebaseManagement_StreamMapping');
