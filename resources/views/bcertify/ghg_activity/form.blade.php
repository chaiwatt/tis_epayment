@push('css')
<link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet" type="text/css" />
@endpush

<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
  {!! Form::label('title', 'กิจกรรม (TH):', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-6">
    {!! Form::text('title', null, ('required' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('title_en') ? 'has-error' : ''}}">
  {!! Form::label('title_en', 'กิจกรรม (EN):', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-6">
    {!! Form::text('title_en', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('title_en', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('kind') ? 'has-error' : ''}}">
  {!! Form::label('kind', 'ประเภท:', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-6">
    {!! Form::select('kind', HP::GHGKinds(), null, ['class' => 'form-control', 'required' => 'required', 'placeholder'=>'-เลือกประเภท-']) !!}
    {!! $errors->first('kind', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('ghg_id') ? 'has-error' : ''}}">
  {!! Form::label('ghg_id', 'สาขาและขอบข่าย:', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-6">
    {!! Form::select('ghg_id', App\Models\Bcertify\Ghg::pluck('title', 'id'), null, ['class' => 'form-control', 'required' => 'required', 'placeholder'=>'-เลือกสาขาและขอบข่าย-']) !!}
    {!! $errors->first('ghg_id', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('state') ? 'has-error' : ''}}">
  {!! Form::label('state', 'สถานะ:', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-6">
    <label>{!! Form::radio('state', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-green']) !!} เปิด</label>
    <label>{!! Form::radio('state', '0', false, ['class'=>'check', 'data-radio'=>'iradio_square-red']) !!} ปิด</label>

    {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group">
  <div class="col-md-offset-4 col-md-4">

    <button class="btn btn-primary" type="submit">
      <i class="fa fa-paper-plane"></i> บันทึก
    </button>
    @can('view-'.str_slug('ghg_activity'))
    <a class="btn btn-default" href="{{url('/bcertify/ghg_activity')}}">
      <i class="fa fa-rotate-left"></i> ยกเลิก
    </a>
    @endcan
  </div>
</div>

@push('js')
<script src="{{ asset('plugins/components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/components/icheck/icheck.init.js') }}"></script>
@endpush
