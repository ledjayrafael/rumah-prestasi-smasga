@extends('layouts.guest')

@section('title', '401 — Perlu Masuk')

@section('content')
    <x-error-card title="Perlu Masuk"
        message="Kamu harus masuk terlebih dahulu untuk mengakses halaman ini." />
@endsection
