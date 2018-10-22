<?php
$subject instanceof \App\Models\Task ? $instance = 'task' : $instance = 'lead';
// TODO: Make this a config
$dateFormat = 'd F, Y, H:i:s';
$dateFormat = 'D, F d, Y, h:i:s a';
$curDate = new DateTime();

?>

<div class="panel panel-primary shadow" style="display:none;">
    <div class="panel-heading"><p>{{$subject->title}}</p></div>
    <div class="panel-body">
        <p>{{$subject->description }}</p>
        <p class="smalltext">{{ __('Created at') }}:
            {{ date($dateFormat, strtotime($subject->created_at))}}
            @if($subject->updated_at != $subject->created_at)
                <br/>{{ __('Modified') }}: {{date($dateFormat, strtotime($subject->updated_at))}}
            @endif</p>
    </div>
</div>

@if($instance == 'task')
    {!! Form::open(array('url' => array('/comments/task',$subject->id, ))) !!}
    <div class="form-group">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'comment-field']) !!}

        {!! Form::submit( __('Add Comment') , ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@else
    {!! Form::open(array('url' => array('/comments/lead',$lead->id, ))) !!}
    <div class="form-group">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'comment-field', 'rows' => 5]) !!}

        {!! Form::submit( __('Add Comment') , ['class' => 'btn btn-primary']) !!}
    	<div id="btnSentLI" class="btn btn-info">Sent LinkedIn Invitation</div>
    </div>
    {!! Form::close() !!}
@endif
<?php $count = 0;?>
<?php $i = 1 ?>
@foreach($subject->comments as $comment)
    <div class="panel panel-primary shadow" style="margin-top:15px; padding-top:10px;">
        <div class="panel-body">
            <p class="smalltext">{{ __('Created at') }}:
                {{ date($dateFormat, strtotime($comment->created_at))}}
                @if($comment->updated_at != $comment->created_at)
                        <br/>{{ __('Modified') }} : {{date($dateFormat, strtotime($comment->updated_at))}}
                @endif
                -- <?php $date2 = new DateTime($comment->created_at);$interval = $curDate->diff($date2);echo $interval->days . " days ago";  ?>
                </p>
            <p>  {{ $comment->description }}</p>
            <p class="smalltext">{{ __('Comment by') }}: <a
                        href="{{route('users.show', $comment->user->id)}}"> {{$comment->user->name}} </a>
            </p>
        </div>
    </div>
@endforeach
<br/>


@push('scripts')
    <script>
        $('#comment-field').atwho({
            at: "@",
            limit: 5,
            delay: 400,
            callbacks: {
                remoteFilter: function (t, e) {
                    t.length <= 2 || $.getJSON("/users/users", {q: t}, function (t) {
                        e(t)
                    })
                }
            }
        });


	 $('#btnSentLI').bind("click", function (event) {
	   var insertStr = "Sent LinkedIn Invitation.";
	   var el = $("#comment-field");
	   var val = el.val();
	   var ta = el[0];
	   var indexOf = el.prop('selectionStart');
		var ss=ta.selectionStart
		var se=ta.selectionEnd
		ta.value=ta.value.substring(0,ss)+insertStr+ta.value.substring(se,ta.value.length)
		//ta.setSelectionRange(ss+choicebox.value.length+2,ss+choicebox.value.length+2)

	})

    </script>
@endpush