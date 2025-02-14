@extends('layouts.master')
@push('css')

    <style>

        th {
            text-align: center;
        }

        td {
            text-align: center;
        }

        .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
            background-color: #FFF2CC;
        }

        .modal-header {
            padding: 9px 15px;
            border-bottom: 1px solid #eee;
            background-color: #317CC1;
        }

        /*
          Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
          */
        @media only screen
        and (max-width: 760px), (min-device-width: 768px)
        and (max-device-width: 1024px) {

            /* Force table to not be like tables anymore */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin: 0 0 1rem 0;
            }

            tr:nth-child(odd) {
                background: #eee;
            }

            td {
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            td:before {
                /* Now like a table header */
                /*position: absolute;*/
                /* Top/left values mimic padding */
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }

            /*
            Label the data
        You could also use a data-* attribute and content for this. That way "bloats" the HTML, this way means you need to keep HTML and CSS in sync. Lea Verou has a clever way to handle with text-shadow.
            */
            /*td:nth-of-type(1):before { content: "Column Name"; }*/

        }

        .wrapper-detail {
            border: solid 1px silver;
            margin-left: 20px;
            margin-right: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        fieldset {
            padding: 20px;
        }
    </style>

@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <form id="form_data" method="post" enctype="multipart/form-data">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <input name="id" value="{{$data->id}}" hidden>
                        <div id="alert">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="box-title">ระบบประเมินผลทดสอบ (สำหรับ LAB)</h1>
                                <hr class="hr-line bg-primary">
                            </div>
                        </div>

                        <fieldset class="row">
                            <div class="white-box" style="display: flex; flex-direction: column;">
                                <div class="box-title">
                                     ผลการทดสอบ ใบรับ - นำส่งตัวอย่าง : <b style="text-decoration: underline;">{{$data->no}}</b>
                                </div>
                                <div class="table-responsive">
                                    <table class="table color-bordered-table primary-bordered-table" id="myTable">
                                        <thead>
                                        <tr bgcolor="#0283cc">
                                            <th style="width: 2%;color: white">ลำดับที่</th>
                                            <th style="width: 6%;color: white">เลขที่ใบรับ-นำส่งตัวอย่าง</th>
                                            <th style="width: 8%;color: white">ชื่อหน่วยทดสอบ</th>
                                            <th style="width: 6%;color: white">สถานะ</th>
                                            <th style="width: 6%;color: white">วันที่ส่ง</th>
                                            <th style="width: 4%;color: white">รายละเอียด</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data_detail as $list)
                                            <tr>
                                                <td> {{ $loop->iteration or $list->id }} </td>
                                                <td> {{$list->no_example_id}}</td>
                                                <td> {{$list->name_lap}}</td>
                                                <td>
                                                    @if($list->status == '1')
                                                        -
                                                    @elseif($list->status == '-')
                                                        -
                                                    @else
                                                       {{HP::map_lap_status($list->status)}}
                                                    @endif
                                                </td>
                                                <td> {{HP::DateThai($list->created_at)}}</td>
                                                <td>
                                                    <a href="{{url('/resurv/test_product/detail/'.$list->no_example_id.'/'.$data->id)}}"
                                                       class="btn btn-info "
                                                       style="background-color: #0283cc; border: #0283cc">
                                                        รายละเอียด
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="row">
                            <div class="white-box" style="display: flex; flex-direction: column;">
                                <div class="col-sm-8">
                                    <p class="col-sm-3">ผลการประเมิน :</p>
                                    <div class="col-md-6" style="text-align: -webkit-center">
                                        {!! Form::select('test_status', ['1'=>'ผ่าน','2'=>'ไม่ผ่าน'], $data->test_status, ['class' => 'form-control', 'id'=>'test_status', 'placeholder'=>'-เลือกผลการประเมิน-', $data->status2==2?'disabled':'']); !!}
                                    </div>
                                </div>
                                <div class="col-sm-7"></div>
                                <div class="col-sm-8 " id="notation" style="margin-top: 10px;">
                                    <p class="col-sm-3">หมายเหตุ :</p>
                                    <div class="col-md-9">
                                        <textarea rows="4" class="form-control" name="test_remark" {{ $data->status2==2?'disabled':'' }}>{{$data->test_remark}}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-7"></div>
                                <div class="col-sm-8" style="margin-top: 10px;">
                                    <p class="col-sm-3">ผู้บันทึก :</p>
                                    <div class="col-md-9">
                                        <input class="form-control" name="test_user" readonly
                                            value="{{ !empty($data) && $data->test_user!=''? ($data->test_user):auth()->user()->reg_fname.' '.auth()->user()->reg_lname }}">
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        @if(in_array('5', auth()->user()->RoleListId) && $data->status2==2)

                            <fieldset class="row">
                                <div class="white-box" style="display: flex; flex-direction: column;">
                                <div class="box-title">
                                    <strong> สำหรับ ผก. รับรอง</strong>
                                    <hr>
                                </div>
                                <br>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <p class="col-sm-3">ความเห็น :</p>
                                    <div class="col-sm-9">
                                        <input type="radio" name="poko_comment" value="yes" id="poko_comment1" {{ $data->poko_comment=='yes'?'checked':'' }}> <label for="poko_comment1">เห็นชอบและโปรดดำเนินการต่อไป</label>
                                        <br>
                                        <input type="radio" name="poko_comment" value="no" id="poko_comment2" {{ $data->poko_comment=='no'?'checked':'' }}> <label for="poko_comment2">อื่นๆ</label>
                                    </div>
                                </div>


                                <div class="col-sm-4"></div>
                                <div class="col-sm-8" style="margin-top: 10px;">
                                    <p class="col-sm-3">หมายเหตุ :</p>
                                    <div class="col-md-9">
                                        <textarea rows="4" class="form-control" name="poko_remark">{{$data->poko_remark}}</textarea>
                                    </div>
                                </div>



                                    <div class="col-sm-7"></div>

                                    <div class="col-sm-8" style="margin-top: 10px;">
                                        <p class="col-sm-3">ผู้ตรวจประเมิน :</p>
                                        <div class="col-md-9">
                                            <input class="form-control" name="user_approved" readonly
                                            value="{{ !empty($data) && $data->user_approved!=''? ($data->user_approved):auth()->user()->reg_fname.' '.auth()->user()->reg_lname }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-7"></div>

                                    <div class="col-sm-8" style="margin-top: 10px;">
                                        <p class="col-sm-3">วันที่ตรวจประเมิน :</p>
                                        <div class="col-md-9">
                                            <input class="form-control"
                                                value="{{ $data->date_approved?HP::dateThai(HP::revertDate($data->date_approved)):HP::dateThai(date('d/m/Y')) }}" readonly>
                                            <input name="date_approved" type="hidden"
                                                value="{{ $data->date_approved ?? date('Y-m-d') }}">
                                        </div>
                                    </div>

                                </div>
                            </fieldset>

                        @endif

                       <div align="right">
                            <button class="btn btn-success btn-lg waves-effect waves-light" id="save" type="submit">
                                    <i class="fa fa-save"></i>
                                    <b>บันทึก</b>
                            </button>
                            <a class="btn btn-default btn-lg waves-effect waves-light"
                               href="{{ app('url')->previous() }}">
                                <i class="fa fa-undo"></i>
                                <b>ยกเลิก</b>
                            </a>
                        </div>
                        <input type="hidden" name="previousUrl" value="{{$previousUrl}}">
                        <input type="hidden" name="status2" value="{{$data->status2}}">


                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>

    <script>


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#form_data').submit(function (event) {
            event.preventDefault();
            var form_data = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{url('/resurv/test_product/update')}}",
                datatype: "script",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "success") {
                        alert('บันทึกข้อมูลสำเร็จ');
                        window.location.href = "{{url('/resurv/test_product')}}"
                    } else if (data.status == "error") {
                        alert(data.message)
                    } else {
                        alert('ระบบขัดข้อง โปรดตรวจสอบ !');
                    }
                }
            });
        });

        function add_submit_btn(val) {
            $('#submit_btn').html('<input type="text" name="check_submit" value="' + val + '" hidden>');
        }

    </script>

@endpush
