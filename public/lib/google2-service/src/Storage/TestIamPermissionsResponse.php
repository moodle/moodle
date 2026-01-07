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

namespace Google\Service\Storage;

class TestIamPermissionsResponse extends \Google\Collection
{
  protected $collection_key = 'permissions';
  /**
   * The kind of item this is.
   *
   * @var string
   */
  public $kind;
  /**
   * The permissions held by the caller. Permissions are always of the format
   * storage.resource.capability, where resource is one of buckets, objects, or
   * managedFolders. The supported permissions are as follows: -
   * storage.buckets.delete - Delete bucket.   - storage.buckets.get - Read
   * bucket metadata.   - storage.buckets.getIamPolicy - Read bucket IAM policy.
   * - storage.buckets.create - Create bucket.   - storage.buckets.list - List
   * buckets.   - storage.buckets.setIamPolicy - Update bucket IAM policy.   -
   * storage.buckets.update - Update bucket metadata.   - storage.objects.delete
   * - Delete object.   - storage.objects.get - Read object data and metadata.
   * - storage.objects.getIamPolicy - Read object IAM policy.   -
   * storage.objects.create - Create object.   - storage.objects.list - List
   * objects.   - storage.objects.setIamPolicy - Update object IAM policy.   -
   * storage.objects.update - Update object metadata.  -
   * storage.managedFolders.delete - Delete managed folder.   -
   * storage.managedFolders.get - Read managed folder metadata.   -
   * storage.managedFolders.getIamPolicy - Read managed folder IAM policy.   -
   * storage.managedFolders.create - Create managed folder.   -
   * storage.managedFolders.list - List managed folders.   -
   * storage.managedFolders.setIamPolicy - Update managed folder IAM policy.
   *
   * @var string[]
   */
  public $permissions;

  /**
   * The kind of item this is.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The permissions held by the caller. Permissions are always of the format
   * storage.resource.capability, where resource is one of buckets, objects, or
   * managedFolders. The supported permissions are as follows: -
   * storage.buckets.delete - Delete bucket.   - storage.buckets.get - Read
   * bucket metadata.   - storage.buckets.getIamPolicy - Read bucket IAM policy.
   * - storage.buckets.create - Create bucket.   - storage.buckets.list - List
   * buckets.   - storage.buckets.setIamPolicy - Update bucket IAM policy.   -
   * storage.buckets.update - Update bucket metadata.   - storage.objects.delete
   * - Delete object.   - storage.objects.get - Read object data and metadata.
   * - storage.objects.getIamPolicy - Read object IAM policy.   -
   * storage.objects.create - Create object.   - storage.objects.list - List
   * objects.   - storage.objects.setIamPolicy - Update object IAM policy.   -
   * storage.objects.update - Update object metadata.  -
   * storage.managedFolders.delete - Delete managed folder.   -
   * storage.managedFolders.get - Read managed folder metadata.   -
   * storage.managedFolders.getIamPolicy - Read managed folder IAM policy.   -
   * storage.managedFolders.create - Create managed folder.   -
   * storage.managedFolders.list - List managed folders.   -
   * storage.managedFolders.setIamPolicy - Update managed folder IAM policy.
   *
   * @param string[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return string[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestIamPermissionsResponse::class, 'Google_Service_Storage_TestIamPermissionsResponse');
