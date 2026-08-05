@extends('layouts.guest')

@section('title', '419 — Sesi Berakhir')

@section('content')
    <x-error-card title="Sesi Berakhir"
        message="Sesi kamu sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi." />
@endsection
