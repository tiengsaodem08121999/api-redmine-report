@extends('layout')
@section('content')
@dd($dailyReportListWithMonth)
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
                </form>
            </div>
        </main>
    </div>  
@endsection
