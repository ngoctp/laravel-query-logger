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
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'query_logger');

        if ($this->isLoggerEnabled()) {
            $filePath = config('query_logger.file_path');
            if ($filePath) {
                $streamHandler = new StreamHandler($filePath, Logger::INFO);
                $streamHandler->setFormatter(new LineFormatter("%message%;\n"));
                $this->logger = new Logger('query_logger');
                $this->logger->pushHandler($streamHandler);
            }
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('query_logger.php'),
        ]);

        if ($this->logger) {
            $this->logger->info('-- ------ REQUESTED AT '. Carbon::now()->toDateTimeString() .' ------ --');
            $this->app['db']->listen(function($query, $bindings = null, $time = null, $name = null) {
                if ($query instanceof \Illuminate\Database\Events\QueryExecuted) {
                    $sql = $this->formatQuery($query->sql, $query->bindings, $query->connection);
                } else {
                    $sql = $this->formatQuery($query, $bindings, $this->app['db']->connection($name));
                }

                $this->logger->info($sql);
            });
        }
    }

    private function formatQuery($query, $bindings, $connection)
    {
        $sqlParts = explode('?', $query);
        $bindings = $connection->prepareBindings($bindings);
        $pdo = $connection->getPdo();
        $sql = array_shift($sqlParts);
        foreach ($bindings as $i => $binding) {
            $sql .= $pdo->quote($binding) . $sqlParts[$i];
        }

        return $sql;
    }

    private function isLoggerEnabled()
    {
        $enabled = config('query_logger.enabled');
        if (is_null($enabled)) {
            $enabled = config('app.debug');
            if (is_null($enabled)) {
                $enabled = env('APP_DEBUG', false);
            }
        }

        return $enabled;
    }

    private function getConfigPath()
    {
        return __DIR__ . '/../config/query_logger.php';
    }
}
