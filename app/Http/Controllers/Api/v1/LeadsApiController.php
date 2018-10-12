<?php
namespace App\Http\Controllers\Api\v1;

use Config;
use App\Models\Client;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadsApiController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function postLead()
    {
        return ['success' => 1];
    }

}
