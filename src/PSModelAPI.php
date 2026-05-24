<?php

namespace SferalabsProcessingSDK;

/**
 * Processing Server Model API
 */
abstract class PSModelAPI extends BaseAPI
{
    const BASE_ACTION = 'model/';

    /**
     * @param string $model
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function exists(string $model, array $condition = [], array $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $condition
     * @param array $params
     * @param array $filters
     * @return ResultWrapper
     */
    public static function count(string $model, array $condition = [], array $params = [], array $filters = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'condition' => $condition,
            'params' => $params,
            'filters' => $filters
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function find(string $model, array $condition = [], array $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param string $pk
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function findByPk(string $model, string $pk, array $condition = [], $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'pk' => $pk,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $attributes
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function findByAttributes(string $model, array $attributes, array $condition = [], $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'attributes' => $attributes,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $condition
     * @param array $filters
     * @param array $params
     * @param array|null $sort
     * @param string|null $sortBy
     * @return ResultWrapper
     */
    public static function findAll(
        string $model,
        array  $condition = [],
        array  $filters = [],
        array  $params = [],
        array  $sort = null,
        string $sortBy = null
    ): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'condition' => $condition,
            'params' => $params,
            'sort' => $sort,
            'sort_by' => $sortBy,
            'filters' => $filters
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $pk
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function findAllByPk(string $model, array $pk, array $condition = [], $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'pk' => $pk,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $attributes
     * @return ResultWrapper
     */
    public static function findAllByAttributes(string $model, array $attributes): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'attributes' => $attributes
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param string|null $pk
     * @param array $attributes
     * @return ResultWrapper
     */
    public static function save(string $model, string $pk = null, array $attributes = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'pk' => $pk,
            'attributes' => $attributes
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param array $attributes
     * @param array $condition
     * @param array $params
     * @return ResultWrapper
     */
    public static function updateAll(string $model, array $attributes, array $condition = [], array $params = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'attributes' => $attributes,
            'condition' => $condition,
            'params' => $params
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param string $pk
     * @return ResultWrapper
     */
    public static function deleteByPk(string $model, string $pk): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'pk' => $pk
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param string $model
     * @param string $method
     * @param string|null $pk
     * @param array $args
     * @return ResultWrapper
     */
    public static function modelCall(string $model, string $method, string $pk = null, array $args = []): ResultWrapper
    {
        return self::sendPostRequest([
            'model' => $model,
            'method' => $method,
            'pk' => $pk,
            'args' => $args
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param int $id
     * @param array $data
     * @return ResultWrapper
     */
    public static function remoteAssetServerUpdate(int $id, array $data = []): ResultWrapper
    {
        return self::sendPostRequest([
            'id' => $id,
            'data' => $data
        ], self::BASE_ACTION . __FUNCTION__);
    }

    /**
     * @param $id
     * @param array $data
     * @return bool|mixed
     */
    public static function remoteAssetServerChangePassword(int $id, array $data = []): ResultWrapper
    {
        return self::sendPostRequest([
            'id' => $id,
            'data' => $data
        ], self::BASE_ACTION . __FUNCTION__);
    }

}
