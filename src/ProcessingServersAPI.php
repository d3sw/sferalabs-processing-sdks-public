<?php

namespace SferalabsProcessingSDK;

/**
 * All communications with ProcessingServers should be done via this class interface.
 * Class ProcessingServersAPI
 */
abstract class ProcessingServersAPI extends BaseAPI
{
    // Remote Asset Server (RAS) API calls
    const ACTION_RAS_UPDATE = 'ras/update';
    const ACTION_RAS_UPLOAD = 'ras/upload';
    const ACTION_RAS_DOWNLOAD = 'ras/download';
    const ACTION_RAS_CHECK_SECRET_KEY = 'ras/checkSecretKey';
    const ACTION_RAS_CHECK_DOWNLOAD_KEY = 'ras/checkDownloadKey';
    const ACTION_RAS_LIST = 'ras/ls';
    const ACTION_RAW_RAS_LIST = 'ras/rawLs';
    const ACTION_RAS_SEARCH = 'ras/search';
    const ACTION_RAS_RAW_SEARCH = 'ras/rawSearch';
    const ACTION_RAS_REMOVE = 'ras/rm';
    const ACTION_RAS_MOVE = 'ras/move';
    const ACTION_RAS_POLICY = 'ras/policy';
    const ACTION_RAS_SIGN_STRING = 'ras/signString';
    const ACTION_RAS_S3_INFO = 'ras/s3Info';
    const ACTION_RAS_SIGNATURE_V4 = 'ras/signatureV4';
    const ACTION_RAS_MK_DIR = 'ras/mkDir';
    const ACTION_RAS_WHITE_LIST = 'ras/whiteList';
    const ACTION_RAS_REAL_NAME = 'ras/realName';
    const ACTION_RAS_CLEAR_TREE_CACHE = 'ras/clearTreeCache';

    // VAD (voice activity detection)
    const ACTION_VAD_GET_TIMINGS = 'vad/getTimings';

    // getting audio URI from DAT file
    private const ACTION_AUDIO_GET_SIGNED_URL = 'audio/getSignedUrl';

    // Extract Video Frames (voice activity detection)
    const ACTION_EXTRACT_FRAMES_GET_ZIP = 'extractFrames/getZip';

    // Movie Script Conversion api call
    const ACTION_SCRIPT_TO_DFXP = 'convertSubtitle/scriptToDfxp';

    // Image Subtitle Export (add to queue)
    const ACTION_IMAGE_SUBTITLE_EXPORT = 'imageSubtitleExport/queue';
    // Image Subtitle Export (get status)
    const ACTION_IMAGE_SUBTITLE_EXPORT_STATUS = 'imageSubtitleExport/status';

    // Delete video
    const ACTION_GET_VIDEO_TO_DELETE = 'video/getVideoToDelete';

    // Delete video
    const ACTION_DELETE_VIDEO_ASSET = 'video/deleteVideoAsset';

    const ACTION_VERIFY_RTMP = 'video/verifyRtmp';

    const ACTION_CONVERT_PROCESSOR_PARAMETERS_VIEW = 'ras/convertProcessorParametersView';

    const ACTION_SST_EXPORT_PREVIEW = 'imageSubtitleExport/SSTPreview';
    const ACTION_BDN_EXPORT_PREVIEW = 'imageSubtitleExport/BDNPreview';

    // Batch File Convert (add to queue)
    const ACTION_BATCH_FILE_TASK = 'batchFileConvert/queue';
    //  Batch File Convert (get status)
    const ACTION_BATCH_FILE_CONVERT_STATUS = 'batchFileConvert/status';
    // Get Remote Processing Server Id by Host
    const ACTION_GET_REMOTE_PROCESSING_SERVER_ID_BY_HOST = 'remoteAssetServer/getServerIdByHost';
    // Get Server lists
    const ACTION_GET_SERVER_LIST = 'remoteAssetServer/getServerList';
    // Add task or batch of tasks
    const ACTION_ADD_TASK = 'task/add';
    // Netflix package verification
    const ACTION_NETFLIX_PACKAGE_VERIFY = 'netflixPackageVerify/fullPackage';

    // Verify IP
    const ACTION_VERIFY_IP = 'ras/verifyIp';

