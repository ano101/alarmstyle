@extends('layout.layout')
@section('content')
    <img src="{{route('images.show', ['path' => 'products/test.png', 'preset' => 'product.card'])}}" />
@endsection
