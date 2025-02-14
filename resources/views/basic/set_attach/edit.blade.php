@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไขชื่อรายการไฟล์แนบ #{{ $set_attach->id }}</h3>
                    @can('view-'.str_slug('set_attach'))
                        <a class="btn btn-success pull-right" href="{{ url('/basic/set_attach') }}">
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

                    {!! Form::model($set_attach, [
                        'method' => 'PATCH',
                        'url' => ['/basic/set_attach', $set_attach->id],
                        'class' => 'form-horizontal',
                        'files' => true
                    ]) !!}

                    @include ('basic.set_attach.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
