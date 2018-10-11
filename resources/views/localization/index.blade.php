@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('wellcome.title',['name'=>'abc']) }} {{ trans_choice('time.minutes_ago', 5, ['value' => 5]) }}</div>

                    <div class="card-body">
                    <p>
                        {{ trans_choice('wellcome.cart',20)  }}
                    </p>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @endsection
