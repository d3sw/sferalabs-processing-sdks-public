<?php

namespace SferalabsProcessingSDK;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResultWrapper
{
    /**
     * @var ResponseInterface|null
     */
    protected ?ResponseInterface $response;

    /**
     * @var Throwable|null
     */
    protected ?Throwable $exception;

    /**
     * Response constructor.
     * @param ResponseInterface|null $response
     * @param Throwable|null $exception
     */
    public function __construct(ResponseInterface $response = null, Throwable $exception = null)
    {
        $this->response = $response;
        $this->exception = $exception;
    }

    /**
     * @return array|null
     */
    public function getContents(): ?array
    {
        return $this->response ? @json_decode((string)$this->response->getBody(), true) : null;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return ($this->getContents()['data'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getExceptionMessage(): ?string
    {
        return $this->exception ? $this->exception->getMessage() : null;
    }

    /**
     * @return array|null
     */
    public function getExceptionBody(): ?string
    {
        if ($this->exception) {
            try {
                return $this->exception->getResponse()->getBody();
            } catch (Throwable $exception) {
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return (bool)$this->response;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if ($data = $this->getContents()) {
            return $data[$key] ?? null;
        }

        return null;
    }
}
