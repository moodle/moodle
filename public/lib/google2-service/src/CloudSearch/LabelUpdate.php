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

namespace Google\Service\CloudSearch;

class LabelUpdate extends \Google\Model
{
  /**
   * @var string
   */
  public $canonicalName;
  protected $labelCreatedType = LabelCreated::class;
  protected $labelCreatedDataType = '';
  protected $labelDeletedType = LabelDeleted::class;
  protected $labelDeletedDataType = '';
  /**
   * @var string
   */
  public $labelId;
  protected $labelRenamedType = LabelRenamed::class;
  protected $labelRenamedDataType = '';
  protected $labelUpdatedType = LabelUpdated::class;
  protected $labelUpdatedDataType = '';
  /**
   * @var string
   */
  public $syncId;

  /**
   * @param string
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * @param LabelCreated
   */
  public function setLabelCreated(LabelCreated $labelCreated)
  {
    $this->labelCreated = $labelCreated;
  }
  /**
   * @return LabelCreated
   */
  public function getLabelCreated()
  {
    return $this->labelCreated;
  }
  /**
   * @param LabelDeleted
   */
  public function setLabelDeleted(LabelDeleted $labelDeleted)
  {
    $this->labelDeleted = $labelDeleted;
  }
  /**
   * @return LabelDeleted
   */
  public function getLabelDeleted()
  {
    return $this->labelDeleted;
  }
  /**
   * @param string
   */
  public function setLabelId($labelId)
  {
    $this->labelId = $labelId;
  }
  /**
   * @return string
   */
  public function getLabelId()
  {
    return $this->labelId;
  }
  /**
   * @param LabelRenamed
   */
  public function setLabelRenamed(LabelRenamed $labelRenamed)
  {
    $this->labelRenamed = $labelRenamed;
  }
  /**
   * @return LabelRenamed
   */
  public function getLabelRenamed()
  {
    return $this->labelRenamed;
  }
  /**
   * @param LabelUpdated
   */
  public function setLabelUpdated(LabelUpdated $labelUpdated)
  {
    $this->labelUpdated = $labelUpdated;
  }
  /**
   * @return LabelUpdated
   */
  public function getLabelUpdated()
  {
    return $this->labelUpdated;
  }
  /**
   * @param string
   */
  public function setSyncId($syncId)
  {
    $this->syncId = $syncId;
  }
  /**
   * @return string
   */
  public function getSyncId()
  {
    return $this->syncId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelUpdate::class, 'Google_Service_CloudSearch_LabelUpdate');