    // Aspera Node Download Setup
    const ACTION_ASPERA_NODE_DOWNLOAD_SETUP = 'asperaNode/api/downloadSetup';

    // Media Seal generate a new password
    const ACTION_MEDIA_SEAL_GENERATE_PASSWORD = 'mediaSeal/api/generatePassword';

    // Get S3 signed url
    const ACTION_S3SIGNED_URL = 'ras/signedUrl';

    // Action custom options list
    const ACTION_CUSTOM_OPTIONS_LIST = 'customOptions/list';

    const ACTION_GET_FILE_TEMPORARY_ACCESS = 'temporaryAccess/getTempAccess';

    const ACTION_MOVE_OLD_TASKS = 'task/moveOldTasks';
    const ACTION_PERFORMANCE_STATISTICS = 'task/getPerformanceStatistics';

    /**
     * @return string
     */
    protected static function getTaskMode(): string
    {
        return 'V2';
    }

    /**
     * @return string|null
     */
    protected static function resolveUserIp(): ?string
    {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
    }

    /**
     * @param $months int
     * @return bool|mixed
     */
    public static function callMoveOldTasks(int $months = 2): ?string
    {
        if ($months >= 1) {
            return self::sendGetRequest(['months' => $months], self::ACTION_MOVE_OLD_TASKS);
        }

        return null;
    }

    /**
     * Return array where key "id" -  its remote processing server id (integer)
     * @param string $hostName
     * @param string $type
     * @return ResultWrapper
     */
    public static function getRemoteProcessingServerId(string $hostName, string $type): ResultWrapper
    {
        return self::sendGetRequest(['host' => $hostName, 'type' => $type], self::ACTION_GET_REMOTE_PROCESSING_SERVER_ID_BY_HOST);
    }

    /**
     * Get remote asset servers list
     * @return ResultWrapper
     */
    public static function getRemoteAssetServerList(): ResultWrapper
    {
        return self::sendGetRequest([], self::ACTION_GET_SERVER_LIST);
    }


    /**
     * Return array where key "id" - its string of tasks id's separated by ", "
     * @param array $tasksData You need have task data with keys - type and data.
     *                         You can add more then one task. For this way use array in array structure
     *                         If you want add a children task to current task -  use "children" key in the task
     * @param string $version V1-current processing, V2-batch jobs
     * @param int $vendorId
     * @return ResultWrapper
     * @author Igor S <igor.skakovskiy@sferastudios.com>
     *
     */
    public static function addTask(array $tasksData, string $version = 'V2', int $vendorId = 0): ResultWrapper
    {
        $version = ($version ?? static::getTaskMode());

        self::refineTasks($tasksData, $version, $vendorId);

        return self::sendPostRequest($tasksData, self::ACTION_ADD_TASK, 25, false);
    }

    /**
     * Get Temporary Access on Files stored on video bucket. For now only for WAV
     * @param string $path
     * @param boolean $decodeJson
     * @return bool|mixed
     * @author Igor S <igor.skakovskiy.contractor@bydeluxe.com>
     *
     */
    public static function getFileTempAccess(string $path): ResultWrapper
    {
        return self::sendPostRequest(array(
            'path' => $path
        ), self::ACTION_GET_FILE_TEMPORARY_ACCESS);
    }

    /**
     * @param $key
     * @return ResultWrapper
     */
    public static function rasCheckSecretKey(string $key): ResultWrapper
    {
        return self::sendPostRequest(array(
            'key' => $key
        ), self::ACTION_RAS_CHECK_SECRET_KEY);
    }

    /**
     * @param $key
     * @return ResultWrapper
     */
    public static function rasCheckDownloadKey(string $key): ResultWrapper
    {
        return self::sendPostRequest(array(
            'key' => $key
        ), self::ACTION_RAS_CHECK_DOWNLOAD_KEY);
    }

