<?php
$xmlFolder = \dirname( \dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR .
    'Dokumentationen' . \DIRECTORY_SEPARATOR .
    'FAQ-Daten' . DIRECTORY_SEPARATOR .
    'xml';
$tmp = \count( \explode(\DIRECTORY_SEPARATOR, $xmlFolder ) );
$iterator = new \IteratorIterator( new \DirectoryIterator( $xmlFolder ) );
foreach ( $iterator as $item ) {
    if ( '' === $item->getFilename() || '..' === $item->getFilename() || !$item->isDir() ) { continue; }
    $alias = $item->getFilename();
    $categoryFolder = $item->getRealpath();
    $tmp1 = \count( \explode(\DIRECTORY_SEPARATOR, $categoryFolder ) );
    echo "\n- (" . $alias . ') ' . $categoryFolder;
}