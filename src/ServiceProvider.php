<?php

namespace NgocTP\QueryLogger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use DB;
use Illuminate\Database\Events\QueryExecuted;
use Monolog\Formatter\LineFormatter;
use Carbon\Carbon;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 
     * @var \Monolog\Logger $logger
     */
    protected $logger = null;
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/query_logger.php' => config_path('query_logger.php'),
        ]);
        
        if ($this->logger) {
            $this->logger->info('----- REQUEST AT '. Carbon::now()->toDateTimeString() .' -----');
            DB::listen(function (QueryExecuted $query) {
                $sqlParts = explode('?', $query->sql);
                $bindings = $query->connection->prepareBindings($query->bindings);
                $pdo = $query->connection->getPdo();
                $sql = array_shift($sqlParts);
                foreach ($bindings as $i => $binding) {
                    $sql .= $pdo->quote($binding) . $sqlParts[$i];
                }
                
                $this->logger->info($sql);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $enabled = config('query_logger.enabled');
        if (is_null($enabled) ? config('app.debug') : $enabled) {
            $streamHandler = new StreamHandler(config('query_logger.file_path', storage_path('logs/query_logger.php')), Logger::INFO);
            $streamHandler->setFormatter(new LineFormatter("%message%;\n"));
            $this->logger = new Logger('sql');
            $this->logger->pushHandler($streamHandler);
        }
    }
}
