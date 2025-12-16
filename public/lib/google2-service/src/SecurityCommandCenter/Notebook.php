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

namespace Google\Service\SecurityCommandCenter;

class Notebook extends \Google\Model
{
  /**
   * The user ID of the latest author to modify the notebook.
   *
   * @var string
   */
  public $lastAuthor;
  /**
   * The name of the notebook.
   *
   * @var string
   */
  public $name;
  /**
   * The most recent time the notebook was updated.
   *
   * @var string
   */
  public $notebookUpdateTime;
  /**
   * The source notebook service, for example, "Colab Enterprise".
   *
   * @var string
   */
  public $service;

  /**
   * The user ID of the latest author to modify the notebook.
   *
   * @param string $lastAuthor
   */
  public function setLastAuthor($lastAuthor)
  {
    $this->lastAuthor = $lastAuthor;
  }
  /**
   * @return string
   */
  public function getLastAuthor()
  {
    return $this->lastAuthor;
  }
  /**
   * The name of the notebook.
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
   * The most recent time the notebook was updated.
   *
   * @param string $notebookUpdateTime
   */
  public function setNotebookUpdateTime($notebookUpdateTime)
  {
    $this->notebookUpdateTime = $notebookUpdateTime;
  }
  /**
   * @return string
   */
  public function getNotebookUpdateTime()
  {
    return $this->notebookUpdateTime;
  }
  /**
   * The source notebook service, for example, "Colab Enterprise".
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Notebook::class, 'Google_Service_SecurityCommandCenter_Notebook');
