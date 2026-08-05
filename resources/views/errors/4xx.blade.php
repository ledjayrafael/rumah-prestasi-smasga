@extends('layouts.guest')

@php $code = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400; @endphp

@section('title', $code . ' — Permintaan Tidak Valid')

@section('content')
    <x-error-card title="Permintaan Tidak Valid"
        message="Terjadi masalah saat memproses permintaan ini." />
@endsection
