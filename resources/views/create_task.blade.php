@extends('layout')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('execute_create_task') }}" id="form-create-task" method="POST">
            @csrf
            <table class="table table-hover mb-0" id="taskTable">
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
                            <select name="tracker[]" class="form-select" aria-label="Default select example">
                                @foreach (config('information.tracker') as $name =>  $id)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>   
                        </td>
                        <td>
                            <textarea name="subject[]" class="form-control" placeholder="Subject"></textarea>
                        </td>
                        <td>
                            <textarea name="description[]" class="form-control" placeholder="Description"></textarea>
                        </td>
                        <td>
                            <textarea name="sub_task[]" class="form-control" placeholder="Sub task"></textarea>
                        </td>
                        <td>
                            <select name="assignee[]" class="form-select" aria-label="Default select example">
                                @foreach (config('information.assignee') as $name =>  $id)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                           <i class="fa-solid fa-square-plus mt-2" style="cursor: pointer;" onclick="copyRow(this)"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex mt-3 justify-content-end">
                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'primary',
                    'text' => 'Create Task',
                    'id' => 'btn-create-task',
                    'formId' => 'form-create-task',
                ])
            </div>
        </form>
    </div>

    <script>
        function copyRow(button) {
            const row = button.closest('tr');
            const newRow = row.cloneNode(true);
            // Add the new row to the table
            row.parentNode.insertBefore(newRow, row.nextSibling);
        }
    </script>
@endsection
