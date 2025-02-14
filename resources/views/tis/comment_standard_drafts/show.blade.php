@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">CommentStandardDraf {{ $commentstandarddraf->id }}</h3>
                    @can('view-'.str_slug('comment-standard-drafts'))
                        <a class="btn btn-success pull-right" href="{{ app('url')->previous() }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                        </a>
                    @endcan
                    <div class="clearfix"></div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table">
                            <tbody>
                              <tr>
                                  <th>ID</th>
                                  <td>{{ $commentstandarddraf->id }}</td>
                              </tr>
                              <tr><th> Comment </th><td> {{ $commentstandarddraf->comment }} </td></tr><tr><th> Name </th><td> {{ $commentstandarddraf->name }} </td></tr><tr><th> Tel </th><td> {{ $commentstandarddraf->tel }} </td></tr><tr><th> Email </th><td> {{ $commentstandarddraf->email }} </td></tr><tr><th> Department Id </th><td> {{ $commentstandarddraf->department_id }} </td></tr>
                              <tr>
                                <th> สถานะ </th>
                                <td> {!! $commentstandarddraf->state=='1'?'<span class="label label-success">เปิดใช้งาน</span>':'<span class="label label-danger">ปิดใช้งาน</span>' !!} </td>
                              </tr>
                              <tr>
                                <th> ผู้สร้าง </th>
                                <td> {{ $commentstandarddraf->createdName }} </td>
                              </tr>
                              <tr>
                                <th> วันเวลาที่สร้าง </th>
                                <td> {{ HP::DateTimeThai($commentstandarddraf->created_at) }} </td>
                              </tr>
                              <tr>
                                <th> ผู้แก้ไข </th>
                                <td> {{ $commentstandarddraf->updatedName }} </td>
                              </tr>
                              <tr>
                                <th> วันเวลาที่แก้ไข </th>
                                <td> {{ HP::DateTimeThai($commentstandarddraf->updated_at) }} </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
