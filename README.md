#PHP SDK for sferalabs-processing-app
**Does not depend on any framework** 
>How to use:
- In composer.json
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/d3sw/sferalabs-processing-sdk"
    }
]
"require": {
        "d3sw/sferalabs-processing-sdk": "dev-master",
    },
```
- Create classes extending abstract ones (ProcessingServersAPI or PSModelAPI)
- Implement getApiEndpoint and handleException methods. Example for Laravel:
```
use Illuminate\Support\Facades\Log;

trait LaravelDepending
{
    /**
     * @return string
     * @throws \Exception
     */
    static function getApiEndpoint(): string
    {
        if(!env('PROCESSING_APP_PATH'))
        {
            throw new \Exception('configure PROCESSING_APP_PATH in .env file');
        }
        
        return env('PROCESSING_APP_PATH');
    }

    /**
     * @param \Throwable $e
     */
    static function handleException(\Throwable $e)
    {
        Log::error($e->getMessage());
    }
}
```
