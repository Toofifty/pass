@extends('layouts.app')

@section('content')
<div class="container">
</div>
<div class="container" id="app">
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">Vaults</div>
            <div class="panel-body">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Quick search">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="panel panel-default">
            <div class="panel-heading">Dashboard</div>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                You are logged in!
            </div>
        </div>
        <edit></edit>
        <note-list></note-list>
    </div>
</div>
@endsection
