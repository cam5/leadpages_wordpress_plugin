<?php

require dirname(dirname(__FILE__)).'/baseAcceptanceTest.php';

//php env variables for local testing
if(file_exists(dirname(dirname(__FILE__)).'/_data/test_variables.php')) {
    require dirname(dirname(__FILE__)) . '/_data/test_variables.php';
}
