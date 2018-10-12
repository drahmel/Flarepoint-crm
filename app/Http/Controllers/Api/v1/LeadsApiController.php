<?php
namespace App\Http\Controllers\Api\v1;

use Config;
use App\Models;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadsApiController extends Controller
{

	/*
	ALTER TABLE `flarepoint`.`leads`
ADD COLUMN `name` VARCHAR(255) NULL AFTER `updated_at`,
ADD COLUMN `location` VARCHAR(255) NULL AFTER `name`,
ADD COLUMN `photo` VARCHAR(255) NULL AFTER `location`,
ADD COLUMN `summary` VARCHAR(255) NULL AFTER `photo`,
ADD COLUMN `experience` VARCHAR(255) NULL AFTER `summary`;

*/
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function postLead(Request $request)
    {
    	$out = ['success' => 0];
    	$out['data'] = $request->input('body');
    	$lead = new Models\Lead();
    	$data =['title' => "TEST LEAD", 'status' => 1];
    	$data['name'] = $request->input('name');
    	$userId = 1;
    	$lead->user_assigned_id = $userId;
    	$lead->user_created_id = $userId;
    	$lead->client_id = 1;
    	if(!$request->input('title') && !empty($data['name'])) {
    		$data['title'] = $data['name'];
    	}
    	if($request->input('title')) {
    		$lead->location = $request->input('location');
    	}
    	$lead->photo = $request->input('photo');
    	$lead->summary = $request->input('summary');
    	$lead->experience = $request->input('experience');
    	$lead->fill($data);
    	$lead->save();
    	$out['data'] = $data;

        return $out;
    }

}
