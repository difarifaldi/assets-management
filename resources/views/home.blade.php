@extends('layouts.section')
@section('content')
    <section class="content px-4 py-1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @php
                        $hour = date('H');
                        $greetings = $hour >= 18 ? 'Good Night' : ($hour >= 12 ? 'Good Afternoon' : 'Good Morning');
                    @endphp
                    <h2 class="font-weight-bold">
                        {{ $greetings }}, {{ Auth::user()->name }} !
                    </h2>
                    <h5 class="font-weight-normal mb-0">
                        Nice to see you again!
                        <span class="text-primary">Let's get started!</span>
                    </h5>
                </div>
            </div>
        </div>
    </section>
@endsection
