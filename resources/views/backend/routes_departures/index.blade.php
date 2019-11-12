@extends('bustravel::backend.layouts.app')

@section('title', 'Routes Departure Times')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Routes Departure Times</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">routes departure times</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <h5 class="card-title">All Routes Departure Times</h5>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
                <div class="btn-group">
                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-plus"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu">
                    <a href="{{route('bustravel.routes.departures.create')}}" class="dropdown-item">New Route Departure Time</a>
                    <a href="#" class="dropdown-item">delete selected</a>
                </div>
                </div>

            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <div class="row">
               <div class="col-md-12">
                 <table id="example1" class="table table-bordered table-hover table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Operator</th>
                                <th>Route</th>
                                <th>Price</th>
                                <th>Bus </th>
                                <th>Departure time</th>
                                <th>Driver</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach ($routes as $route_departure_time)
                            <tr>
                              <td>@if($route_departure_time->status==1)
                                    <a href="#" class="btn btn-xs btn-success"> <i class="fas fa-check"></i></a>
                                  @else
                                  <a href="#" class="btn btn-xs btn-danger"> <i class="fas fa-times"></i></a>

                                  @endif
                               </td>
                                <td>{{$route_departure_time->route->operator->name}}</td>
                                <td>{{$route_departure_time->route->start->code??'None'}} - {{$route_departure_time->route->end->code??'None'}}</td>
                                <td>{{number_format($route_departure_time->route->price,2)}} - {{number_format($route_departure_time->route->return_price,2)}}</td>
                                <td>{{$route_departure_time->bus->number_plate??'NONE'}} - {{$route_departure_time->bus->seating_capacity??''}}</td>
                                <td>{{$route_departure_time->departure_time}}</td>
                                <td>{{$route_departure_time->driver->name??'NONE'}}</td>
                                <td><a title="Edit" href="{{route('bustravel.routes.departures.edit',$route_departure_time->id)}}"><i class="fas fa-edit"></i></a>
                                    <a title="Delete" onclick="return confirm('Are you sure you want to delete this Route')" href="{{route('bustravel.routes.departures.delete',$route_departure_time->id)}}"><span style="color:tomato"><i class="fas fa-trash-alt"></i></span></a>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                    </table>
               </div>
            </div>
            <!-- /.row -->
            </div>
            <!-- ./card-body -->
            <div class="card-footer">
            <div class="row">
                <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">$35,210.43</h5>
                    <span class="description-text">TOTAL NUMBER OF ROUTES</span>
                </div>
                <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">$10,390.90</h5>
                    <span class="description-text">TOTAL NUMBER OF ROUTES</span>
                </div>
                <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                    <h5 class="description-header">$24,813.53</h5>
                    <span class="description-text">TOTAL NUMBER OF ROUTES</span>
                </div>
                <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                <div class="description-block">
                    <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                    <h5 class="description-header">1200</h5>
                    <span class="description-text">TOTAL NUMBER OF ROUTES</span>
                </div>
                <!-- /.description-block -->
                </div>
            </div>
            <!-- /.row -->
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
    @parent
    <script>
        $(function () {
var table = $('#example1').DataTable({
      responsive: false,
      dom: 'Blfrtip',
      buttons: [
        {
          extend: 'excelHtml5',
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'pdfHtml5',
          exportOptions: {
            columns: ':visible'
          }
        },
      'colvis',
        //'selectAll',
          //	'selectNone'
      ],
            });
  $('div.alert').not('.alert-danger').delay(5000).fadeOut(350);
})
</script>

@stop
