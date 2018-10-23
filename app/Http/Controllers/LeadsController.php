<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon;
use Session;
use Datatables;
use App\Models\Lead;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Repositories\Lead\LeadRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
use App\Http\Requests\Lead\UpdateLeadFollowUpRequest;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;

class LeadsController extends Controller
{
    protected $leads;
    protected $clients;
    protected $settings;
    protected $users;

    public function __construct(
        LeadRepositoryContract $leads,
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        SettingRepositoryContract $settings
    )
    {
        $this->users = $users;
        $this->settings = $settings;
        $this->clients = $clients;
        $this->leads = $leads;
        $this->middleware('lead.create', ['only' => ['create']]);
        $this->middleware('lead.assigned', ['only' => ['updateAssign']]);
        $this->middleware('lead.update.status', ['only' => ['updateStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('leads.index');
    }

    /**
     * Data for Data tables
     * @return mixed
     */
    public function anyData()
    {
    	$dateFormat = 'd/m/Y';
    	$dateFormat = 'm/d/Y';

        $leads = Lead::select(
            ['id', 'title', 'name', 'location', 'photo', 'workflow_step_id', 'user_created_id', 'client_id', 'user_assigned_id', 'contact_date', 'updated_at']
        )->where('status', 1);
        return Datatables::of($leads)
            ->addColumn('namelink', function ($leads) {
                return '<a href="leads/' . $leads->id . '" ">' . $leads->name . '</a>';
            })
            ->addColumn('titlelink', function ($leads) {
				$comments = $leads->comments;
				$lastCommentStr = "";
				if(count($comments) > 0) {
					$maxId = -1;
					// TODO: Hack until I can figure out the morphMany sorting
					foreach($comments as $comment) {
						if($comment->id > $maxId) {
							$lastComment = $comment;
							$maxId = $comment->id;
						}
					}

					$lastCommentStr = "<div class='smalltext'>Most Recent Comment ({$lastComment['updated_at']}): {$lastComment['description']}</div>";
				}
                return '<a href="leads/' . $leads->id . '" ">' . $leads->title . '</a>' . $lastCommentStr
                	;
            })
            ->addColumn('photoimg', function ($leads) {
                return '<img style="width:64px;" src="' . (!empty($leads->photo) ? $leads->photo : '/images/persona_placeholder.png') . '" "/>';
            })
            ->addColumn('edit', function ($leads) {
                return '<a href="' . route("leads.edit", $leads->id) . '" class="btn btn-success"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            })
            ->editColumn('user_created_id', function ($leads) {
                return $leads->creator->name;
            })
            ->editColumn('workflow_step_id', function ($leads) {
                return $leads->workflowStep->title;
            })

            ->editColumn('contact_date', function ($leads) use ($dateFormat) {
                return $leads->contact_date ? with(new Carbon($leads->contact_date))
                    ->format($dateFormat) : '';
            })
            ->editColumn('user_assigned_id', function ($leads) {
                return $leads->user->name;
            })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('leads.create')
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLeadRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadRequest $request)
    {
        $getInsertedId = $this->leads->create($request);
        Session()->flash('flash_message', 'Lead is created');
        return redirect()->route('leads.show', $getInsertedId);
    }

    public function updateAssign($id, Request $request)
    {
        $this->leads->updateAssign($id, $request);
        Session()->flash('flash_message', 'New user is assigned');
        return redirect()->back();
    }

    /**
     * Update the follow up date (Deadline)
     * @param UpdateLeadFollowUpRequest $request
     * @param $id
     * @return mixed
     */
    public function updateFollowup(UpdateLeadFollowUpRequest $request, $id)
    {
        $this->leads->updateFollowup($id, $request);
        Session()->flash('flash_message', 'New follow up date is set');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('leads.show')
            ->withLead($this->leads->find($id))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        return view('leads.edit')
            ->withLead($this->leads->find($id));
            //->withRoles($this->roles->listAllRoles())
            //->withDepartments($this->departments->listAllDepartments());
    }

    /**
     * @param $id
     * @param UpdateLeadRequest $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $this->leads->update($id, $request);
        Session()->flash('flash_message', 'Lead successfully updated');
        return redirect()->back();
    }

    /**
     * Complete lead
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateStatus($id, Request $request)
    {
        $this->leads->updateStatus($id, $request);
        Session()->flash('flash_message', 'Lead is completed');
        return redirect()->back();
    }
}
