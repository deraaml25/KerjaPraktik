<?php
$dir = new RecursiveDirectoryIterator('c:/laragon/www/sidmini/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach($ite as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Replace 'd M Y' or 'd F Y' with 'd/m/y'
        $newContent = preg_replace('/([\'"])d [MF] Y(,? H:i)?([\'"])/', '$1d/m/y$2$3', $content);

        // Also change table headers containing 'Tgl' or 'Tanggal' to text-center
        $newContent = preg_replace_callback('/<th\b[^>]*>(.*?)<\/th>/is', function($matches) {
            $tag = $matches[0];
            if (preg_match('/tanggal|tgl/i', $matches[1])) {
                $tag = preg_replace('/\btext-left\b/', 'text-center', $tag);
                // If it doesn't have text-center, add it
                if (strpos($tag, 'text-center') === false) {
                    $tag = preg_replace('/class="([^"]*)"/', 'class="$1 text-center"', $tag);
                }
            }
            return $tag;
        }, $newContent);

        // For <td> containing format('d/m/y'), change text-left to text-center or add text-center.
        $newContent = preg_replace_callback('/<td\b[^>]*>(.*?)<\/td>/is', function($matches) {
            $tag = $matches[0];
            if (strpos($matches[1], "format('d/m/y'") !== false || strpos($matches[1], "translatedFormat('d/m/y'") !== false) {
                if (strpos($matches[0], 'text-center') === false) {
                    // Only add text-center if it doesn't have text-right
                    if (strpos($matches[0], 'text-right') === false) {
                        $tag = preg_replace('/class="([^"]*)"/', 'class="$1 text-center"', $tag);
                        $tag = preg_replace('/\s+text-left\b/', '', $tag);
                    }
                }
            }
            return $tag;
        }, $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
echo "Done.\n";
