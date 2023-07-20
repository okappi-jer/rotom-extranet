<?php

/**
 * Check Creatio response (list, multiple resources).
 *
 * @return bool
 */
function checkCreatioResponseList($response)
{
    if(is_object($response) && property_exists( $response, 'd') && property_exists( $response->d, 'results')){
        return true;
    }else{
        return false;
    }
}

function checkCreatioResponseListO4($response)
{
    if(property_exists( $response, 'value')){
        return true;
    }else{
        return false;
    }
}

/**
 * Check Creatio response (show, single resources).
 *
 * @return bool
 */
function checkCreatioResponseSingle($response)
{
    if(is_object($response) && property_exists( $response, 'd') && property_exists( $response->d, 'results') && count($response->d->results) != 0){
        return true;
    }else{
        return false;
    }
}

function checkCreatioResponseSingleO4($response)
{
    if(is_object($response) && property_exists( $response, 'value') && count($response->value) != 0){
        return true;
    }else{
        return false;
    }
}

function array_count_values_of($value, $array) {
    $counts = array_count_values($array);
    return $counts[$value];
}
