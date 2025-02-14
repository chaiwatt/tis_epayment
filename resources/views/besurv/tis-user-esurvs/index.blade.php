@extends('layouts.master')

@push('css')

<style>

  .label-filter{
    margin-top: 7px;
  }
  /*
	Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
	*/
	@media
	  only screen
    and (max-width: 760px), (min-device-width: 768px)
    and (max-device-width: 1024px)  {

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
		td:nth-of-type(1):before { content: "ลำดับ"; }
		td:nth-of-type(2):before { content: "กลุ่มงานย่อย"; }
		td:nth-of-type(3):before { content: "กลุ่มงานหลัก"; }
		td:nth-of-type(4):before { content: "จำนวนมาตรฐาน"; }
		td:nth-of-type(5):before { content: "จัดการ"; }

	}
</style>

@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ตั้งค่ากลุ่มงานย่อยรับแจ้งตามมาตรฐาน</h3>

                    <div class="pull-right">

                      @can('edit-'.str_slug('TisUserEsurv'))

                          <a class="btn btn-success btn-sm btn-outline waves-effect waves-light" href="#" onclick="UpdateState(1);">
                            <span class="btn-label"><i class="fa fa-check"></i></span><b>เปิด</b>
                          </a>

                          <a class="btn btn-danger btn-sm btn-outline waves-effect waves-light" href="#" onclick="UpdateState(0);">
                            <span class="btn-label"><i class="fa fa-close"></i></span><b>ปิด</b>
                          </a>

                      @endcan

                      @can('add-'.str_slug('TisUserEsurv'))
                          <a class="btn btn-success btn-sm waves-effect waves-light" href="{{ url('/besurv/tis-user-esurvs/create') }}">
                            <span class="btn-label"><i class="fa fa-plus"></i></span><b>เพิ่ม</b>
                          </a>
                      @endcan

                      @can('delete-'.str_slug('TisUserEsurv'))
                          <a class="btn btn-danger btn-sm waves-effect waves-light" href="#" onclick="Delete();">
                            <span class="btn-label"><i class="fa fa-trash-o"></i></span><b>ลบ</b>
                          </a>
                      @endcan

                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::model($filter, ['url' => '/besurv/tis-user-esurvs', 'method' => 'get', 'id' => 'myFilter']) !!}

                        <div class="col-md-3">
                            {!! Form::label('perPage', 'Show:', ['class' => 'col-md-3 control-label label-filter']) !!}
                            <div class="col-md-9">
                                {!! Form::select('perPage', ['10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100', '500'=>'500'], null, ['class' => 'form-control', 'onchange'=>'this.form.submit()']); !!}
                            </div>
                        </div>

                        <div class="col-md-9">
                            {!! Form::label('department', 'กลุ่มงานหลัก:', ['class' => 'col-md-3 control-label label-filter']) !!}
                            <div class="col-md-9">
                                {!! Form::select('department', App\Models\Besurv\Department::pluck('depart_name', 'did'), null, ['class' => 'form-control', 'onchange'=>'this.form.submit()', 'placeholder' => '-เลือกกลุ่มงานหลัก-']); !!}
                            </div>
                        </div>

                        <div class="col-md-offset-3 col-md-9 m-t-10">
                            {!! Form::label('tisno', 'เลขที่ มอก./ชื่อ มอก.', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-9">
                                {!! Form::select('tisno', App\Models\Basic\Tis::Where('status',1)->selectRaw('tb3_TisAutono, CONCAT(tb3_Tisno, " : ", tb3_TisThainame) As Title')->orderbyRaw('CONVERT(tb3_TisThainame USING tis620)')->pluck('Title', 'tb3_TisAutono'), null, ['class' => 'form-control', 'onchange'=>'this.form.submit()', 'placeholder'=>'-เลือก เลข มอก./ชื่อ มอก.-']); !!}
                            </div>
                        </div>

                      {{-- <div class="col-md-4">
                        {!! Form::label('search', 'ค้นหา:', ['class' => 'col-md-3 control-label label-filter']) !!}
                        <div class="col-md-9">
                          <div class="input-group">
                              {!! Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'ชื่อหรือสกุล']); !!}
                              <span class="input-group-btn">
                                <button type="submit" id="check-minutes" class="btn waves-effect waves-light btn-success">
                                  <i class="fa fa-search"></i>
                                </button>
                              </span>
                          </div>
                        </div>
                      </div> --}}

											<input type="hidden" name="sort" value="{{ Request::get('sort') }}" />
											<input type="hidden" name="direction" value="{{ Request::get('direction') }}" />

										{!! Form::close() !!}

                    <div class="clearfix"></div>

                    <div class="table-responsive">

                      {!! Form::open(['url' => '/besurv/tis-user-esurvs/multiple', 'method' => 'delete', 'id' => 'myForm', 'class'=>'hide']) !!}

                      {!! Form::close() !!}

                      {!! Form::open(['url' => '/besurv/tis-user-esurvs/update-state', 'method' => 'put', 'id' => 'myFormState', 'class'=>'hide']) !!}
                        <input type="hidden" name="state" id="state" />
                      {!! Form::close() !!}

                        <table class="table table-borderless" id="myTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@sortablelink('sub_departname', 'กลุ่มงานย่อย')</th>
                                <th>กลุ่มงานหลัก</th>
                                <th class="text-right">จำนวนมาตรฐาน&nbsp;</th>
                                <th>จัดการ</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($sub_departments as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->sub_departname }}</td>
                                    <td>{{ @$item->department->depart_name }}</td>
                                    <td class="text-right">
                                      @if($item->tis_users->count() > 0 && $item->tis_users->first()->tb3_Tisno=='All')
                                          <span class="label label-rounded label-success">
                                            <b>มาตรฐานทั้งหมด</b>
                                          </span>
                                      @elseif($item->tis_users->count() > 0)
                                          <span class="label label-rounded label-info">
                                            <b>{{ $item->tis_users->count() }} มาตรฐาน&nbsp;</b>
                                          </span>
                                      @else
                                          <span class="label label-rounded label-default">
                                            <b>ไม่มี</b>
                                          </span>
                                      @endif
                                    </td>
                                    <td>
                                        @can('view-'.str_slug('tisuseresurvs'))
                                            <a href="{{ url('/besurv/tis-user-esurvs/' . $item->getKey()) }}"
                                               title="View TisUserEsurv" class="btn btn-info btn-xs">
                                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                        @endcan

                                        @can('edit-'.str_slug('tisuseresurvs'))
                                            <a href="{{ url('/besurv/tis-user-esurvs/' . $item->getKey() . '/edit') }}"
                                               title="Edit TisUserEsurv" class="btn btn-primary btn-xs">
                                                  <i class="fa fa-pencil-square-o" aria-hidden="true"> </i>
                                            </a>
                                        @endcan

                                    </td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>

                        <div class="pagination-wrapper">
                          @php
                              $page = array_merge($filter, ['sort' => Request::get('sort'),
                                                            'direction' => Request::get('direction'),
                                                            'perPage' => Request::get('perPage'),
                                                           ]);
                          @endphp
                          {!!
                              $sub_departments->appends($page)->render()
                          !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>

    <script>
        $(document).ready(function () {

            @if(\Session::has('flash_message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('flash_message')}}',
                loaderBg: '#70b7d6',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
            @endif

            //เลือกทั้งหมด
            $('#checkall').change(function(event) {

              if($(this).prop('checked')){//เลือกทั้งหมด
                $('#myTable').find('input.cb').prop('checked', true);
              }else{
                $('#myTable').find('input.cb').prop('checked', false);
              }

            });

        });

        function Delete(){

          if($('#myTable').find('input.cb:checked').length > 0){//ถ้าเลือกแล้ว
            if(confirm_delete()){
              $('#myTable').find('input.cb:checked').appendTo("#myForm");
              $('#myForm').submit();
            }
          }else{//ยังไม่ได้เลือก
            alert("กรุณาเลือกข้อมูลที่ต้องการลบ");
          }

        }

        function confirm_delete() {
            return confirm("ยืนยันการลบข้อมูล?");
        }

        function UpdateState(state){

          if($('#myTable').find('input.cb:checked').length > 0){//ถ้าเลือกแล้ว
              $('#myTable').find('input.cb:checked').appendTo("#myFormState");
              $('#state').val(state);
              $('#myFormState').submit();
          }else{//ยังไม่ได้เลือก
            if(state=='1'){
              alert("กรุณาเลือกข้อมูลที่ต้องการเปิด");
            }else{
              alert("กรุณาเลือกข้อมูลที่ต้องการปิด");
            }
          }

        }

    </script>

@endpush
