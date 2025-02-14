@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left text-primary">ตรวจสอบการชำระ</h3>
                    @can('view-'.str_slug('law-cases-payment'))
                         <a class="btn btn-default pull-right" href="{{url('/law/cases/payment')}}">
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

                    {!! Form::model($cases, [
                        'method' => 'POST',
                        'url' => ['/law/cases/payment', $cases->id],
                        'class' => 'form-horizontal',
                        'files' => true,
                        'id' =>'pay_in_form'
                    ]) !!}

                      @include ('laws.cases.payment.form')

                      <div class="clearfix"></div>
                      <a  href="{{ url('/law/cases/payment') }}"  class="btn btn-default btn-lg btn-block">
                         <i class="fa fa-rotate-left"></i>
                        <b>กลับ</b>
                      </a>
                      
                 
                     

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>

        $(document).ready(function() {
            //Disable
            $('#pay_in_form').find('input, select, textarea').prop('disabled', true);
            $('#pay_in_form').find('button').remove();
            $('#pay_in_form').find('.show_tag_a').hide();
            $('#pay_in_form').find('.box_remove').remove();
            $('#pay_in_form').find('.repeater-form-file').remove();
            $('.check-readonly').prop('disabled', true);
            $('.check-readonly').parent().removeClass('disabled');
            $('.check-readonly').parent().css({"background-color": "rgb(238, 238, 238);","border-radius":"50%","cursor":"not-allowed"});
        });

    </script>
@endpush