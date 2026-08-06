@extends('layouts.guest')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
    <x-error-card title="Halaman Tidak Ditemukan"
        message="Halaman yang kamu cari tidak tersedia atau sudah dipindahkan." />
@endsection
