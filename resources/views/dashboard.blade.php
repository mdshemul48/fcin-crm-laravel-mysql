@extends('layouts.app')

@section('title', 'Dashboard')


@section('content')
    <p>this is the best</p>
    {{-- <input id="datepicker" width="276" /> --}}

@endsection

{{-- @section('css')
    <style>
        .input-group-append {
            cursor: pointer;
        }
    </style>

@endsection --}}

{{-- @push('custom-scripts')
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap5',
            value: '01/01/2025',
            format: 'dd/mm/yyyy'
        });
    </script>
@endpush --}}
