<?php

function USER()
{
    return Auth()->user();
}


if (!function_exists('isAdmin')) {

    function isAdmin()
    {
        return USER()->role == 'admin';
    }
}
