<?php
namespace Tablasyn;

use \Exception;

class Auto {
    public function __construct() {
        try {
            if (($file = @file_get_contents(str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/autorun.json'))) === false) {
                throw new Exception('No autorun.json file');
            }

            $file = json_decode($file, true);

            if ((json_last_error() !== JSON_ERROR_NONE) || (!is_array($file))) {
                throw new Exception('Invalid JSON');
            } else {
                foreach ($file as $script) {
                    if (preg_match('/^[a-zA-Z0-9_\-]+\.php$/', $script) && is_file(str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Autorun/') . $script)) {
                        include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Autorun/') . $script;
                    } else {
                        echo 'Warning: can\'t autorun ' . $script . ' (possibly because of invalid name or not existing)' . "\n";
                        error_log('Warning: can\'t autorun ' . $script . ' (possibly because of invalid name or not existing)' . "\n", 3, str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/logs.txt'));
                    }
                }
            }
        } catch (Exception $e) {
            echo 'Warning: can\'t autorun; PHP error:' . "\n" . $e->getMessage() . "\n";
            error_log('Warning: can\'t autorun; PHP error:' . "\n" . $e->getMessage() . "\n", 3, str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/logs.txt'));
        }
    }
}
