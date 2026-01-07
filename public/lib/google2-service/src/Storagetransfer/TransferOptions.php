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

namespace Google\Service\Storagetransfer;

class TransferOptions extends \Google\Model
{
  /**
   * Overwrite behavior is unspecified.
   */
  public const OVERWRITE_WHEN_OVERWRITE_WHEN_UNSPECIFIED = 'OVERWRITE_WHEN_UNSPECIFIED';
  /**
   * Overwrites destination objects with the source objects, only if the objects
   * have the same name but different HTTP ETags or checksum values.
   */
  public const OVERWRITE_WHEN_DIFFERENT = 'DIFFERENT';
  /**
   * Never overwrites a destination object if a source object has the same name.
   * In this case, the source object is not transferred.
   */
  public const OVERWRITE_WHEN_NEVER = 'NEVER';
  /**
   * Always overwrite the destination object with the source object, even if the
   * HTTP Etags or checksum values are the same.
   */
  public const OVERWRITE_WHEN_ALWAYS = 'ALWAYS';
  /**
   * Whether objects should be deleted from the source after they are
   * transferred to the sink. **Note:** This option and
   * delete_objects_unique_in_sink are mutually exclusive.
   *
   * @var bool
   */
  public $deleteObjectsFromSourceAfterTransfer;
  /**
   * Whether objects that exist only in the sink should be deleted. **Note:**
   * This option and delete_objects_from_source_after_transfer are mutually
   * exclusive.
   *
   * @var bool
   */
  public $deleteObjectsUniqueInSink;
  protected $metadataOptionsType = MetadataOptions::class;
  protected $metadataOptionsDataType = '';
  /**
   * When to overwrite objects that already exist in the sink. The default is
   * that only objects that are different from the source are overwritten. If
   * true, all objects in the sink whose name matches an object in the source
   * are overwritten with the source object.
   *
   * @var bool
   */
  public $overwriteObjectsAlreadyExistingInSink;
  /**
   * When to overwrite objects that already exist in the sink. If not set,
   * overwrite behavior is determined by
   * overwrite_objects_already_existing_in_sink.
   *
   * @var string
   */
  public $overwriteWhen;

  /**
   * Whether objects should be deleted from the source after they are
   * transferred to the sink. **Note:** This option and
   * delete_objects_unique_in_sink are mutually exclusive.
   *
   * @param bool $deleteObjectsFromSourceAfterTransfer
   */
  public function setDeleteObjectsFromSourceAfterTransfer($deleteObjectsFromSourceAfterTransfer)
  {
    $this->deleteObjectsFromSourceAfterTransfer = $deleteObjectsFromSourceAfterTransfer;
  }
  /**
   * @return bool
   */
  public function getDeleteObjectsFromSourceAfterTransfer()
  {
    return $this->deleteObjectsFromSourceAfterTransfer;
  }
  /**
   * Whether objects that exist only in the sink should be deleted. **Note:**
   * This option and delete_objects_from_source_after_transfer are mutually
   * exclusive.
   *
   * @param bool $deleteObjectsUniqueInSink
   */
  public function setDeleteObjectsUniqueInSink($deleteObjectsUniqueInSink)
  {
    $this->deleteObjectsUniqueInSink = $deleteObjectsUniqueInSink;
  }
  /**
   * @return bool
   */
  public function getDeleteObjectsUniqueInSink()
  {
    return $this->deleteObjectsUniqueInSink;
  }
  /**
   * Represents the selected metadata options for a transfer job.
   *
   * @param MetadataOptions $metadataOptions
   */
  public function setMetadataOptions(MetadataOptions $metadataOptions)
  {
    $this->metadataOptions = $metadataOptions;
  }
  /**
   * @return MetadataOptions
   */
  public function getMetadataOptions()
  {
    return $this->metadataOptions;
  }
  /**
   * When to overwrite objects that already exist in the sink. The default is
   * that only objects that are different from the source are overwritten. If
   * true, all objects in the sink whose name matches an object in the source
   * are overwritten with the source object.
   *
   * @param bool $overwriteObjectsAlreadyExistingInSink
   */
  public function setOverwriteObjectsAlreadyExistingInSink($overwriteObjectsAlreadyExistingInSink)
  {
    $this->overwriteObjectsAlreadyExistingInSink = $overwriteObjectsAlreadyExistingInSink;
  }
  /**
   * @return bool
   */
  public function getOverwriteObjectsAlreadyExistingInSink()
  {
    return $this->overwriteObjectsAlreadyExistingInSink;
  }
  /**
   * When to overwrite objects that already exist in the sink. If not set,
   * overwrite behavior is determined by
   * overwrite_objects_already_existing_in_sink.
   *
   * Accepted values: OVERWRITE_WHEN_UNSPECIFIED, DIFFERENT, NEVER, ALWAYS
   *
   * @param self::OVERWRITE_WHEN_* $overwriteWhen
   */
  public function setOverwriteWhen($overwriteWhen)
  {
    $this->overwriteWhen = $overwriteWhen;
  }
  /**
   * @return self::OVERWRITE_WHEN_*
   */
  public function getOverwriteWhen()
  {
    return $this->overwriteWhen;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferOptions::class, 'Google_Service_Storagetransfer_TransferOptions');
