@extends('layouts.guest')

@php $code = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500; @endphp

@section('title', $code . ' — Terjadi Kesalahan')

@section('content')
    <x-error-card title="Terjadi Kesalahan"
        message="Maaf, terjadi kesalahan pada sistem. Tim kami sudah diberi tahu." />
@endsection
