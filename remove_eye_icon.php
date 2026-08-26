<?php
$dir = new RecursiveDirectoryIterator('c:/laragon/www/sidmini/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach($ite as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getPathname();
        
        // Skip login.blade.php as the eye icon there is for toggling password visibility
        if (strpos($path, 'login.blade.php') !== false) {
            continue;
        }

        $content = file_get_contents($path);
        
        // Regex to match the eye icon SVG and optional trailing whitespace
        $newContent = preg_replace('/<svg[^>]*>\s*<path[^>]*M15 12a3[^>]*\s*\/?>(?:\s*<\/path>)?\s*<path[^>]*M2\.458 12C3[^>]*\s*\/?>(?:\s*<\/path>)?\s*<\/svg>\s*/is', '', $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
echo "Done.\n";
