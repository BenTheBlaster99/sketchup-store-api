<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Creator pool percentage
    |--------------------------------------------------------------------------
    |
    | Share of total monthly subscription revenue allocated to the creator
    | pool. Remaining revenue stays with the platform (e.g. 40% pool = 60/40).
    | The pool is split among creators by their share of creator-model downloads.
    |
    */

    'pool_percentage' => (int) env('CREATOR_POOL_PERCENTAGE', 40),

];
