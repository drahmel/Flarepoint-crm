@extends('layouts.master')
@section('heading')
    <h1>{{__('All leads')}}</h1>
@stop

@section('content')
    <table class="table table-hover" id="leads-table">
        <thead>
        <tr>

            <th>{{ __('Photo') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Location') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Assigned') }}</th>
            <th>{{ __('Updated At') }}</th>
            <th></th>

        </tr>
        </thead>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#leads-table').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 6, "desc" ]],
            ajax: '{!! route('leads.data') !!}',
            columns: [

                {data: 'photoimg', name: 'photo'},
                {data: 'name', name: 'leads.name', searchable: true},
                {data: 'titlelink', name: 'leads.title', searchable: true},
                {data: 'location', name: 'location',},
                {data: 'workflow_step_id', name: 'workflow_step_id'},
                {data: 'user_assigned_id', name: 'user_assigned_id'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'edit', name: 'edit', searchable: false},


            ]
        });
    });
</script>
@endpush