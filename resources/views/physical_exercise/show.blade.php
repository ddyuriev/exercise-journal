@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="mb-3">
                <span class="form-control pale-border">{{$physical_exercise->name}}</span>
            </div>
            <div class="mb-3">
                <span class="form-control pale-border">{{$physical_exercise->description}}</span>
            </div>

            <div class="mb-3">
                <span class="form-control pale-border">{{physicalExerciseIntToName($physical_exercise->status)}}</span>
            </div>
        </div>
    </div>

@endsection
