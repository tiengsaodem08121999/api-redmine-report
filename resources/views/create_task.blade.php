@extends('layout')

@section('content')
    <div class="container-fluid">
        <form action="" method="POST">
            @csrf
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>tracker</th>
                        <th>subject</th>
                        <th>description</th>
                        <th>Sub task</th>
                        <th>Assignee</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            
                        </td>
                        <td>
                            <select name="tracker" class="form-select" aria-label="Default select example">
                                <option value="1">Bug</option>
                                <option value="2">Feature</option>
                                <option value="3">Task</option>
                            </select>   
                        </td>
                        <td>
                            <input type="text" name="subject" class="form-control" placeholder="Subject">     
                        </td>
                        <td>
                            <textarea name="description" class="form-control" placeholder="Description"></textarea>
                        </td>
                        <td>
                            <input type="text" name="sub_task">
                        </td>
                        <td>
                            <select name="assignee" class="form-select" aria-label="Default select example">
                                <option value="1">User 1</option>
                                <option value="2">User 2</option>
                                <option value="3">User 3</option>
                            </select>
                        </td>
                        <td>
                           <i class="fa-solid fa-square-plus mt-2"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
               <button type="submit" class="btn btn-primary ">Create Task</button>
        </form>
    </div>
@endsection
