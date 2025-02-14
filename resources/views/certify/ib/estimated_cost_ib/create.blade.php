@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ระบบการประมาณค่าใช้จ่าย (IB)</h3>
                    @can('view-'.str_slug('estimatedcostib'))
                        <a class="btn btn-success pull-right loading_state" href="{{url('/certify/estimated_cost-ib')}}">
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

                    {!! Form::open(['url' => '/certify/estimated_cost-ib', 'class' => 'form-horizontal', 'files' => true,'id'=>"cost_form"]) !!}

                    @include ('certify/ib/estimated_cost_ib.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
