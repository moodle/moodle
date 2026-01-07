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

namespace Google\Service\Storage\Resource;

use Google\Service\Storage\Folder;
use Google\Service\Storage\Folders as FoldersModel;
use Google\Service\Storage\GoogleLongrunningOperation;

/**
 * The "folders" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storageService = new Google\Service\Storage(...);
 *   $folders = $storageService->folders;
 *  </code>
 */
class Folders extends \Google\Service\Resource
{
  /**
   * Permanently deletes a folder. Only applicable to buckets with hierarchical
   * namespace enabled. (folders.delete)
   *
   * @param string $bucket Name of the bucket in which the folder resides.
   * @param string $folder Name of a folder.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string ifMetagenerationMatch If set, only deletes the folder if
   * its metageneration matches this value.
   * @opt_param string ifMetagenerationNotMatch If set, only deletes the folder if
   * its metageneration does not match this value.
   * @throws \Google\Service\Exception
   */
  public function delete($bucket, $folder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'folder' => $folder];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Returns metadata for the specified folder. Only applicable to buckets with
   * hierarchical namespace enabled. (folders.get)
   *
   * @param string $bucket Name of the bucket in which the folder resides.
   * @param string $folder Name of a folder.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string ifMetagenerationMatch Makes the return of the folder
   * metadata conditional on whether the folder's current metageneration matches
   * the given value.
   * @opt_param string ifMetagenerationNotMatch Makes the return of the folder
   * metadata conditional on whether the folder's current metageneration does not
   * match the given value.
   * @return Folder
   * @throws \Google\Service\Exception
   */
  public function get($bucket, $folder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'folder' => $folder];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Folder::class);
  }
  /**
   * Creates a new folder. Only applicable to buckets with hierarchical namespace
   * enabled. (folders.insert)
   *
   * @param string $bucket Name of the bucket in which the folder resides.
   * @param Folder $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool recursive If true, any parent folder which doesn't exist will
   * be created automatically.
   * @return Folder
   * @throws \Google\Service\Exception
   */
  public function insert($bucket, Folder $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Folder::class);
  }
  /**
   * Retrieves a list of folders matching the criteria. Only applicable to buckets
   * with hierarchical namespace enabled. (folders.listFolders)
   *
   * @param string $bucket Name of the bucket in which to look for folders.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string delimiter Returns results in a directory-like mode. The
   * only supported value is '/'. If set, items will only contain folders that
   * either exactly match the prefix, or are one level below the prefix.
   * @opt_param string endOffset Filter results to folders whose names are
   * lexicographically before endOffset. If startOffset is also set, the folders
   * listed will have names between startOffset (inclusive) and endOffset
   * (exclusive).
   * @opt_param int pageSize Maximum number of items to return in a single page of
   * responses.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @opt_param string prefix Filter results to folders whose paths begin with
   * this prefix. If set, the value must either be an empty string or end with a
   * '/'.
   * @opt_param string startOffset Filter results to folders whose names are
   * lexicographically equal to or after startOffset. If endOffset is also set,
   * the folders listed will have names between startOffset (inclusive) and
   * endOffset (exclusive).
   * @return FoldersModel
   * @throws \Google\Service\Exception
   */
  public function listFolders($bucket, $optParams = [])
  {
    $params = ['bucket' => $bucket];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], FoldersModel::class);
  }
  /**
   * Renames a source folder to a destination folder. Only applicable to buckets
   * with hierarchical namespace enabled. (folders.rename)
   *
   * @param string $bucket Name of the bucket in which the folders are in.
   * @param string $sourceFolder Name of the source folder.
   * @param string $destinationFolder Name of the destination folder.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string ifSourceMetagenerationMatch Makes the operation conditional
   * on whether the source object's current metageneration matches the given
   * value.
   * @opt_param string ifSourceMetagenerationNotMatch Makes the operation
   * conditional on whether the source object's current metageneration does not
   * match the given value.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function rename($bucket, $sourceFolder, $destinationFolder, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'sourceFolder' => $sourceFolder, 'destinationFolder' => $destinationFolder];
    $params = array_merge($params, $optParams);
    return $this->call('rename', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Folders::class, 'Google_Service_Storage_Resource_Folders');
