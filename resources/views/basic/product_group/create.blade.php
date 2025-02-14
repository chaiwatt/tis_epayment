@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">เพิ่มกลุ่มผลิตภัณฑ์/สาขา</h3>
                    @can('view-'.str_slug('product_group'))
                        <a class="btn btn-success pull-right" href="{{url('/basic/product_group')}}">
                            <i class="icon-arrow-left-circle"></i> กลับ
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

                    {!! Form::open(['url' => '/basic/product_group', 'class' => 'form-horizontal', 'files' => true]) !!}

                    @include ('basic.product_group.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
