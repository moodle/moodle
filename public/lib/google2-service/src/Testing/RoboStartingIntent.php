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

namespace Google\Service\Testing;

class RoboStartingIntent extends \Google\Model
{
  protected $launcherActivityType = LauncherActivityIntent::class;
  protected $launcherActivityDataType = '';
  protected $noActivityType = NoActivityIntent::class;
  protected $noActivityDataType = '';
  protected $startActivityType = StartActivityIntent::class;
  protected $startActivityDataType = '';
  /**
   * Timeout in seconds for each intent.
   *
   * @var string
   */
  public $timeout;

  /**
   * An intent that starts the main launcher activity.
   *
   * @param LauncherActivityIntent $launcherActivity
   */
  public function setLauncherActivity(LauncherActivityIntent $launcherActivity)
  {
    $this->launcherActivity = $launcherActivity;
  }
  /**
   * @return LauncherActivityIntent
   */
  public function getLauncherActivity()
  {
    return $this->launcherActivity;
  }
  /**
   * Skips the starting activity
   *
   * @param NoActivityIntent $noActivity
   */
  public function setNoActivity(NoActivityIntent $noActivity)
  {
    $this->noActivity = $noActivity;
  }
  /**
   * @return NoActivityIntent
   */
  public function getNoActivity()
  {
    return $this->noActivity;
  }
  /**
   * An intent that starts an activity with specific details.
   *
   * @param StartActivityIntent $startActivity
   */
  public function setStartActivity(StartActivityIntent $startActivity)
  {
    $this->startActivity = $startActivity;
  }
  /**
   * @return StartActivityIntent
   */
  public function getStartActivity()
  {
    return $this->startActivity;
  }
  /**
   * Timeout in seconds for each intent.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoboStartingIntent::class, 'Google_Service_Testing_RoboStartingIntent');
