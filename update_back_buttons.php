<?php
$dir = new RecursiveDirectoryIterator('c:/laragon/www/sidmini/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach($ite as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $newContent = preg_replace_callback('/<a\s+href="[^"]*"\s*[\n\r\s]*class="inline-flex items-center text-sm font-medium text-muted hover:text-ink">.*?<\/a>/is', function($matches) {
            $tag = $matches[0];
            $tag = preg_replace(
                '/class="inline-flex items-center text-sm font-medium text-muted hover:text-ink"/', 
                'class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm group"', 
                $tag
            );
            $tag = preg_replace(
                '/<svg class="w-5 h-5 mr-1"/', 
                '<svg class="w-4 h-4 mr-2 text-slate-500 group-hover:text-slate-700 group-hover:-translate-x-1 transition-transform duration-200"', 
                $tag
            );
            return $tag;
        }, $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
echo "Done.\n";
