@extends('layout')
@section('content')
    <div class="min-h-screen">
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Timesheet</h1>
            <div class="flex justify-between items-center">
                <form action="{{ route('timesheet') }}" method="get">
                    <input type="month" name="date" value="{{ date('Y-m') }}">
                    <select name="project_id">
                        @foreach ($project['projects'] as $project)
                            <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('redmine') }}" class="btn btn-primary">Rerport</a>
                </form>
            </div>
        
            @foreach ($userNotLogTime as $day => $user)
                @if($user)
                    <span class="text-sm text-gray-500"><b>  {{ $day }} </b> </span> <br>
                    @foreach ($user as $name => $tasks)
                            <span class="text-sm text-gray-500"> {{ $name }} </span> <br>

                    @endforeach
                @endif
            @endforeach
        </main>
    </div>  
@endsection
