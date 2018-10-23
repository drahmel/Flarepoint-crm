@extends('layouts.master')

@section('heading')
    <h1>{{ __('Edit lead') }}</h1>
@stop

@section('content')


    {!! Form::model($lead, [
            'method' => 'PATCH',
            'route' => ['leads.update', $lead->id],
            'files'=>true,
            'enctype' => 'multipart/form-data'
            ]) !!}

    @include('leads.form', ['submitButtonText' =>  __('Update lead')])

    {!! Form::close() !!}

@stop