<?php

function USER()
{
    return Auth()->user();
}


if (!function_exists('canAccess')) {

    function canAccess(...$roleNames)
    {
        return in_array(USER()->role, $roleNames);
    }
}
