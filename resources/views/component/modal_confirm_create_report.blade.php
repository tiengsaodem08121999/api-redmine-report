 @if (session('report_id'))
     <div class="alert alert-success alert-dismissible fade show" role="alert">
         {{ 'https://tools.splus-software.com/redmine/issues/' . session('report_id') }}<br>
         {{ 'em gui report ngày ' . now()->format('d/m/Y') }}
     </div>
 @endif
 <form action="{{ route('executeReport') }}" id="execute_report" method="POST">
     <div class="modal fade" id="comfirmCreateReportModal" tabindex="-1" aria-labelledby="modal2Label" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h1 class="modal-title fs-5" id="modal2Label">Comfirm Create Report {{ now()->format('d/m/Y') }}
                     </h1>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR Font
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" value="34/36" name="cr_font" id="cr_font"
                                 placeholder="CR Font">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug Font
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" value="28/28" name="bug_font" id="bug_font"
                                 placeholder="Bug Font">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR CMS
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" value="103/107" name="cr_cms" id="cr_cms"
                                 placeholder="CR CMS">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug CMS
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" value="38/39" name="bug_cms" id="bug_cms"
                                 placeholder="Bug CMS">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     CR API
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" name="cr_api" value="4/5" id="cr_api"
                                 placeholder="CR API">
                         </div>
                         <div class="col-md-3">
                             <span>
                                 <strong>
                                     Bug API
                                 </strong>
                             </span>
                         </div>
                         <div class="col-md-9">
                             <input type="text" class="form-control" name="bug_api" value="0/0" id="bug_api"
                                 placeholder="Bug API">
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     @csrf
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-primary">Save changes</button>
                 </div>
             </div>
         </div>
     </div>
 </form>
