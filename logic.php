<?php
    function albumSelector() {
        $libPath = 'lib';
        $artists = array_filter(glob($libPath . '/*'), 'is_dir');
        foreach ($artists as $artist) {
            echo basename($artist) . "\n";
        } 
    }