    /**
     * @param string $remoteServerId
     * @param string $path
     * @param bool $recursive
     * @param float $timeout
     * @param false $useTreeCache
     * @param string|null $search
     * @return ResultWrapper
     */
    public static function rasLs(string $remoteServerId, string $path = '/', bool $recursive = true, float $timeout = 20,
                                 bool   $useTreeCache = false, string $search = null): ResultWrapper
    {
        return self::sendPostRequest([
            'host' => $remoteServerId,
            'path' => $path,
            'recursive' => $recursive,
            'useTreeCache' => $useTreeCache,
            'search' => $search
        ], self::ACTION_RAS_LIST, $timeout);
    }

    /**
     * @param $remoteServerId
     * @param string $path
     * @param bool $recursive
     * @param int $timeout
     * @return ResultWrapper
     */
    public static function rawRasLs(int $remoteServerId, string $path = '/', bool $recursive = true, int $timeout = 20): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'path' => $path,
            'recursive' => $recursive
        ), self::ACTION_RAW_RAS_LIST, $timeout, false);
    }

    /**
     * @param string $remoteServerId
     * @param string $search
     * @param string $path
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function rasSearch(string $remoteServerId, string $search = '', string $path = '/', float $timeout = 60): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'search' => $search,
            'path' => $path
        ), self::ACTION_RAS_SEARCH, $timeout);
    }

    /**
     * @param int $remoteServerId
     * @param string $search
     * @param string $path
     * @param int $timeout
     * @return ResultWrapper
     */
    public static function rasRawSearch(int $remoteServerId, string $search = '', string $path = '/', int $timeout = 60): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'search' => $search,
            'path' => $path
        ), self::ACTION_RAS_RAW_SEARCH, $timeout, false);
    }

    /**
     * @param string $remoteServerId
     * @param string $path
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function rasRm(string $remoteServerId, string $path, float $timeout = 5): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'path' => $path
        ), self::ACTION_RAS_REMOVE, $timeout);
    }

    /**
     * @param string $remoteServerId
     * @param string $path
     * @param string $pathTo
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function rasMove(string $remoteServerId, string $path, string $pathTo, float $timeout = 10): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'path' => $path,
            'pathTo' => $pathTo
        ), self::ACTION_RAS_MOVE, $timeout);
    }

    /**
     * @param string $remoteServerId
     * @param string $path
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function rasMkDir(string $remoteServerId, string $path, float $timeout = 5): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
            'path' => $path
        ), self::ACTION_RAS_MK_DIR, $timeout);
    }

    /**
     * @param $remoteServerId
     * @param $data
     * @return ResultWrapper
     */
    public static function rasPolicy(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_POLICY);
    }

    /**
     * @param $remoteServerId
     * @param $data
     * @return ResultWrapper
     */
    public static function rasSignString(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_SIGN_STRING);
    }

    /**
     * @param $remoteServerId
     * @param $data
     * @return ResultWrapper
     */
    public static function rasS3Info(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_S3_INFO);
    }

    /**
     * @param $remoteServerId
     * @param $data
     * @return ResultWrapper
     */
    public static function rasSignatureV4(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_SIGNATURE_V4);
    }

    /**
     * Update remote_asset_servers table with data.
     * Mainly used to set and encrypt password.
     * @param string $remoteServerId
     * @param array $data
     *  scenario
     *  [any RemoteAssetServer model attributes]
     * @return bool|mixed
     */
    public static function rasUpdate(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_UPDATE, true, 10);
    }

    /**
     * @param string $remoteServerId
     * @param array $data
     * @return ResultWrapper
     */
    public static function rasUpload(string $remoteServerId, array $data): ResultWrapper
    {
        return self::sendPostRequest(array_merge(array(
            'remote_server_id' => $remoteServerId
        ), $data), self::ACTION_RAS_UPLOAD, 15);
    }

    /**
     * @param array $data
     * @return ResultWrapper
     */
    public static function rasUploadDirect(array $data): ResultWrapper
    {
        return self::sendGetRequest($data, self::ACTION_RAS_UPLOAD, 15);
    }

    /**
     * Download single file from remote server (aspera/ftp) to specified S3 bucket.
     * @param array $data
     *  host                Remote server id
     *  path                Source path (on remote server)
     *  output_s3_bucket    Target S3 bucket
     *  output_s3_path      Target path on S3 bucket
     *  key                 Download key (required to download files > 1mb)
     * @return ResultWrapper
     */
    public static function rasDownload(array $data): ResultWrapper
    {
        return self::sendPostRequest($data, self::ACTION_RAS_DOWNLOAD, 30);
    }

    /**
     * VAD Get timings based on specified audio file.
     * @param $audioUrl
     * @param bool $useS3
     * @param int $minBoxDurationSec
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function vadGetTimings(string $audioUrl, bool $useS3 = true, int $minBoxDurationSec = 1, float $timeout = 40): ResultWrapper
    {
        return self::sendPostRequest(array(
            'audioUrl' => $audioUrl,
            'minBoxDurationSec' => $minBoxDurationSec,
            'useS3' => $useS3 ? 1 : 0,
        ), self::ACTION_VAD_GET_TIMINGS, true, $timeout);
    }

    /**
     * @param string $datFileUrl
     * @param bool $returnDialogOnly - Return signed URL for dialog only, if exists
     * @param int $timeout
     * @return ResultWrapper
     */
    public static function audioGetSignedUrl(string $datFileUrl, bool $returnDialogOnly = false, int $timeout = 5): ResultWrapper
    {
        return self::sendPostRequest(
            ['datFileUrl' => $datFileUrl, 'returnDialogOnly' => $returnDialogOnly],
            self::ACTION_AUDIO_GET_SIGNED_URL,
            $timeout
        );
    }

    /**
     * VAD Get timings based on specified audio file.
     * @param string $sourceVideoPath
     * @param string $sourceVideoHost
     * @param string|null $videoName
     * @param bool $searchByVideoName - prioritize the video name for search
     * @param float $timeout
     * @return ResultWrapper
     */
    public static function getExtractedFramesZip(
        string $sourceVideoPath,
        string $sourceVideoHost,
        string $videoName = null,
        bool   $searchByVideoName = false,
        float  $timeout = 3
    ): ResultWrapper
    {
        return self::sendPostRequest(array(
            'sourceVideoPath' => $sourceVideoPath,
            'sourceVideoHost' => $sourceVideoHost,
            'videoName' => $videoName,
            'searchByVideoName' => $searchByVideoName
        ), self::ACTION_EXTRACT_FRAMES_GET_ZIP, $timeout);
    }

    /**
     * @param String $exportFormat Subtitle format ID
     * @param String $s3Bucket S3 bucket where subtitle XML is stored
     * @param String $s3File Path to subtitle xml on S3 bucket
     * @param String $subtitleName Subtitle file name
     * @param array $taskInfo
     * @return ResultWrapper
     */
    public static function addImageSubtitleExportTask(
        string $exportFormat,
        string $s3Bucket,
        string $s3File,
        string $subtitleName,
        array  $taskInfo
    ): ResultWrapper
    {
        return self::sendPostRequest(array(
            'exportFormat' => $exportFormat,
            's3Bucket' => $s3Bucket,
            's3File' => $s3File,
            'subtitleName' => $subtitleName,
            'taskInfo' => json_encode($taskInfo)
        ), self::ACTION_IMAGE_SUBTITLE_EXPORT, 10);
    }

    /**
     * @param string $subtitleName
     * @param int $processingTaskId
     * @return ResultWrapper
     */
    public static function getImageSubtitleExportStatus(string $subtitleName, int $processingTaskId): ResultWrapper
    {
        return self::sendPostRequest(array(
            'subtitleName' => $subtitleName,
            'processingTaskId' => $processingTaskId,
        ), self::ACTION_IMAGE_SUBTITLE_EXPORT_STATUS);
    }

    /**
     * @param array $taskData
     * @param int|null $processingTaskId
     * @param String $taskType = [convert|download]
     * @return ResultWrapper
     */
    public static function addBatchFileTask(array $taskData, int $processingTaskId = null, string $taskType = 'convert'): ResultWrapper
    {
        return self::sendPostRequest(array(
            'taskData' => $taskData,
            'processingTaskId' => $processingTaskId,
            'taskType' => $taskType,
        ), self::ACTION_BATCH_FILE_TASK);
    }

    /**
     * @param int $processingTaskId
     * @return ResultWrapper
     */
    public static function getBatchFileConvertStatus(int $processingTaskId): ResultWrapper
    {
        return self::sendPostRequest(array(
            'processingTaskId' => $processingTaskId,
        ), self::ACTION_BATCH_FILE_CONVERT_STATUS);
    }

    /**
     * Load custom options by format
     *
     * @param $format
     * @return ResultWrapper
     * @author Valentin <valentin.kanyuk@sferastudios.com>
     */
    public static function getCustomOptions(string $format): ResultWrapper
    {
        return self::sendPostRequest(array(
            'format' => $format,
        ), self::ACTION_CUSTOM_OPTIONS_LIST);

    }

    /**
     * Convert movie script to DFXP
     *
     * @param array $data
     * @param string $format
     * @param float|null $frameRate
     * @param mixed $options
     * @return ResultWrapper
     */
    public static function scriptToDfxp(array $data, string $format, float $frameRate = null, array $options = null): ResultWrapper
    {
        return self::sendPostRequest(array(
            'data' => $data,
            'format' => $format,
            'frameRate' => $frameRate,
            'options' => $options
        ), self::ACTION_SCRIPT_TO_DFXP, false);
    }

    /**
     * Get videos to delete
     *
     * @important $key should be "folder" if $multiple is true, and otherwise
     *
     * @param string $key s3 key
     * @param bool $multiple delete matching files or single file
     * @param string $filename
     * @return mixed
     * @author Jose Bayona <jose.b@scopicsoftware.com>
     *
     */
    public static function getVideoAssetToDelete(string $key, bool $multiple = false, string $filename = ''): ResultWrapper
    {
        return self::sendPostRequest(array(
            'key' => $key,
            'multiple' => $multiple,
            'filename' => $filename,
        ), self::ACTION_GET_VIDEO_TO_DELETE);
    }

    /**
     * Purge video from bucket
     *
     * @important $key should be "folder" if $multiple is true, and otherwise
     *
     * @param string $key s3 key
     * @param bool $multiple delete matching files or single file
     * @param string $filename
     * @return mixed
     * @author Duong N <duong.n@scopicsoftware.com>
     *
     */
    public static function deleteVideoAsset(string $key, bool $multiple = false, string $filename = ''): ResultWrapper
    {
        return self::sendPostRequest(array(
            'key' => $key,
            'multiple' => $multiple,
            'filename' => $filename,
        ), self::ACTION_DELETE_VIDEO_ASSET);
    }

    /**
     * @param $url
     * @return ResultWrapper
     */
    public static function verifyRtmp(string $url): ResultWrapper
    {
        return self::sendPostRequest(array(
            'url' => $url,
        ), self::ACTION_VERIFY_RTMP, 10);
    }

    /**
     * @param string $namePrefix
     * @return ResultWrapper
     */
    public static function getConvertProcessorParametersView(string $namePrefix): ResultWrapper
    {
        return self::sendPostRequest(array(
            'namePrefix' => $namePrefix
        ), self::ACTION_CONVERT_PROCESSOR_PARAMETERS_VIEW, true); //->getFromDataByKey('view')
    }

    /**
     * Verify full netflix package
     *
     * @param string $s3_bucket
     * @param string $s3_path Should contain all package files (subtitles + manifest)
     * @return ResultWrapper
     */
    public static function netflixPackageVerify(string $s3_bucket, string $s3_path): ResultWrapper
    {
        return self::sendPostRequest(array(
            's3_bucket' => $s3_bucket,
            's3_path' => $s3_path,
        ), self::ACTION_NETFLIX_PACKAGE_VERIFY, 60);
    }

    /**
     * @param int $offsetH
     * @param int $offsetV
     * @param string $tvType
     * @param string $fontFamily
     * @param int $fontSize
     * @param string $fontWeight
     * @param string $textFace
     * @param string $borderColor
     * @param string $background
     * @param string $antiAlias
     * @param int $outlineSize
     * @param int $shadowWidth
     * @param int $antiAliasCheck
     * @param string $text
     * @param string $horizontalAlign
     * @param string $verticalAlign
     * @param string $justify
     * @param string $layout
     * @param string $width
     * @param string $height
     * @return array
     */
    public static function SSTExport(
        int    $offsetH = 0,
        int    $offsetV = 0,
        string $tvType = "NTSC",
        string $fontFamily = 'Arial',
        int    $fontSize = 29,
        string $fontWeight = 'regular',
        string $textFace = '0,0,0',
        string $borderColor = '255,0,0',
        string $background = '0,0,0,0',
        string $antiAlias = '0,0,255',
        int    $outlineSize = 2,
        int    $shadowWidth = 0,
        int    $antiAliasCheck = 1,
        string $text = '',
        string $horizontalAlign = '',
        string $verticalAlign = '',
        string $justify = '',
        string $layout = '',
        string $width = '',
        string $height = ''
    ): array
    {
        $content = self::sendGetRequest(array(
            'offsetH' => $offsetH,
            'offsetV' => $offsetV,
            'videoFormat' => 'sd_' . $tvType,
            'fontFamily' => $fontFamily,
            'fontSize' => $fontSize,
            'fontWeight' => $fontWeight,
            'textFace' => $textFace,
            'borderColor' => $borderColor,
            'background' => $background,
            'antiAlias' => $antiAlias,
            'outlineSize' => $outlineSize,
            'shadowWidth' => $shadowWidth,
            'antiAliasCheck' => $antiAliasCheck,
            'text' => $text,
            'horizontalAlign' => $horizontalAlign,
            'verticalAlign' => $verticalAlign,
            'justify' => $justify,
            'layout' => $layout,
            'width' => $width,
            'height' => $height,
        ), self::ACTION_BDN_EXPORT_PREVIEW, 30);

        if ($content->isSuccess()) {
            return array(
                'content_type' => 'image/png',
                'content' => $content->getContents()
            );
        }

        return array(
            'content_type' => null,
            'content' => null
        );
    }

    /**
     * @param int $offsetH
     * @param int $offsetV
     * @param string $videoFormat
     * @param string $fontFamily
     * @param int $fontSize
     * @param string $fontWeight
     * @param string $textFace
     * @param string $borderColor
     * @param string $background
     * @param string $antiAlias
     * @param int $outlineSize
     * @param int $shadowWidth
     * @param int $antiAliasCheck
     * @param string $text
     * @param string $horizontalAlign
     * @param string $verticalAlign
     * @param string $justify
     * @param string $layout
     * @param string $width
     * @param string $height
     * @return array|null[]
     */
    public static function BDNExport(
        int    $offsetH = 0,
        int    $offsetV = 0,
        string $videoFormat = '1080p',
        string $fontFamily = 'Arial',
        int    $fontSize = 61,
        string $fontWeight = 'regular',
        string $textFace = '235,235,235',
        string $borderColor = '10,10,10',
        string $background = '0,0,0,0',
        string $antiAlias = '0,0,0',
        int    $outlineSize = 4,
        int    $shadowWidth = 2,
        int    $antiAliasCheck = 1,
        string $text = '',
        string $horizontalAlign = '',
        string $verticalAlign = '',
        string $justify = '',
        string $layout = '',
        string $width = '',
        string $height = ''
    ): array
    {
        $content = self::sendGetRequest(array(
            'offsetH' => $offsetH,
            'offsetV' => $offsetV,
            'videoFormat' => $videoFormat,
            'fontFamily' => $fontFamily,
            'fontSize' => $fontSize,
            'fontWeight' => $fontWeight,
            'textFace' => $textFace,
            'borderColor' => $borderColor,
            'background' => $background,
            'antiAlias' => $antiAlias,
            'outlineSize' => $outlineSize,
            'shadowWidth' => $shadowWidth,
            'antiAliasCheck' => $antiAliasCheck,
            'text' => $text,
            'horizontalAlign' => $horizontalAlign,
            'verticalAlign' => $verticalAlign,
            'justify' => $justify,
            'layout' => $layout,
            'width' => $width,
            'height' => $height,
        ), self::ACTION_BDN_EXPORT_PREVIEW, 30);

        if ($content->isSuccess()) {
            return array(
                'content_type' => 'image/png',
                'content' => $content->getContents()
            );
        }

        return array(
            'content_type' => null,
            'content' => null
        );
    }


    /**
     * Verify user ip if it is whilte list in processing server
     * @param string|null $ip
     * @return bool
     * @author: Duong N <duong.nguyen@sferastudios.com>
     */
    public static function verifyUserIp(string $ip = null): bool
    {
        $result = self::sendPostRequest(array(
            'ip' => static::resolveUserIp(),
        ), self::ACTION_VERIFY_IP, 2);

        return $result->isSuccess();
    }

    /**
     * Get RAS white list ids in processing server
     * @return array
     * @author: Alexey K <alexey.kovalev@sferastudios.com>
     */
    public static function getRasWhiteList(): array
    {
        return self::sendPostRequest([], self::ACTION_RAS_WHITE_LIST)->get('whiteList') ?? [];
    }

    /**
     * @param string $host
     * @param string $path
     * @return ResultWrapper
     */
    public static function realName(string $host, string $path): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $host,
            'path' => $path
        ), self::ACTION_RAS_REAL_NAME, 20);
    }

    /**
     * Presigned url only for audio files
     * @param $path
     * @return ResultWrapper
     */
    public static function getS3SignedUrl(string $path): ResultWrapper
    {
        if (substr($path, -4) != ".wav") {
            return new ResultWrapper(null, new \Exception($path . ' must have .wav ext'));
        }

        return self::sendPostRequest([
            'path' => $path,
        ], self::ACTION_S3SIGNED_URL, 2);
    }

    /**
     * @param $remoteServerId
     * @param int $timeout
     * @return ResultWrapper
     */
    public static function rasClearTreeCache(int $remoteServerId, int $timeout = 60): ResultWrapper
    {
        return self::sendPostRequest(array(
            'host' => $remoteServerId,
        ), self::ACTION_RAS_CLEAR_TREE_CACHE, $timeout);
    }

    /**
     * @param array $tasks
     * @param string $version
     * @param int $vendorId
     * @return void
     */
    public static function refineTasks(array &$tasks, string $version, int $vendorId = 0)
    {
        if (isset($tasks['type'])) {
            self::refineTaskData($tasks, $version, $vendorId);
        } else {
            foreach ($tasks as &$task) {
                self::refineTaskData($task, $version, $vendorId);
            }
        }
    }

    /**
     * If task data contains "vendor_id" override $vendorId param
     * @param array $task
     * @param string $version V1|V2
     * @param int $vendorId
     * @return void
     */
    public static function refineTaskData(array &$task, string $version = 'V2', int $vendorId = 0)
    {
        $data = unserialize($task['data']);

        if (!empty($data['callback_data'])) {
            $data['callback_data']['origin'] = 'laravel';
            $task['data'] = serialize($data);
        }

        if (isset($data['vendor_id']) && is_numeric($data['vendor_id'])) {
            $vendorId = $data['vendor_id'];
        }

        $task['version'] = ($version ?? static::getTaskMode());
        $task['vendor_id'] = $vendorId;

        if (empty($task['file_name']) && !empty($data['path'])) {
            if (is_array($data['path'])) {
                foreach ($data['path'] as $key => $dataTask) {
                    $task['file_name'] = (isset($data['dist_names'])) ? ($data['dist_names'][$key] ?? $data['dist_names'][0]) : basename($dataTask);
                    $task['path'] = $dataTask;
                    $task['host'] = empty($data['host'][$key]) ? null : $data['host'][$key];

                    //recursive to children
                    if (!empty($task['children'])) {
                        self::refineTasks($task['children'], $version, $vendorId);
                    }
                }
            } else {
                $task['file_name'] = (isset($data['dist_names'])) ? $data['dist_names'][0] : basename($data['path']);
                $task['path'] = $data['path'];
                $task['host'] = empty($data['host']) ? null : $data['host'];
            }
        }

        if (!empty($task['children'])) {
            self::refineTasks($task['children'], $version, $vendorId);
        }
    }

    /**
     * @param $timeframes string
     * @return ResultWrapper
     */
    public static function getPerformanceStatistics(string $timeframes = ''): ResultWrapper
    {
        if (empty($timeframes)) {
            return new ResultWrapper(null, new \Exception($timeframes . ' must not be empty'));
        }

        return self::sendPostRequest(['timeframes' => $timeframes], self::ACTION_PERFORMANCE_STATISTICS, 5.0, false);
    }
}
