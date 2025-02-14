@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">MeetingStandard {{ $meetingstandard->id }}</h3>
                    @can('view-'.str_slug('meetingstandards'))
                        <a class="btn btn-success pull-right" href="{{ url('/certify/meeting-standards') }}">
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
                                  <td>{{ $meetingstandard->id }}</td>
                              </tr>
                              <tr><th> Title </th><td> {{ $meetingstandard->title }} </td></tr><tr><th> State </th><td> {{ $meetingstandard->state }} </td></tr><tr><th> Created By </th><td> {{ $meetingstandard->created_by }} </td></tr><tr><th> Updated By </th><td> {{ $meetingstandard->updated_by }} </td></tr>
                              <tr>
                                <th> สถานะ </th>
                                <td> {!! $meetingstandard->state=='1'?'<span class="label label-success">เปิดใช้งาน</span>':'<span class="label label-danger">ปิดใช้งาน</span>' !!} </td>
                              </tr>
                              <tr>
                                <th> ผู้สร้าง </th>
                                <td> {{ $meetingstandard->createdName }} </td>
                              </tr>
                              <tr>
                                <th> วันเวลาที่สร้าง </th>
                                <td> {{ HP::DateTimeThai($meetingstandard->created_at) }} </td>
                              </tr>
                              <tr>
                                <th> ผู้แก้ไข </th>
                                <td> {{ $meetingstandard->updatedName }} </td>
                              </tr>
                              <tr>
                                <th> วันเวลาที่แก้ไข </th>
                                <td> {{ HP::DateTimeThai($meetingstandard->updated_at) }} </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
