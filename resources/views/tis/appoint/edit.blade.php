@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไขการแต่งตั้งคณะกรรมการ #{{ $appoint->id }}</h3>
                    @can('view-'.str_slug('appoint'))
                        <a class="btn btn-success pull-right" href="{{ app('url')->previous() }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                        </a>
                    @endcan
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($appoint, [
                        'method' => 'PATCH',
                        'url' => ['/tis/appoint', $appoint->id],
                        'class' => 'form-horizontal',
                        'files' => true
                    ]) !!}

                    @include ('tis.appoint.form')

                    {!! Form::close() !!}

                    @include ('tis.appoint.modal_appoint_department')
                    @include ('tis.appoint.modal_board')

                </div>
            </div>
        </div>
    </div>
@endsection
