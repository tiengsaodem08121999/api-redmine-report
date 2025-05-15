@php
    $developers = [
        'QuyLV'   => 'cac020bc0f405ab33aba64abffe5216beacf4a27',
        'DuongNT' => 'cad5eb98b070c1ee75330031c0e34ed4cd412eb1',
        'KietNT'  => 'ece23009da9170c5d5edb0c1ef1095db5657f9f4',
    ];
@endphp
<form action="{{route('logtime')}}" method="POST">
    <div class="modal fade" id="logTimeModal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal1Label">LogTime</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="task_id" class="form-label">Task ID</label>
                        <input type="text" class="form-control" id="task_id" name="task_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="task_id" class="form-label">User </label>
                        <select name="key" id="" class="form-select" required>
                            @foreach ($developers as $dev => $key)
                                <option value="{{ $key }}">{{ $dev }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="spent_time" class="form-label">Spent Time (hours)</label>
                        <input type="number" class="form-control" value="8" id="spent_time" name="spent_time"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Activity</label>
                        <select name="activity_id" class="form-select" required>
                            <option value="15">01_Study</option>
                            <option value="8">02_Design</option>
                            <option value="10" selected>03_Coding</option>
                            <option value="9">04_Unit Test</option>
                            <option value="17">05_Integration Test</option>
                            <option value="11">06_User Acceptance Test</option>
                            <option value="16">07_Review (code, doc)</option>
                            <option value="24">08_Correction (fix bug, doc)</option>
                            <option value="12">09_Translation</option>
                            <option value="14">10_Meeting</option>
                            <option value="20">11_Training</option>
                            <option value="31">12_System Test</option>
                            <option value="23">99_Others</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
</form>
