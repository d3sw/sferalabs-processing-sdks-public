<?php

namespace SferalabsProcessingSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

/**
 * All communications with ProcessingServers should be done via this class interface.
 * Class ProcessingServersAPI
 */
abstract class BaseAPI
{
    /**
     * @var $client Client|null
     */
    public static ?Client $client = null;

    /**
     * @return string
     */
    abstract static function getApiEndpoint(): string;

    /**
     * @param \Throwable $e
     */
    abstract static function handleException(\Throwable $e);
    //ProcessingServers::clearAPIEndpointCache();
    //Log

    /**
     * @return int
     */
    protected static function getProcessingLocalMinTimeout(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    protected static function getRequestTries(): int
    {
        return 2;
    }

    /**
     * @return float
     */
    protected static function getTimeOut(): float
    {
        return 5.0;
    }

    /**
     * @param array $data
     * @return array
     */
    protected static function dataInterceptor(array $data)
    {
        //extend it to add vendor_id
        return $data;
    }

    /**
     * @return Client
     */
    protected static function getClient(): Client
    {
        if (!self::$client) {
            self::$client = new Client(['verify' => false]);
        }

        return self::$client;
    }

    /**
     * @param array $data
     * @param string $action
     * @param float|null $timeout
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    protected static function sendPostRequest(array $data, string $action, float $timeout = null, bool $postAsForm = true)
    {
        return self::sendRequestHandler($data, $action, $timeout, true, $postAsForm);
    }

    /**
     * @param array $data
     * @param string $action
     * @param float|null $timeout
     * @return ResultWrapper
     */
    protected static function sendGetRequest(array $data, string $action, float $timeout = null)
    {
        return self::sendRequestHandler($data, $action, $timeout, false);
    }

    /**
     * @param array $data
     * @param string $action
     * @param float|null $timeout
     * @param bool $post
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    public static function sendRequestHandler(array $data, string $action, float $timeout = null,
                                              bool  $post = true, bool $postAsForm = false): ResultWrapper
    {
        $data = static::dataInterceptor($data);
        $currentTry = 0;

        do {
            if ($currentTry > 0) {
                sleep(1);
            }
            try {
                $response = self::sendRequest($data, $action, $timeout ?? static::getTimeOut(), $post, $postAsForm);

                return new ResultWrapper($response);
            } catch (\Throwable $e) {
                static::handleException($e);
            }

            $currentTry++;
        } while ($e instanceof ServerException and $currentTry <= static::getRequestTries());

        return new ResultWrapper(null, $e);
    }

    /**
     * @param array $data
     * @param string $action
     * @param float $timeout
     * @param bool $post
     * @param bool $postAsForm
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public static function sendRequest(array $data, string $action, float $timeout, bool $post, bool $postAsForm): ?ResponseInterface
    {
        $action = '/api/' . $action;
        $endpoint = static::getApiEndpoint();

        $localMinTimeout = static::getProcessingLocalMinTimeout();
        if ($localMinTimeout && $timeout < $localMinTimeout) {
            $timeout = $localMinTimeout;
        }

        if ($post) {
            $params = [
                'timeout' => $timeout,  // in seconds
                //'connect_timeout' => 1.5
            ];

            if ($postAsForm) {
                $params['form_params'] = $data;
            } else {
                $params['body'] = json_encode($data);
            }

            $response = static::getClient()->post(rtrim($endpoint, '/') . $action, $params);
        } else {
            $uri = rtrim($endpoint, '/') . $action;
            if ($data) {
                $uri .= (strpos($uri, '?') === false ? '?' : '&') . http_build_query($data);
            }

            $response = static::getClient()->get($uri, [
                'timeout' => $timeout,    // in seconds
            ]);
        }

        return $response;
    }
}
