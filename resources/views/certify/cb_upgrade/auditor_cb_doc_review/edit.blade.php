@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แต่งตั้งคณะผู้ตรวจประเมินเอกสาร (CB)</h3>
                    @can('view-'.str_slug('auditorcb'))
                    <a class="btn btn-success pull-right ml-2" href="{{ url('/certify/auditor-cb') }}">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                    </a>
                        <a class="btn btn-info pull-right " style="margin-right:5px !important" href="{{ url('/certify/auditor_cb_doc_review/auditor_cb_doc_review_result_show/' . $certiCb->id) }}">
                             บันทึกผลตรวจประเมินเอกสาร
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

                    {!! Form::model($auditorcb, [
                        'method' => 'PUT',
                        'url' => ['/certify/auditor_cb_doc_review/auditor_cb_doc_review_update', $auditorcb->id],
                        'class' => 'form-horizontal',
                        'files' => true,
                        'id'=>'form_auditor'
                    ]) !!}

                    @include ('certify.cb.auditor_cb_doc_review.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
