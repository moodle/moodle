<?php

namespace Kevinrob\GuzzleCache;

use GuzzleHttp\Psr7\PumpStream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CacheEntry
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var \DateTime
     */
    protected $staleAt;

    /**
     * @var \DateTime
     */
    protected $staleIfErrorTo;

    /**
     * @var \DateTime
     */
    protected $staleWhileRevalidateTo;

    /**
     * @var \DateTime
     */
    protected $dateCreated;

    /**
     * Cached timestamp of staleAt variable.
     *
     * @var int
     */
    protected $timestampStale;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \DateTime $staleAt
     * @param \DateTime|null $staleIfErrorTo if null, detected with the headers (RFC 5861)
     * @param \DateTime|null $staleWhileRevalidateTo
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        \DateTime $staleAt,
        \DateTime $staleIfErrorTo = null,
        \DateTime $staleWhileRevalidateTo = null
    ) {
        $this->dateCreated = new \DateTime();

        $this->request = $request;
        $this->response = $response;
        $this->staleAt = $staleAt;

        $values = new KeyValueHttpHeader($response->getHeader('Cache-Control'));

        if ($staleIfErrorTo === null && $values->has('stale-if-error')) {
            $this->staleIfErrorTo = (new \DateTime(
                '@'.($this->staleAt->getTimestamp() + (int) $values->get('stale-if-error'))
            ));
        } else {
            $this->staleIfErrorTo = $staleIfErrorTo;
        }

        if ($staleWhileRevalidateTo === null && $values->has('stale-while-revalidate')) {
            $this->staleWhileRevalidateTo = new \DateTime(
                '@'.($this->staleAt->getTimestamp() + (int) $values->get('stale-while-revalidate'))
            );
        } else {
            $this->staleWhileRevalidateTo = $staleWhileRevalidateTo;
        }
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response
            ->withHeader('Age', $this->getAge());
    }

    /**
     * @return ResponseInterface
     */
    public function getOriginalResponse()
    {
        return $this->response;
    }

    /**
     * @return RequestInterface
     */
    public function getOriginalRequest()
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function isVaryEquals(RequestInterface $request)
    {
        if ($this->response->hasHeader('Vary')) {
            if ($this->request === null) {
                return false;
            }

            foreach ($this->getVaryHeaders() as $key => $value) {
                if (!$this->request->hasHeader($key)
                    && !$request->hasHeader($key)
                ) {
                    // Absent from both
                    continue;
                } elseif ($this->request->getHeaderLine($key)
                    == $request->getHeaderLine($key)
                ) {
                    // Same content
                    continue;
                }

                return false;
            }
        }

        return true;
    }

    /**
     * Get the vary headers that should be honoured by the cache.
     *
     * @return KeyValueHttpHeader
     */
    public function getVaryHeaders()
    {
        return new KeyValueHttpHeader($this->response->getHeader('Vary'));
    }

    /**
     * @return \DateTime
     */
    public function getStaleAt()
    {
        return $this->staleAt;
    }

    /**
     * @return bool
     */
    public function isFresh()
    {
        return !$this->isStale();
    }

    /**
     * @return bool
     */
    public function isStale()
    {
        return $this->getStaleAge() > 0;
    }

    /**
     * @return int positive value equal staled
     */
    public function getStaleAge()
    {
        // This object is immutable
        if ($this->timestampStale === null) {
            $this->timestampStale = $this->staleAt->getTimestamp();
        }

        return time() - $this->timestampStale;
    }

    /**
     * @return bool
     */
    public function serveStaleIfError()
    {
        return $this->staleIfErrorTo !== null
            && $this->staleIfErrorTo->getTimestamp() >= (new \DateTime())->getTimestamp();
    }

    /**
     * @return bool
     */
    public function staleWhileValidate()
    {
        return $this->staleWhileRevalidateTo !== null
            && $this->staleWhileRevalidateTo->getTimestamp() >= (new \DateTime())->getTimestamp();
    }

    /**
     * @return bool
     */
    public function hasValidationInformation()
    {
        return $this->response->hasHeader('Etag') || $this->response->hasHeader('Last-Modified');
    }

    /**
     * Time in seconds how long the entry should be kept in the cache
     *
     * This will not give the time (in seconds) that the response will still be fresh for
     * from the HTTP point of view, but an upper bound on how long it is necessary and
     * reasonable to keep the response in a cache (to re-use it or re-validate it later on).
     *
     * @return int TTL in seconds (0 = infinite)
     */
    public function getTTL()
    {
        if ($this->hasValidationInformation()) {
            // No TTL if we have a way to re-validate the cache
            return 0;
        }

        $ttl = 0;

        // Keep it when stale if error
        if ($this->staleIfErrorTo !== null) {
            $ttl = max($ttl, $this->staleIfErrorTo->getTimestamp() - time());
        }

        // Keep it when stale-while-revalidate
        if ($this->staleWhileRevalidateTo !== null) {
            $ttl = max($ttl, $this->staleWhileRevalidateTo->getTimestamp() - time());
        }

        // Keep it until it become stale
        $ttl = max($ttl, $this->staleAt->getTimestamp() - time());

        // Don't return 0, it's reserved for infinite TTL
        return $ttl !== 0 ? (int) $ttl : -1;
    }

    /**
     * @return int Age in seconds
     */
    public function getAge()
    {
        return time() - $this->dateCreated->getTimestamp();
    }

    public function __sleep()
    {
        // Stream/Resource can't be serialized... So we copy the content into an implementation of `Psr\Http\Message\StreamInterface`
        if ($this->response !== null) {
            $responseBody = (string)$this->response->getBody();
            $this->response = $this->response->withBody(
                new PumpStream(
                    new BodyStore($responseBody),
                    [
                        'size' => mb_strlen($responseBody),
                    ]
                )
            );
        }

        $requestBody = (string)$this->request->getBody();
        $this->request = $this->request->withBody(
            new PumpStream(
                new BodyStore($requestBody),
                [
                    'size' => mb_strlen($requestBody)
                ]
            )
        );

        return array_keys(get_object_vars($this));
    }

    public function __wakeup()
    {
        // We re-create the stream of the response
        if ($this->response !== null) {
            $this->response = $this->response
                ->withBody(
                    \GuzzleHttp\Psr7\Utils::streamFor((string) $this->response->getBody())
                );
        }
        $this->request = $this->request
            ->withBody(
                \GuzzleHttp\Psr7\Utils::streamFor((string) $this->request->getBody())
            );
    }

}
