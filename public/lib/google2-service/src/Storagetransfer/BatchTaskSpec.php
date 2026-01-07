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

class BatchTaskSpec extends \Google\Model
{
  protected $deleteObjectTaskSpecType = DeleteObjectTaskSpec::class;
  protected $deleteObjectTaskSpecDataType = '';
  protected $listTaskSpecType = ListTaskSpec::class;
  protected $listTaskSpecDataType = '';
  protected $metadataTaskSpecType = MetadataTaskSpec::class;
  protected $metadataTaskSpecDataType = '';

  /**
   * @param DeleteObjectTaskSpec
   */
  public function setDeleteObjectTaskSpec(DeleteObjectTaskSpec $deleteObjectTaskSpec)
  {
    $this->deleteObjectTaskSpec = $deleteObjectTaskSpec;
  }
  /**
   * @return DeleteObjectTaskSpec
   */
  public function getDeleteObjectTaskSpec()
  {
    return $this->deleteObjectTaskSpec;
  }
  /**
   * @param ListTaskSpec
   */
  public function setListTaskSpec(ListTaskSpec $listTaskSpec)
  {
    $this->listTaskSpec = $listTaskSpec;
  }
  /**
   * @return ListTaskSpec
   */
  public function getListTaskSpec()
  {
    return $this->listTaskSpec;
  }
  /**
   * @param MetadataTaskSpec
   */
  public function setMetadataTaskSpec(MetadataTaskSpec $metadataTaskSpec)
  {
    $this->metadataTaskSpec = $metadataTaskSpec;
  }
  /**
   * @return MetadataTaskSpec
   */
  public function getMetadataTaskSpec()
  {
    return $this->metadataTaskSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchTaskSpec::class, 'Google_Service_Storagetransfer_BatchTaskSpec');
