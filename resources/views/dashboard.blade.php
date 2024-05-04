<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
  <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <style>
    table.dataTable tbody tr {
    background-color: #343a40;
}
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc {
    background-image: none;
}
.page-item.active .page-link {
    background-color: #343a40;
    border-color: #343a40;
}
.page-link {
    color: #343a40;
}
  </style>
  <div class="py-12">
    <div class="container" style="max-width:1240px;">
    <nav class="navbar navbar-dark bg-dark mb-1">
      <h5 class="text-white">Employee</h5>
    <a class="btn btn-secondary" href="{{ route('uploadForm') }}">Import File</a>
   </nav>
  <table class="table table-dark" id="myTable">
  <thead>
    <tr>
    <th>No</th>
    <th>Employee ID</th>
    <th>Name</th>
    <th>Domain</th>
    <th>Year Founded</th>
    <th>Industry</th>
    <th>Country</th>
    </tr>
  </thead>
  <tbody>
    
  </tbody>
</table>
    </div>
    <!-- <div class="container mt-5">
      <h2 class="mb-4">Employee Table</h2>
      <a class="btn btn-primary" href="{{ route('uploadForm') }}">Import File</a>
      <table id="myTable" class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Domain</th>
            <th>Year Founded</th>
            <th>Industry</th>
            <th>Size range</th>
            <th>Locality</th>
            <th>Country</th>
            <th>Linkedin URL</th>
            <th>Current Employee Estimate</th>
            <th>Total Employee Estimate</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div> -->
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript">
    $(function() {
      var table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        ajax: "{{ route('dashboard') }}",
        columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        }, {
          data: 'employee_id',
          name: 'employee_id'
        }, {
          data: 'name',
          name: 'name'
        }, {
          data: 'domain',
          name: 'domain'
        }, {
          data: 'year_founded',
          name: 'year_founded'
        }, {
          data: 'industry',
          name: 'industry'
        },
        {
          data: 'country',
          name: 'country'
        }]
      });
    });
  </script>
  </body>
  </html>
</x-app-layout>