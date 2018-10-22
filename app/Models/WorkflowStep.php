<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
	protected $table = 'workflow_steps';
    protected $fillable = [
        'title',
        'worflow_id',
        'status'
    ];


}
