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
    	$body = $request->input('body');
    	$lead = new Models\Lead();
    	$data =['title' => date('Y-m-d H:i:s'), 'status' => 1];
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
    	if(!empty($body)) {
    		$jsonData = json_decode($body, true);
    		$data = array_merge($data, $jsonData);
    	}
    	if(!empty($data['photo'])) {
    		$regex = '/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
    		$url = $data['photo'];
    		$matches = [];
    		preg_match($regex, $url, $matches);
    		$data['matches'] = $matches;
    		if(!empty($matches[0])) {
    			$data['photo'] = $matches[0];
    		} else {
				$data['photo'] = null;
    		}
    	}

    	$lead->fill($data);
    	$lead->save();
    	$out['data'] = $data;

        return $out;
    }

}
