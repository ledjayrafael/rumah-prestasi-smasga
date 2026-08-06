@extends('layouts.guest')

@section('title', '403 — Akses Ditolak')

@section('content')
    <x-error-card title="Akses Ditolak"
        message="Kamu tidak memiliki izin untuk mengakses halaman ini." />
@endsection
