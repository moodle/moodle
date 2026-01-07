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

namespace Google\Service\FirebaseAppDistribution;

class GoogleFirebaseAppdistroV1FeedbackReport extends \Google\Model
{
  /**
   * Output only. The time when the feedback report was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. A link to the Firebase console displaying the feedback report.
   *
   * @var string
   */
  public $firebaseConsoleUri;
  /**
   * The name of the feedback report resource. Format: `projects/{project_number
   * }/apps/{app}/releases/{release}/feedbackReports/{feedback_report}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A signed link (which expires in one hour) that lets you
   * directly download the screenshot.
   *
   * @var string
   */
  public $screenshotUri;
  /**
   * Output only. The resource name of the tester who submitted the feedback
   * report.
   *
   * @var string
   */
  public $tester;
  /**
   * Output only. The text of the feedback report.
   *
   * @var string
   */
  public $text;

  /**
   * Output only. The time when the feedback report was created.
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
   * Output only. A link to the Firebase console displaying the feedback report.
   *
   * @param string $firebaseConsoleUri
   */
  public function setFirebaseConsoleUri($firebaseConsoleUri)
  {
    $this->firebaseConsoleUri = $firebaseConsoleUri;
  }
  /**
   * @return string
   */
  public function getFirebaseConsoleUri()
  {
    return $this->firebaseConsoleUri;
  }
  /**
   * The name of the feedback report resource. Format: `projects/{project_number
   * }/apps/{app}/releases/{release}/feedbackReports/{feedback_report}`
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
   * Output only. A signed link (which expires in one hour) that lets you
   * directly download the screenshot.
   *
   * @param string $screenshotUri
   */
  public function setScreenshotUri($screenshotUri)
  {
    $this->screenshotUri = $screenshotUri;
  }
  /**
   * @return string
   */
  public function getScreenshotUri()
  {
    return $this->screenshotUri;
  }
  /**
   * Output only. The resource name of the tester who submitted the feedback
   * report.
   *
   * @param string $tester
   */
  public function setTester($tester)
  {
    $this->tester = $tester;
  }
  /**
   * @return string
   */
  public function getTester()
  {
    return $this->tester;
  }
  /**
   * Output only. The text of the feedback report.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1FeedbackReport::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1FeedbackReport');
