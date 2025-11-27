 @props(['project_name'])
 @if (session('report_id'))
     <div class="alert alert-success alert-dismissible fade show" role="alert">
         {{ 'https://redmine.splus-software.com/issues/' . session('report_id') }}<br>
         {{ 'em gui report ngày ' . now()->format('d/m/Y') }}
     </div>
 @endif
 <form action="{{ route('executeReport') }}" id="execute_report" method="POST">
     <div class="modal fade" id="comfirmCreateReportModal" tabindex="-1" aria-labelledby="modal2Label" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                    <input type="hidden" name="project_name" value="{{ $project_name }}">
                     <h1 class="modal-title fs-5" id="modal2Label">Comfirm Create Report {{ now()->format('d/m/Y') }}
                     </h1>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 {{-- <div class="modal-body">
                     <div class="row">
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR Font
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" value="{{ data_get($report_summary, 'cr_font', '0/0') }}" name="cr_font" id="cr_font"
                                 placeholder="CR Font">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug Font
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" value="{{ data_get($report_summary, 'bug_font', '0/0') }}" name="bug_font" id="bug_font"
                                 placeholder="Bug Font">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR CMS
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" value="{{ data_get($report_summary, 'cr_cms', '0/0') }}" name="cr_cms" id="cr_cms"
                                 placeholder="CR CMS">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug CMS
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" value="{{ data_get($report_summary, 'bug_cms', '0/0') }}" name="bug_cms" id="bug_cms"
                                 placeholder="Bug CMS">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR API
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" name="cr_api" value="{{ data_get($report_summary, 'cr_api', '0/0') }}" id="cr_api"
                                 placeholder="CR API">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug API
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9 mt-1">
                             <input type="text" class="form-control" name="bug_api" value="{{ data_get($report_summary, 'bug_api', '0/0') }}" id="bug_api"
                                 placeholder="Bug API">
                         </div>
                     </div>
                 </div> --}}
                 <div class="modal-footer">
                     @csrf
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                     @include('components.button', [
                        'type' => 'submit',
                        'color' => 'primary',
                        'text' => 'Save changes',
                        'target' => '',
                        'id' => 'btn-save-changes',
                        'formId' => 'execute_report',
                    ])
                 </div>
             </div>
         </div>
     </div>
 </form>
