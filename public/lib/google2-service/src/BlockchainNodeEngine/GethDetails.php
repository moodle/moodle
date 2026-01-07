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

namespace Google\Service\BlockchainNodeEngine;

class GethDetails extends \Google\Model
{
  /**
   * The garbage collection has not been specified.
   */
  public const GARBAGE_COLLECTION_MODE_GARBAGE_COLLECTION_MODE_UNSPECIFIED = 'GARBAGE_COLLECTION_MODE_UNSPECIFIED';
  /**
   * Configures Geth's garbage collection so that older data not needed for a
   * full node is deleted. This is the default mode when creating a full node.
   */
  public const GARBAGE_COLLECTION_MODE_FULL = 'FULL';
  /**
   * Configures Geth's garbage collection so that old data is never deleted.
   * This is the default mode when creating an archive node. This value can also
   * be chosen when creating a full node in order to create a partial/recent
   * archive node. See [Sync
   * modes](https://geth.ethereum.org/docs/fundamentals/sync-modes) for more
   * details.
   */
  public const GARBAGE_COLLECTION_MODE_ARCHIVE = 'ARCHIVE';
  /**
   * Immutable. Blockchain garbage collection mode.
   *
   * @var string
   */
  public $garbageCollectionMode;

  /**
   * Immutable. Blockchain garbage collection mode.
   *
   * Accepted values: GARBAGE_COLLECTION_MODE_UNSPECIFIED, FULL, ARCHIVE
   *
   * @param self::GARBAGE_COLLECTION_MODE_* $garbageCollectionMode
   */
  public function setGarbageCollectionMode($garbageCollectionMode)
  {
    $this->garbageCollectionMode = $garbageCollectionMode;
  }
  /**
   * @return self::GARBAGE_COLLECTION_MODE_*
   */
  public function getGarbageCollectionMode()
  {
    return $this->garbageCollectionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GethDetails::class, 'Google_Service_BlockchainNodeEngine_GethDetails');
