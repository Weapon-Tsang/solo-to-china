<?php
/** PHP-WASM equivalent of php -l when a native PHP CLI is unavailable. */
function stc_verify_owned_php_syntax() {
    $count=0;
    foreach (array('/wordpress/wp-content/themes/solo-to-china','/wordpress/wp-content/themes/solo-to-china-child','/wordpress/wp-content/plugins/solo-to-china-tools','/tmp/solo-to-china-scripts') as $root) {
        if (!is_dir($root)) {continue;}
        $iterator=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS));
        foreach($iterator as $file) {
            if ('php'!==$file->getExtension()) {continue;}
            try {token_get_all(file_get_contents($file->getPathname()),TOKEN_PARSE);$count++;}
            catch(ParseError $error) {throw new RuntimeException($file->getPathname().': '.$error->getMessage());}
        }
    }
    update_option('stc_upgrade_php_syntax',array('php'=>PHP_VERSION,'files'=>$count,'passed'=>true),false);
    return $count;
}
