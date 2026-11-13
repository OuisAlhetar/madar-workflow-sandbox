<?php
declare(strict_types=1);

// Single place the suite and CI agree on: error reporting on, no output buffering surprises.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('assert.exception', '1');

require_once __DIR__ . '/../src/Price.php';
