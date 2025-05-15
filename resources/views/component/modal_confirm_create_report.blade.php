 @if (session('report_id'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ "@DuongNT"}} <br>
          {{ "https://tools.splus-software.com/redmine/issues/" . session('report_id')}}<br>
          {{ "em gui report ngày " .now()->format('d/m/Y')}}
        </div>
    @endif

<div class="modal fade" id="comfirmCreateReportModal" tabindex="-1" aria-labelledby="modal2Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modal2Label">Comfirm Create Report {{now()->format('d/m/Y')}}</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        hihih
      </div>
      <div class="modal-footer">
        <form action="{{ route('executeReport') }}" id="execute_report" method="POST">
                @csrf
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
      </div>
    </div>
  </div>
</div>