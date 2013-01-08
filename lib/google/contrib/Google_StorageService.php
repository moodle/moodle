<?php
/*
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


  /**
   * The "objectAccessControls" collection of methods.
   * Typical usage is:
   *  <code>
   *   $storageService = new Google_StorageService(...);
   *   $objectAccessControls = $storageService->objectAccessControls;
   *  </code>
   */
  class Google_ObjectAccessControlsServiceResource extends Google_ServiceResource {


    /**
     * Creates a new ACL entry on the specified object. (objectAccessControls.insert)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param Google_ObjectAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_ObjectAccessControl
     */
    public function insert($bucket, $object, Google_ObjectAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_ObjectAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the ACL entry for the specified entity on the specified object.
     * (objectAccessControls.get)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param array $optParams Optional parameters.
     * @return Google_ObjectAccessControl
     */
    public function get($bucket, $object, $entity, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'entity' => $entity);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_ObjectAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves ACL entries on the specified object. (objectAccessControls.list)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param array $optParams Optional parameters.
     * @return Google_ObjectAccessControls
     */
    public function listObjectAccessControls($bucket, $object, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ObjectAccessControls($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an ACL entry on the specified object. (objectAccessControls.update)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param Google_ObjectAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_ObjectAccessControl
     */
    public function update($bucket, $object, $entity, Google_ObjectAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'entity' => $entity, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_ObjectAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an ACL entry on the specified object. This method supports patch semantics.
     * (objectAccessControls.patch)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param Google_ObjectAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_ObjectAccessControl
     */
    public function patch($bucket, $object, $entity, Google_ObjectAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'entity' => $entity, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_ObjectAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the ACL entry for the specified entity on the specified object.
     * (objectAccessControls.delete)
     *
     * @param string $bucket Name of a bucket.
     * @param string $object Name of the object.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param array $optParams Optional parameters.
     */
    public function delete($bucket, $object, $entity, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'entity' => $entity);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "bucketAccessControls" collection of methods.
   * Typical usage is:
   *  <code>
   *   $storageService = new Google_StorageService(...);
   *   $bucketAccessControls = $storageService->bucketAccessControls;
   *  </code>
   */
  class Google_BucketAccessControlsServiceResource extends Google_ServiceResource {


    /**
     * Creates a new ACL entry on the specified bucket. (bucketAccessControls.insert)
     *
     * @param string $bucket Name of a bucket.
     * @param Google_BucketAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_BucketAccessControl
     */
    public function insert($bucket, Google_BucketAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_BucketAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the ACL entry for the specified entity on the specified bucket.
     * (bucketAccessControls.get)
     *
     * @param string $bucket Name of a bucket.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param array $optParams Optional parameters.
     * @return Google_BucketAccessControl
     */
    public function get($bucket, $entity, $optParams = array()) {
      $params = array('bucket' => $bucket, 'entity' => $entity);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_BucketAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves ACL entries on the specified bucket. (bucketAccessControls.list)
     *
     * @param string $bucket Name of a bucket.
     * @param array $optParams Optional parameters.
     * @return Google_BucketAccessControls
     */
    public function listBucketAccessControls($bucket, $optParams = array()) {
      $params = array('bucket' => $bucket);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_BucketAccessControls($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an ACL entry on the specified bucket. (bucketAccessControls.update)
     *
     * @param string $bucket Name of a bucket.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param Google_BucketAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_BucketAccessControl
     */
    public function update($bucket, $entity, Google_BucketAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'entity' => $entity, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_BucketAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an ACL entry on the specified bucket. This method supports patch semantics.
     * (bucketAccessControls.patch)
     *
     * @param string $bucket Name of a bucket.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param Google_BucketAccessControl $postBody
     * @param array $optParams Optional parameters.
     * @return Google_BucketAccessControl
     */
    public function patch($bucket, $entity, Google_BucketAccessControl $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'entity' => $entity, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_BucketAccessControl($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the ACL entry for the specified entity on the specified bucket.
     * (bucketAccessControls.delete)
     *
     * @param string $bucket Name of a bucket.
     * @param string $entity The entity holding the permission. Can be user-userId, group-groupId, allUsers, or allAuthenticatedUsers.
     * @param array $optParams Optional parameters.
     */
    public function delete($bucket, $entity, $optParams = array()) {
      $params = array('bucket' => $bucket, 'entity' => $entity);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "objects" collection of methods.
   * Typical usage is:
   *  <code>
   *   $storageService = new Google_StorageService(...);
   *   $objects = $storageService->objects;
   *  </code>
   */
  class Google_ObjectsServiceResource extends Google_ServiceResource {


    /**
     * Stores new data blobs and associated metadata. (objects.insert)
     *
     * @param string $bucket Name of the bucket in which to store the new object. Overrides the provided object metadata's bucket value, if any.
     * @param Google_StorageObject $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string name Name of the object. Required when the object metadata is not otherwise provided. Overrides the object metadata's name value, if any.
     * @opt_param string projection Set of properties to return. Defaults to no_acl, unless the object resource specifies the acl property, when it defaults to full.
     * @return Google_StorageObject
     */
    public function insert($bucket, Google_StorageObject $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_StorageObject($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves objects or their associated metadata. (objects.get)
     *
     * @param string $bucket Name of the bucket in which the object resides.
     * @param string $object Name of the object.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to no_acl.
     * @return Google_StorageObject
     */
    public function get($bucket, $object, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_StorageObject($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of objects matching the criteria. (objects.list)
     *
     * @param string $bucket Name of the bucket in which to look for objects.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of items plus prefixes to return. As duplicate prefixes are omitted, fewer total results may be returned than requested.
     * @opt_param string projection Set of properties to return. Defaults to no_acl.
     * @opt_param string prefix Filter results to objects whose names begin with this prefix.
     * @opt_param string pageToken A previously-returned page token representing part of the larger set of results to view.
     * @opt_param string delimiter Returns results in a directory-like mode. items will contain only objects whose names, aside from the prefix, do not contain delimiter. Objects whose names, aside from the prefix, contain delimiter will have their name, truncated after the delimiter, returned in prefixes. Duplicate prefixes are omitted.
     * @return Google_Objects
     */
    public function listObjects($bucket, $optParams = array()) {
      $params = array('bucket' => $bucket);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Objects($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a data blob's associated metadata. (objects.update)
     *
     * @param string $bucket Name of the bucket in which the object resides.
     * @param string $object Name of the object.
     * @param Google_StorageObject $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to full.
     * @return Google_StorageObject
     */
    public function update($bucket, $object, Google_StorageObject $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_StorageObject($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a data blob's associated metadata. This method supports patch semantics. (objects.patch)
     *
     * @param string $bucket Name of the bucket in which the object resides.
     * @param string $object Name of the object.
     * @param Google_StorageObject $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to full.
     * @return Google_StorageObject
     */
    public function patch($bucket, $object, Google_StorageObject $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_StorageObject($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes data blobs and associated metadata. (objects.delete)
     *
     * @param string $bucket Name of the bucket in which the object resides.
     * @param string $object Name of the object.
     * @param array $optParams Optional parameters.
     */
    public function delete($bucket, $object, $optParams = array()) {
      $params = array('bucket' => $bucket, 'object' => $object);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "buckets" collection of methods.
   * Typical usage is:
   *  <code>
   *   $storageService = new Google_StorageService(...);
   *   $buckets = $storageService->buckets;
   *  </code>
   */
  class Google_BucketsServiceResource extends Google_ServiceResource {


    /**
     * Creates a new bucket. (buckets.insert)
     *
     * @param Google_Bucket $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to no_acl, unless the bucket resource specifies acl or defaultObjectAcl properties, when it defaults to full.
     * @return Google_Bucket
     */
    public function insert(Google_Bucket $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Bucket($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns metadata for the specified bucket. (buckets.get)
     *
     * @param string $bucket Name of a bucket.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to no_acl.
     * @return Google_Bucket
     */
    public function get($bucket, $optParams = array()) {
      $params = array('bucket' => $bucket);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Bucket($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a list of buckets for a given project. (buckets.list)
     *
     * @param string $projectId A valid API project identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of buckets to return.
     * @opt_param string pageToken A previously-returned page token representing part of the larger set of results to view.
     * @opt_param string projection Set of properties to return. Defaults to no_acl.
     * @return Google_Buckets
     */
    public function listBuckets($projectId, $optParams = array()) {
      $params = array('projectId' => $projectId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Buckets($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a bucket. (buckets.update)
     *
     * @param string $bucket Name of a bucket.
     * @param Google_Bucket $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to full.
     * @return Google_Bucket
     */
    public function update($bucket, Google_Bucket $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Bucket($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a bucket. This method supports patch semantics. (buckets.patch)
     *
     * @param string $bucket Name of a bucket.
     * @param Google_Bucket $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string projection Set of properties to return. Defaults to full.
     * @return Google_Bucket
     */
    public function patch($bucket, Google_Bucket $postBody, $optParams = array()) {
      $params = array('bucket' => $bucket, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Bucket($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an empty bucket. (buckets.delete)
     *
     * @param string $bucket Name of a bucket.
     * @param array $optParams Optional parameters.
     */
    public function delete($bucket, $optParams = array()) {
      $params = array('bucket' => $bucket);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

/**
 * Service definition for Google_Storage (v1beta1).
 *
 * <p>
 * Lets you store and retrieve potentially-large, immutable data objects.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://code.google.com/apis/storage/docs/developer-guide.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_StorageService extends Google_Service {
  public $objectAccessControls;
  public $bucketAccessControls;
  public $objects;
  public $buckets;
  /**
   * Constructs the internal representation of the Storage service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'storage/v1beta1/';
    $this->version = 'v1beta1';
    $this->serviceName = 'storage';

    $client->addService($this->serviceName, $this->version);
    $this->objectAccessControls = new Google_ObjectAccessControlsServiceResource($this, $this->serviceName, 'objectAccessControls', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ObjectAccessControl"}, "response": {"$ref": "ObjectAccessControl"}, "httpMethod": "POST", "path": "b/{bucket}/o/{object}/acl", "id": "storage.objectAccessControls.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "id": "storage.objectAccessControls.get", "httpMethod": "GET", "path": "b/{bucket}/o/{object}/acl/{entity}", "response": {"$ref": "ObjectAccessControl"}}, "list": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}}, "id": "storage.objectAccessControls.list", "httpMethod": "GET", "path": "b/{bucket}/o/{object}/acl", "response": {"$ref": "ObjectAccessControls"}}, "update": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ObjectAccessControl"}, "response": {"$ref": "ObjectAccessControl"}, "httpMethod": "PUT", "path": "b/{bucket}/o/{object}/acl/{entity}", "id": "storage.objectAccessControls.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ObjectAccessControl"}, "response": {"$ref": "ObjectAccessControl"}, "httpMethod": "PATCH", "path": "b/{bucket}/o/{object}/acl/{entity}", "id": "storage.objectAccessControls.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "path": "b/{bucket}/o/{object}/acl/{entity}", "id": "storage.objectAccessControls.delete", "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->bucketAccessControls = new Google_BucketAccessControlsServiceResource($this, $this->serviceName, 'bucketAccessControls', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "BucketAccessControl"}, "response": {"$ref": "BucketAccessControl"}, "httpMethod": "POST", "path": "b/{bucket}/acl", "id": "storage.bucketAccessControls.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "id": "storage.bucketAccessControls.get", "httpMethod": "GET", "path": "b/{bucket}/acl/{entity}", "response": {"$ref": "BucketAccessControl"}}, "list": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}}, "id": "storage.bucketAccessControls.list", "httpMethod": "GET", "path": "b/{bucket}/acl", "response": {"$ref": "BucketAccessControls"}}, "update": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "BucketAccessControl"}, "response": {"$ref": "BucketAccessControl"}, "httpMethod": "PUT", "path": "b/{bucket}/acl/{entity}", "id": "storage.bucketAccessControls.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "BucketAccessControl"}, "response": {"$ref": "BucketAccessControl"}, "httpMethod": "PATCH", "path": "b/{bucket}/acl/{entity}", "id": "storage.bucketAccessControls.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control"], "path": "b/{bucket}/acl/{entity}", "id": "storage.bucketAccessControls.delete", "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "entity": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->objects = new Google_ObjectsServiceResource($this, $this->serviceName, 'objects', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "name": {"type": "string", "location": "query"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "supportsMediaUpload": true, "request": {"$ref": "Object"}, "mediaUpload": {"protocols": {"simple": {"path": "/upload/storage/v1beta1/b/{bucket}/o", "multipart": true}, "resumable": {"path": "/resumable/upload/storage/v1beta1/b/{bucket}/o", "multipart": true}}, "accept": ["*/*"]}, "response": {"$ref": "Object"}, "httpMethod": "POST", "path": "b/{bucket}/o", "id": "storage.objects.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_only", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "id": "storage.objects.get", "httpMethod": "GET", "supportsMediaDownload": true, "path": "b/{bucket}/o/{object}", "response": {"$ref": "Object"}}, "list": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_only", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"max-results": {"minimum": "0", "type": "integer", "location": "query", "format": "uint32"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}, "bucket": {"required": true, "type": "string", "location": "path"}, "prefix": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "delimiter": {"type": "string", "location": "query"}}, "response": {"$ref": "Objects"}, "httpMethod": "GET", "supportsSubscription": true, "path": "b/{bucket}/o", "id": "storage.objects.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "request": {"$ref": "Object"}, "response": {"$ref": "Object"}, "httpMethod": "PUT", "path": "b/{bucket}/o/{object}", "id": "storage.objects.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "request": {"$ref": "Object"}, "response": {"$ref": "Object"}, "httpMethod": "PATCH", "path": "b/{bucket}/o/{object}", "id": "storage.objects.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "path": "b/{bucket}/o/{object}", "id": "storage.objects.delete", "parameters": {"object": {"required": true, "type": "string", "location": "path"}, "bucket": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->buckets = new Google_BucketsServiceResource($this, $this->serviceName, 'buckets', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "request": {"$ref": "Bucket"}, "response": {"$ref": "Bucket"}, "httpMethod": "POST", "path": "b", "id": "storage.buckets.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_only", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "id": "storage.buckets.get", "httpMethod": "GET", "path": "b/{bucket}", "response": {"$ref": "Bucket"}}, "list": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_only", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"max-results": {"minimum": "0", "type": "integer", "location": "query", "format": "uint32"}, "pageToken": {"type": "string", "location": "query"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}, "projectId": {"required": true, "type": "string", "location": "query", "format": "uint64"}}, "id": "storage.buckets.list", "httpMethod": "GET", "path": "b", "response": {"$ref": "Buckets"}}, "update": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "request": {"$ref": "Bucket"}, "response": {"$ref": "Bucket"}, "httpMethod": "PUT", "path": "b/{bucket}", "id": "storage.buckets.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}, "projection": {"enum": ["full", "no_acl"], "type": "string", "location": "query"}}, "request": {"$ref": "Bucket"}, "response": {"$ref": "Bucket"}, "httpMethod": "PATCH", "path": "b/{bucket}", "id": "storage.buckets.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/devstorage.full_control", "https://www.googleapis.com/auth/devstorage.read_write"], "path": "b/{bucket}", "id": "storage.buckets.delete", "parameters": {"bucket": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));

  }
}

class Google_Bucket extends Google_Model {
  protected $__websiteType = 'Google_BucketWebsite';
  protected $__websiteDataType = '';
  public $website;
  public $kind;
  public $timeCreated;
  public $projectId;
  protected $__aclType = 'Google_BucketAccessControl';
  protected $__aclDataType = 'array';
  public $acl;
  protected $__defaultObjectAclType = 'Google_ObjectAccessControl';
  protected $__defaultObjectAclDataType = 'array';
  public $defaultObjectAcl;
  public $location;
  protected $__ownerType = 'Google_BucketOwner';
  protected $__ownerDataType = '';
  public $owner;
  public $id;
  public $selfLink;
  public function setWebsite(Google_BucketWebsite $website) {
    $this->website = $website;
  }
  public function getWebsite() {
    return $this->website;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTimeCreated($timeCreated) {
    $this->timeCreated = $timeCreated;
  }
  public function getTimeCreated() {
    return $this->timeCreated;
  }
  public function setProjectId($projectId) {
    $this->projectId = $projectId;
  }
  public function getProjectId() {
    return $this->projectId;
  }
  public function setAcl(/* array(Google_BucketAccessControl) */ $acl) {
    $this->assertIsArray($acl, 'Google_BucketAccessControl', __METHOD__);
    $this->acl = $acl;
  }
  public function getAcl() {
    return $this->acl;
  }
  public function setDefaultObjectAcl(/* array(Google_ObjectAccessControl) */ $defaultObjectAcl) {
    $this->assertIsArray($defaultObjectAcl, 'Google_ObjectAccessControl', __METHOD__);
    $this->defaultObjectAcl = $defaultObjectAcl;
  }
  public function getDefaultObjectAcl() {
    return $this->defaultObjectAcl;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setOwner(Google_BucketOwner $owner) {
    $this->owner = $owner;
  }
  public function getOwner() {
    return $this->owner;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_BucketAccessControl extends Google_Model {
  public $domain;
  public $bucket;
  public $kind;
  public $id;
  public $role;
  public $entityId;
  public $entity;
  public $email;
  public $selfLink;
  public function setDomain($domain) {
    $this->domain = $domain;
  }
  public function getDomain() {
    return $this->domain;
  }
  public function setBucket($bucket) {
    $this->bucket = $bucket;
  }
  public function getBucket() {
    return $this->bucket;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setRole($role) {
    $this->role = $role;
  }
  public function getRole() {
    return $this->role;
  }
  public function setEntityId($entityId) {
    $this->entityId = $entityId;
  }
  public function getEntityId() {
    return $this->entityId;
  }
  public function setEntity($entity) {
    $this->entity = $entity;
  }
  public function getEntity() {
    return $this->entity;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_BucketAccessControls extends Google_Model {
  protected $__itemsType = 'Google_BucketAccessControl';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_BucketAccessControl) */ $items) {
    $this->assertIsArray($items, 'Google_BucketAccessControl', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_BucketOwner extends Google_Model {
  public $entityId;
  public $entity;
  public function setEntityId($entityId) {
    $this->entityId = $entityId;
  }
  public function getEntityId() {
    return $this->entityId;
  }
  public function setEntity($entity) {
    $this->entity = $entity;
  }
  public function getEntity() {
    return $this->entity;
  }
}

class Google_BucketWebsite extends Google_Model {
  public $notFoundPage;
  public $mainPageSuffix;
  public function setNotFoundPage($notFoundPage) {
    $this->notFoundPage = $notFoundPage;
  }
  public function getNotFoundPage() {
    return $this->notFoundPage;
  }
  public function setMainPageSuffix($mainPageSuffix) {
    $this->mainPageSuffix = $mainPageSuffix;
  }
  public function getMainPageSuffix() {
    return $this->mainPageSuffix;
  }
}

class Google_Buckets extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_Bucket';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_Bucket) */ $items) {
    $this->assertIsArray($items, 'Google_Bucket', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_ObjectAccessControl extends Google_Model {
  public $domain;
  public $object;
  public $bucket;
  public $kind;
  public $id;
  public $role;
  public $entityId;
  public $entity;
  public $email;
  public $selfLink;
  public function setDomain($domain) {
    $this->domain = $domain;
  }
  public function getDomain() {
    return $this->domain;
  }
  public function setObject($object) {
    $this->object = $object;
  }
  public function getObject() {
    return $this->object;
  }
  public function setBucket($bucket) {
    $this->bucket = $bucket;
  }
  public function getBucket() {
    return $this->bucket;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setRole($role) {
    $this->role = $role;
  }
  public function getRole() {
    return $this->role;
  }
  public function setEntityId($entityId) {
    $this->entityId = $entityId;
  }
  public function getEntityId() {
    return $this->entityId;
  }
  public function setEntity($entity) {
    $this->entity = $entity;
  }
  public function getEntity() {
    return $this->entity;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_ObjectAccessControls extends Google_Model {
  protected $__itemsType = 'Google_ObjectAccessControl';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_ObjectAccessControl) */ $items) {
    $this->assertIsArray($items, 'Google_ObjectAccessControl', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_Objects extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_StorageObject';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $prefixes;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_StorageObject) */ $items) {
    $this->assertIsArray($items, 'Google_StorageObject', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPrefixes(/* array(Google_string) */ $prefixes) {
    $this->assertIsArray($prefixes, 'Google_string', __METHOD__);
    $this->prefixes = $prefixes;
  }
  public function getPrefixes() {
    return $this->prefixes;
  }
}

class Google_StorageObject extends Google_Model {
  public $kind;
  public $name;
  protected $__mediaType = 'Google_StorageObjectMedia';
  protected $__mediaDataType = '';
  public $media;
  public $bucket;
  public $contentEncoding;
  public $selfLink;
  protected $__ownerType = 'Google_StorageObjectOwner';
  protected $__ownerDataType = '';
  public $owner;
  public $cacheControl;
  protected $__aclType = 'Google_ObjectAccessControl';
  protected $__aclDataType = 'array';
  public $acl;
  public $id;
  public $contentDisposition;
  public $metadata;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setMedia(Google_StorageObjectMedia $media) {
    $this->media = $media;
  }
  public function getMedia() {
    return $this->media;
  }
  public function setBucket($bucket) {
    $this->bucket = $bucket;
  }
  public function getBucket() {
    return $this->bucket;
  }
  public function setContentEncoding($contentEncoding) {
    $this->contentEncoding = $contentEncoding;
  }
  public function getContentEncoding() {
    return $this->contentEncoding;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setOwner(Google_StorageObjectOwner $owner) {
    $this->owner = $owner;
  }
  public function getOwner() {
    return $this->owner;
  }
  public function setCacheControl($cacheControl) {
    $this->cacheControl = $cacheControl;
  }
  public function getCacheControl() {
    return $this->cacheControl;
  }
  public function setAcl(/* array(Google_ObjectAccessControl) */ $acl) {
    $this->assertIsArray($acl, 'Google_ObjectAccessControl', __METHOD__);
    $this->acl = $acl;
  }
  public function getAcl() {
    return $this->acl;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setContentDisposition($contentDisposition) {
    $this->contentDisposition = $contentDisposition;
  }
  public function getContentDisposition() {
    return $this->contentDisposition;
  }
  public function setMetadata($metadata) {
    $this->metadata = $metadata;
  }
  public function getMetadata() {
    return $this->metadata;
  }
}

class Google_StorageObjectMedia extends Google_Model {
  public $contentType;
  public $timeCreated;
  public $algorithm;
  public $length;
  public $link;
  public $hash;
  public $data;
  public function setContentType($contentType) {
    $this->contentType = $contentType;
  }
  public function getContentType() {
    return $this->contentType;
  }
  public function setTimeCreated($timeCreated) {
    $this->timeCreated = $timeCreated;
  }
  public function getTimeCreated() {
    return $this->timeCreated;
  }
  public function setAlgorithm($algorithm) {
    $this->algorithm = $algorithm;
  }
  public function getAlgorithm() {
    return $this->algorithm;
  }
  public function setLength($length) {
    $this->length = $length;
  }
  public function getLength() {
    return $this->length;
  }
  public function setLink($link) {
    $this->link = $link;
  }
  public function getLink() {
    return $this->link;
  }
  public function setHash($hash) {
    $this->hash = $hash;
  }
  public function getHash() {
    return $this->hash;
  }
  public function setData($data) {
    $this->data = $data;
  }
  public function getData() {
    return $this->data;
  }
}

class Google_StorageObjectOwner extends Google_Model {
  public $entityId;
  public $entity;
  public function setEntityId($entityId) {
    $this->entityId = $entityId;
  }
  public function getEntityId() {
    return $this->entityId;
  }
  public function setEntity($entity) {
    $this->entity = $entity;
  }
  public function getEntity() {
    return $this->entity;
  }
}
