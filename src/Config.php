<?php
namespace Tablasyn;

use \Exception;

class Config {
    private $config;

    private function init() {
        try {
            if (($config = @file_get_contents(str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/config.json'))) === false) {
                throw new Exception('No config.json file');
            }

            $config = json_decode($config, true);

            if ((json_last_error() !== JSON_ERROR_NONE) || (!is_array($config))) {
                throw new Exception('Invalid JSON');
            } else {
                $this->config = $config;
            }
        } catch (Exception $e) {
            echo 'Error: can\'t get config; PHP error:' . "\n" . $e->getMessage() . "\n";
            error_log('Error: can\'t get config; PHP error:' . "\n" . $e->getMessage() . "\n", 3, str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/logs.txt'));
        }
    }

    public function __construct() {
        $this->init();
    }

    public function update() {
        $this->init();
    }

    public function get() {
        return $this->config;
    }
}
