@extends('layout')

@section('content')
    <div class="container-fluid">
        <h1>Check Logtime</h1>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $value['total_hours'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>    
                </table>    
            </div>
        </div>  


    </div>
@endsection
