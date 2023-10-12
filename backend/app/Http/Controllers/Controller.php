<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static $perPage = 10;

    public function sorted($query, Request $request) {
 
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'DESC');
        $query->orderBy($sortField, $sortDirection);

        return $query;
    }

    public function paginated($query, Request $request) {

        $perPage = $request->input('per_page', Controller::$perPage);
        $query = $query->paginate($perPage);
        $query->appends($request->query());

        return $query;
    }
}
