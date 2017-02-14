<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

if (!function_exists('get_dump')) {
    function get_dump($var)
    {
        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);
        $cloner->setMaxString(-1);

        $cloned = $cloner->cloneVar($var);
        $cloned = $cloned->withMaxDepth(50);
        $cloned = $cloned->withMaxItemsPerDepth(-1);
        $cloned = $cloned->withRefHandles(true);

        $dumper = new CliDumper();
        $dumper->setIndentPad('    ');
        $output = fopen('php://memory', 'r+b');
        $dumper->dump($cloned, $output);

        return stream_get_contents($output, -1, 0);
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('backtrace_as_string')) {
    function backtrace_as_string()
    {
        ob_start();

        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $backtrace = ob_get_contents();

        ob_end_clean();

        return $backtrace;
    }
}

if (!function_exists('minimized_backtrace_as_string')) {
    function minimized_backtrace_as_string()
    {
        $minimized = [];

        $backtrace = backtrace_as_string();
        $backtrace = explode("\n", $backtrace);
        foreach ($backtrace as $item) {
            if (preg_match('/(#\d+) .*? called at \[(.*?)\]/', $item, $matches)) {
                $minimized[] = $matches[1] . ' ' . $matches[2];
            }
        }

        return implode("\n", $minimized);
    }
}
