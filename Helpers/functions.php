<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FXP\Api\Helpers
{

    function deleteFolder( $folder )
    {
        $iterator = new \RecursiveDirectoryIterator( $folder, \RecursiveDirectoryIterator::SKIP_DOTS );
        $items = new \RecursiveIteratorIterator( $iterator, \RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $items as $item ) {
            if ( $item->getFilename() === '.' || $item->getFilename() === '..') { continue; }
            if ( $item->isDir() ) { \rmdir( $item->getRealPath() ); }
            else { \unlink( $item->getRealPath() ); }
        }
        \rmdir( $folder );
    }

}

