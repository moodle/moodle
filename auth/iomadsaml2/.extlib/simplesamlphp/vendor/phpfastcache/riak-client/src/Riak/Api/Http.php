<?php

namespace Basho\Riak\Api;

use Basho\Riak\Api;
use Basho\Riak\ApiInterface;
use Basho\Riak\Bucket;
use Basho\Riak\Command;
use Basho\Riak\DataType\Counter;
use Basho\Riak\DataType\Map;
use Basho\Riak\DataType\Set;
use Basho\Riak\DataType\Hll;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\DataObject;
use Basho\Riak\Search\Doc;
use Basho\Riak\TimeSeries\Cell;

/**
 * Handles communications between end user app & Riak via Riak HTTP API using cURL
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Http extends Api implements ApiInterface
{
    // Header keys
    const VCLOCK_KEY = 'X-Riak-Vclock';
    const CONTENT_TYPE_KEY = 'Content-Type';
    const METADATA_PREFIX = 'X-Riak-Meta-';
    const LAST_MODIFIED_KEY = 'Last-Modified';

    // Content Types
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';

    const TS_API_PREFIX = '/ts/v1';

    /**
     * Request body to be sent
     *
     * @var string
     */
    protected $requestBody = '';

    /**
     * Request url the request is being sent to
     *
     * @var string
     */
    protected $requestURL = '';

    /**
     * Response headers returned from request
     *
     * @var array
     */
    protected $responseHeaders = [];

    /**
     * Response body returned from request
     *
     * @var string
     */
    protected $responseBody = '';

    /**
     * HTTP Status Code from response
     *
     * @var int
     */
    protected $statusCode = 0;

    /**
     * cURL connection handle
     *
     * @var null
     */
    protected $connection = null;

    /**
     * API path
     *
     * @var string
     */
    protected $path = '';

    /**
     * Query string
     *
     * @var string
     */
    protected $query = '';

    private $options = [];

    protected $headers = [];

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request . $this->requestBody;
    }

    public function closeConnection()
    {
        if ($this->connection) {
            curl_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Prepare request to be sent
     *
     * @param Command $command
     * @param Node $node
     *
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        if ($this->connection) {
            $this->resetConnection();
        }

        // call parent prepare method to setup object members
        parent::prepare($command, $node);

        // set the API path to be used
        $this->buildPath();

        // general connection preparation
        $this->prepareConnection();

        // request specific connection preparation
        $this->prepareRequest();

        return $this;
    }

    public function resetConnection()
    {
        $this->command = null;
        $this->options = [];
        $this->path = '';
        $this->query = '';
        $this->requestBody = '';
        $this->requestURL = '';
        $this->responseHeaders = [];
        $this->responseBody = '';

        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            curl_reset($this->connection);
        } else {
            curl_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Sets the API path for the command
     *
     * @return $this
     */
    protected function buildPath()
    {
        $bucket = null;
        $key = '';

        $bucket = $this->command->getBucket();

        $location = $this->command->getLocation();
        if (!empty($location) && $location instanceof Location) {
            $key = $location->getKey();
        }
        switch (get_class($this->command)) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'Basho\Riak\Command\Bucket\Store':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_JSON;
            case 'Basho\Riak\Command\Bucket\Fetch':
            case 'Basho\Riak\Command\Bucket\Delete':
                $this->path = sprintf('/types/%s/buckets/%s/props', $bucket->getType(), $bucket->getName());
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'Basho\Riak\Command\KVObject\Fetch':
                $this->headers['Accept'] = '*/*, multipart/mixed';
            case 'Basho\Riak\Command\KVObject\Store':
            case 'Basho\Riak\Command\KVObject\Delete':
                $this->path = sprintf('/types/%s/buckets/%s/keys/%s', $bucket->getType(), $bucket->getName(), $key);
                break;
            case 'Basho\Riak\Command\KVObject\Keys\Fetch':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_JSON;
                $this->path = sprintf('/types/%s/buckets/%s/keys', $bucket->getType(), $bucket->getName());
                break;
            case 'Basho\Riak\Command\DataType\Counter\Store':
            case 'Basho\Riak\Command\DataType\GSet\Store':
            case 'Basho\Riak\Command\DataType\Set\Store':
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'Basho\Riak\Command\DataType\Map\Store':
            case 'Basho\Riak\Command\DataType\Hll\Store':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_JSON;
            case 'Basho\Riak\Command\DataType\Counter\Fetch':
            case 'Basho\Riak\Command\DataType\Set\Fetch':
            case 'Basho\Riak\Command\DataType\Map\Fetch':
            case 'Basho\Riak\Command\DataType\Hll\Fetch':
                $this->path = sprintf('/types/%s/buckets/%s/datatypes/%s', $bucket->getType(), $bucket->getName(), $key);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'Basho\Riak\Command\Search\Index\Store':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_JSON;
            case 'Basho\Riak\Command\Search\Index\Fetch':
            case 'Basho\Riak\Command\Search\Index\Delete':
                $this->path = sprintf('/search/index/%s', $this->command);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'Basho\Riak\Command\Search\Schema\Store':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_XML;
            case 'Basho\Riak\Command\Search\Schema\Fetch':
                $this->path = sprintf('/search/schema/%s', $this->command);
                break;
            case 'Basho\Riak\Command\Search\Fetch':
                $this->path = sprintf('/search/query/%s', $this->command);
                break;
            case 'Basho\Riak\Command\MapReduce\Fetch':
                $this->headers[static::CONTENT_TYPE_KEY] = static::CONTENT_TYPE_JSON;
                $this->path = sprintf('/%s', $this->config['mapred_prefix']);
                break;
            case 'Basho\Riak\Command\Indexes\Query':
                $this->path = $this->createIndexQueryPath($bucket);
                break;
            case 'Basho\Riak\Command\Ping':
                $this->path = '/ping';
                break;
            case 'Basho\Riak\Command\Stats':
                $this->path = '/stats';
                break;
            case 'Basho\Riak\Command\KVObject\FetchPreflist':
                $this->path = sprintf('/types/%s/buckets/%s/keys/%s/preflist', $bucket->getType(), $bucket->getName(), $key);
                break;
            case 'Basho\Riak\Command\TimeSeries\Fetch':
            case 'Basho\Riak\Command\TimeSeries\Delete':
                /** @var $command Command\TimeSeries\Fetch */
                $command = $this->command;
                $this->path = sprintf('%s/tables/%s/keys/%s', static::TS_API_PREFIX, $command->getTable(), implode("/", $command->getData()));
                break;
            case 'Basho\Riak\Command\TimeSeries\Store':
                /** @var $command Command\TimeSeries\Store */
                $command = $this->command;
                $this->path = sprintf('%s/tables/%s/keys', static::TS_API_PREFIX, $command->getTable());
                break;
            case 'Basho\Riak\Command\TimeSeries\Query\Fetch':
                $this->path = sprintf('%s/query', static::TS_API_PREFIX);
                break;
            default:
                $this->path = '';
        }

        return $this;
    }

    /**
     * Generates the URL path for a 2i Query
     *
     * @param Bucket $bucket
     * @return string
     * @throws Api\Exception if 2i query is invalid.
     */
    private function createIndexQueryPath(Bucket $bucket)
    {
        /**  @var Command\Indexes\Query $command */
        $command = $this->command;

        if($command->isMatchQuery()) {
            $path =  sprintf('/types/%s/buckets/%s/index/%s/%s', $bucket->getType(),
                        $bucket->getName(),
                        $command->getIndexName(),
                        $command->getMatchValue());
        }
        elseif($command->isRangeQuery()) {
            $path =  sprintf('/types/%s/buckets/%s/index/%s/%s/%s', $bucket->getType(),
                        $bucket->getName(),
                        $command->getIndexName(),
                        $command->getLowerBound(),
                        $command->getUpperBound());
        }
        else
        {
            throw new Api\Exception("Invalid Secondary Index Query.");
        }

        return $path;
    }

    /**
     * Prepare Connection
     *
     * Sets general connection options that are used with every request
     *
     * @return $this
     * @throws Api\Exception
     */
    protected function prepareConnection()
    {
        // record outgoing headers
        $this->options[CURLINFO_HEADER_OUT] = 1;

        if ($this->command->getConnectionTimeout()) {
            $this->options[CURLOPT_TIMEOUT] = $this->command->getConnectionTimeout();
        }

        if ($this->node->useTls()) {
            // CA File
            if ($this->node->getCaFile()) {
                $this->options[CURLOPT_CAINFO] = $this->node->getCaFile();
            } elseif ($this->node->getCaDirectory()) {
                $this->options[CURLOPT_CAPATH] = $this->node->getCaDirectory();
            } else {
                throw new Api\Exception('A Certificate Authority file is required for secure connections.');
            }

            // verify CA file
            $this->options[CURLOPT_SSL_VERIFYPEER] = true;

            // verify host common name
            $this->options[CURLOPT_SSL_VERIFYHOST] = 0;

            if ($this->node->getUserName()) {
                $this->options[CURLOPT_USERPWD] = sprintf('%s:%s', $this->node->getUserName(),
                    $this->node->getPassword());
            } elseif ($this->node->getCertificate()) {
                /*
                 * NOT CURRENTLY SUPPORTED ON HTTP
                 *
                $this->options[CURLOPT_SSLCERT] = $this->node->getCertificate();
                $this->options[CURLOPT_SSLCERTTYPE] = 'P12';
                if ($this->node->getCertificatePassword()) {
                    $this->options[CURLOPT_SSLCERTPASSWD] = $this->node->getCertificatePassword();
                }
                if ($this->node->getPrivateKey()) {
                    $this->options[CURLOPT_SSLKEY] = $this->node->getPrivateKey();
                    if ($this->node->getPrivateKeyPassword()) {
                        $this->options[CURLOPT_SSLKEYPASSWD] = $this->node->getPrivateKeyPassword();
                    }
                }
                */
            }
        }

        return $this;
    }

    /**
     * Prepare request
     *
     * Sets connection options that are specific to this request
     *
     * @return $this
     */
    protected function prepareRequest()
    {
        return $this->prepareRequestMethod()
            ->prepareRequestHeaders()
            ->prepareRequestParameters()
            ->prepareRequestUrl()
            ->prepareRequestData();
    }

    /**
     * Prepare request data
     *
     * @return $this
     */
    protected function prepareRequestData()
    {
        // if POST or PUT, add parameters to post data, else add to uri
        if (in_array($this->command->getMethod(), ['POST', 'PUT'])) {
            $this->requestBody = $this->command->getEncodedData();
            $this->options[CURLOPT_POSTFIELDS] = $this->requestBody;
        }

        return $this;
    }

    /**
     * Prepares the complete request URL
     *
     * @return $this
     */
    protected function prepareRequestUrl()
    {
        $protocol = $this->node->useTls() ? 'https' : 'http';
        $this->requestURL = sprintf('%s://%s%s?%s', $protocol, $this->node->getUri(), $this->path, $this->query);

        // set the built request URL on the connection
        $this->options[CURLOPT_URL] = $this->requestURL;

        return $this;
    }

    /**
     * Prepare request parameters
     *
     * @return $this
     */
    protected function prepareRequestParameters()
    {
        if ($this->command->hasParameters()) {
            // build query using RFC 3986 (spaces become %20 instead of '+')
            $this->query = http_build_query($this->command->getParameters(), '', '&', PHP_QUERY_RFC3986);
        }

        return $this;
    }

    /**
     * Prepares the request headers
     *
     * @return $this
     */
    protected function prepareRequestHeaders()
    {
        $curl_headers = [];

        foreach ($this->headers as $key => $value) {
            $curl_headers[] = sprintf('%s: %s', $key, $value);
        }

        // if we have an object, set appropriate object headers
        $object = $this->command->getObject();
        if ($object) {
            if ($object->getVclock()) {
                $curl_headers[] = sprintf('%s: %s', static::VCLOCK_KEY, $object->getVclock());
            }

            if ($object->getContentType()) {
                $charset = '';
                if ($object->getCharset()) {
                    $charset = sprintf('; charset=%s', $object->getCharset());
                }
                $curl_headers[] = sprintf('%s: %s', static::CONTENT_TYPE_KEY, $object->getContentType(), $charset);
            }

            // setup index headers
            $translator = new Api\Http\Translator\SecondaryIndex();
            $indexHeaders = $translator->createHeadersFromIndexes($object->getIndexes());
            foreach ($indexHeaders as $value) {
                $curl_headers[] = sprintf('%s: %s', $value[0], $value[1]);
            }

            // setup metadata headers
            foreach($object->getMetaData() as $key => $value) {
                $curl_headers[] = sprintf('%s%s: %s', static::METADATA_PREFIX, $key, $value);
            }
        }

        // set the request headers on the connection
        $this->options[CURLOPT_HTTPHEADER] = $curl_headers;

        // dump local headers to start fresh
        $this->headers = [];

        return $this;
    }

    /**
     * Prepare the request method
     *
     * @return $this
     */
    protected function prepareRequestMethod()
    {
        switch ($this->command->getMethod()) {
            case "POST":
                $this->options[CURLOPT_POST] = 1;
                break;
            case "PUT":
                $this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
            case "DELETE":
                $this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            case "HEAD":
                $this->options[CURLOPT_NOBODY] = 1;
                break;
            default:
                // reset http method to get in case its changed
                $this->options[CURLOPT_HTTPGET] = 1;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return bool
     */
    public function send()
    {
        // set the response header and body callback functions
        $this->options[CURLOPT_HEADERFUNCTION] = [$this, 'responseHeaderCallback'];
        $this->options[CURLOPT_WRITEFUNCTION] = [$this, 'responseBodyCallback'];
        if ($this->command->isVerbose()) {
            // set curls output to be the output buffer stream
            $this->options[CURLOPT_STDERR] = fopen('php://stdout', 'w+');
            $this->options[CURLOPT_VERBOSE] = 1;

            // there is a bug when verbose is enabled, header out causes no output
            // @see https://bugs.php.net/bug.php?id=65348
            unset($this->options[CURLINFO_HEADER_OUT]);

            echo "cURL Command:\n\tcurl -X{$this->command->getMethod()} {$this->requestURL} --data \"{$this->requestBody}\"\n";
        }

        // set all options on the resource
        curl_setopt_array($this->getConnection(), $this->options);

        // execute the request
        $this->success = curl_exec($this->getConnection());
        if ($this->success === false) {
            $this->error = curl_error($this->getConnection());
        } elseif ($this->success === true) {
            $this->error = '';
        }

        $this->request = curl_getinfo($this->getConnection(), CURLINFO_HEADER_OUT);

        // set the response http code
        $this->statusCode = curl_getinfo($this->getConnection(), CURLINFO_HTTP_CODE);

        $this->parseResponse();

        return $this->success;
    }

    /**
     * Add a custom header to the request
     *
     * @param $key
     * @param $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @return resource
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->openConnection();
        }

        return $this->connection;
    }

    public function openConnection()
    {
        $this->connection = curl_init();

        return $this;
    }

    /**
     * Response header callback
     *
     * Handles callback from curl when the response is received, it parses the headers into an array sets them as
     * member of the class.
     *
     * Has to be public for curl to be able to access it.
     *
     * @param $ch
     * @param $header
     *
     * @return int
     */
    public function responseHeaderCallback($ch, $header)
    {
        if (strpos($header, ':')) {
            list ($key, $value) = explode(':', $header, 2);

            $value = trim($value);

            if (!empty($value)) {
                if (!isset($this->responseHeaders[$key])) {
                    $this->responseHeaders[$key] = $value;
                } elseif (is_array($this->responseHeaders[$key])) {
                    $this->responseHeaders[$key] = array_merge($this->responseHeaders[$key], [$value]);
                } else {
                    $this->responseHeaders[$key] = array_merge([$this->responseHeaders[$key]], [$value]);
                }
            }
        }

        return strlen($header);
    }

    /**
     * Response body callback
     *
     * Handles callback from curl when the response is received, it sets the response body as a member of the class.
     *
     * Has to be public for curl to be able to access it.
     *
     * @param $ch
     * @param $body
     *
     * @return int
     */
    public function responseBodyCallback($ch, $body)
    {
        $this->responseBody .= $body;
        return strlen($body);
    }

    protected function parseResponse()
    {
        // trim leading / trailing whitespace
        $body = $this->responseBody;
        $location = null;
        if ($this->getResponseHeader('Location')) {
            $location = Location::fromString($this->getResponseHeader('Location'));
        }

        if ($this->statusCode == 500) {
            $this->success = false;
            $this->error = $body;
        }

        switch (get_class($this->command)) {
            case 'Basho\Riak\Command\Bucket\Store':
            case 'Basho\Riak\Command\Bucket\Fetch':
                $bucket = null;
                $modified = $this->getResponseHeader(static::LAST_MODIFIED_KEY, '');
                $properties = json_decode($body, true);
                if (isset($properties['props']) && $this->command->getBucket()) {
                    $bucket = new Bucket($this->command->getBucket()->getName(), $this->command->getBucket()->getType(), $properties['props']);
                }
                $response = new Command\Bucket\Response($this->success, $this->statusCode, $this->error, $bucket, $modified);
                break;

            case 'Basho\Riak\Command\KVObject\Fetch':
            case 'Basho\Riak\Command\KVObject\Store':
                /** @var Command\KVObject $command */
                $command = $this->command;
                $objects = (new Api\Http\Translator\ObjectResponse($command, $this->statusCode))
                    ->parseResponse($body, $this->responseHeaders);
                $response = new Command\KVObject\Response($this->success, $this->statusCode, $this->error, $location, $objects);
                break;

            case 'Basho\Riak\Command\KVObject\FetchPreflist':
                $response = new Command\KVObject\Response($this->success, $this->statusCode, $this->error, $location, [new DataObject(json_decode($body))]);
                break;

            case 'Basho\Riak\Command\KVObject\Keys\Fetch':
                $data = json_decode($body);
                $keys = [];
                if ($data && isset($data->keys)) {
                    foreach ($data->keys as $key) {
                        $keys[] = new Location($key, $this->command->getBucket());
                    }
                }
                $response = new Command\KVObject\Keys\Response($this->success, $this->statusCode, $this->error, $keys);
                break;

            case 'Basho\Riak\Command\DataType\Counter\Store':
            case 'Basho\Riak\Command\DataType\Counter\Fetch':
                $counter = null;
                $json_object = json_decode($body);
                if ($json_object && isset($json_object->value)) {
                    $counter = new Counter($json_object->value);
                }
                $response = new Command\DataType\Counter\Response(
                    $this->success, $this->statusCode, $this->error, $location, $counter, $this->getResponseHeader('Date')
                );
                break;

            case 'Basho\Riak\Command\DataType\GSet\Store':
            case 'Basho\Riak\Command\DataType\Set\Store':
            case 'Basho\Riak\Command\DataType\Set\Fetch':
                $set = null;
                $json_object = json_decode($body);
                if ($json_object && isset($json_object->value)) {
                    $context = '';
                    if (isset($json_object->context)) {
                        $context = $json_object->context;
                    }
                    $set = new Set($json_object->value, $context);
                }
                $response = new Command\DataType\Set\Response(
                    $this->success, $this->statusCode, $this->error, $location, $set, $this->getResponseHeader('Date')
                );
                break;

            case 'Basho\Riak\Command\DataType\Map\Store':
            case 'Basho\Riak\Command\DataType\Map\Fetch':
                $map = null;
                $json_object = json_decode($body, true);
                if ($json_object && isset($json_object['value'])) {
                    $map = new Map($json_object['value'], $json_object['context']);
                }
                $response = new Command\DataType\Map\Response(
                    $this->success, $this->statusCode, $this->error, $location, $map, $this->getResponseHeader('Date')
                );
                break;

            case 'Basho\Riak\Command\DataType\Hll\Store':
            case 'Basho\Riak\Command\DataType\Hll\Fetch':
                $hll = null;
                $json_object = json_decode($body);
                if ($json_object && isset($json_object->value)) {
                    $hll = new Hll($json_object->value);
                }
                $response = new Command\DataType\Hll\Response(
                    $this->success, $this->statusCode, $this->error, $location, $hll, $this->getResponseHeader('Date')
                );
                break;

            case 'Basho\Riak\Command\Search\Fetch':
                $results = in_array($this->statusCode, [200,204]) ? json_decode($body) : null;
                $docs = [];
                if (!empty($results->response->docs)) {
                    foreach ($results->response->docs as $doc) {
                        $docs[] = new Doc($doc);
                    }
                }
                $numFound = !empty($results->response->numFound) ? $results->response->numFound : 0;

                $response = new Command\Search\Response($this->success, $this->statusCode, $this->error, $numFound, $docs);
                break;
            case 'Basho\Riak\Command\Search\Index\Store':
            case 'Basho\Riak\Command\Search\Index\Fetch':
                $index = json_decode($body);
                $response = new Command\Search\Index\Response($this->success, $this->statusCode, $this->error, $index);
                break;

            case 'Basho\Riak\Command\Search\Schema\Store':
            case 'Basho\Riak\Command\Search\Schema\Fetch':
                $response = new Command\Search\Schema\Response(
                    $this->success, $this->statusCode, $this->error, $body, $this->getResponseHeader(static::CONTENT_TYPE_KEY)
                );
                break;

            case 'Basho\Riak\Command\MapReduce\Fetch':
                $results = in_array($this->statusCode, [200,204]) ? json_decode($body) : null;
                $response = new Command\MapReduce\Response($this->success, $this->statusCode, $this->error, $results);
                break;
            case 'Basho\Riak\Command\Indexes\Query':
                $json_object = in_array($this->statusCode, [200,204]) ? json_decode($body, true) : null;
                $results = [];
                $termsReturned = false;
                $continuation = null;
                $done = true;

                if (isset($json_object['keys'])) {
                    $results = $json_object['keys'];
                }

                if (isset($json_object['results'])) {
                    $results = $json_object['results'];
                    $termsReturned = true;
                }

                if (isset($json_object['continuation'])) {
                    $continuation = $json_object['continuation'];
                    $done = false;
                }

                $response = new Command\Indexes\Response(
                    $this->success, $this->statusCode, $this->error, $results, $termsReturned, $continuation, $done, $this->getResponseHeader('Date')
                );
                break;
            case 'Basho\Riak\Command\Stats':
                $response = new Command\Stats\Response($this->success, $this->statusCode, $this->error, json_decode($body, true));
                break;
            case 'Basho\Riak\Command\TimeSeries\Fetch':
                $row = in_array($this->statusCode, ['200','201','204']) ? json_decode($body, true) : [];
                $response = new Command\TimeSeries\Response($this->success, $this->statusCode, $this->error, [$row]);
                break;
            case 'Basho\Riak\Command\TimeSeries\Query\Fetch':
                $results = in_array($this->statusCode, ['200','204']) ? json_decode($body) : [];
                $rows = [];
                if (isset($results->rows)) {
                    foreach ($results->rows as $row) {
                        $cells = [];
                        foreach ($results->columns as $index => $column) {
                            $cells[$column] = $row[$index];
                        }
                        $rows[] = $cells;
                    }
                }
                $response = new Command\TimeSeries\Query\Response($this->success, $this->statusCode, $this->error, $rows);
                break;
            case 'Basho\Riak\Command\TimeSeries\Store':
            case 'Basho\Riak\Command\TimeSeries\Delete':
            case 'Basho\Riak\Command\KVObject\Delete':
            case 'Basho\Riak\Command\Bucket\Delete':
            case 'Basho\Riak\Command\Search\Index\Delete':
            case 'Basho\Riak\Command\Ping':
            default:
                $response = new Command\Response($this->success, $this->statusCode, $this->error);
                break;
        }

        $this->response = $response;
    }

    protected function getResponseHeader($key, $default = '')
    {
        if (!empty($this->responseHeaders[$key])) {
            return $this->responseHeaders[$key];
        } else {
            return $default;
        }
    }
}
