@extends('bustravel::backend.layouts.app')

@section('title', 'Buses')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><small><a href="{{route('bustravel.buses')}}" class="btn btn-info">Back</a></small> Buses </h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">buses</li>
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
            <h5 class="card-title">Edit {{$bus->number_plate}}  {{$bus->operator->name??"NONE"}}</h5>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <div class="row">
              <div class="col-md-12">
              <form role="form" action="{{route('bustravel.buses.update',$bus->id)}}" method="POST">
              {{csrf_field() }}

              <div class="box-body">
                  <div class="row">
                        <div class="form-group col-md-3 ">
                            <label for="exampleInputEmail1">Number Plate</label>
                            <input type="text"  name="number_plate" value="{{$bus->number_plate}}" class="form-control {{ $errors->has('number_plate') ? ' is-invalid' : '' }}" id="exampleInputEmail1" placeholder="Enter Number Plate" required>
                            @if ($errors->has('number_plate'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('number_plate') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-md-3 ">
                            <label for="exampleInputEmail1">Seating Capacity</label>
                            <input type="number" name="seating_capacity" value="{{$bus->seating_capacity}}"  class="form-control {{ $errors->has('seating_capacity') ? ' is-invalid' : '' }}" id="exampleInputEmail1" placeholder="Enter Seating Capacity">
                            @if ($errors->has('seating_capacity'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('seating_capacity') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-md-12 ">
                            <label for="exampleInputEmail1">Description</label>
                            <textarea class="form-control" rows="3" placeholder="Enter Description" name="description" >{{$bus->description}}</textarea>
                        </div>
                        <div class=" col-md-3 form-group">
                            <label for="signed" class=" col-md-12 control-label">Status</label>
                            <label class="radio-inline">
                              <input type="radio" id="Active" name="status" value="1"  @php echo $bus->status == 1 ? 'checked' :  "" @endphp> Active</label>
                            </label>
                           <label class="radio-inline">
                              <input type="radio" id="Deactive" name="status" value="0"@php echo $bus->status == 0 ? 'checked' :  "" @endphp > Deactive</label>
                           </label>
                        </div>
                  </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="form-group col-md-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
            </div>

            <!-- /.row -->
            </div>
            <!-- ./card-body -->

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
          $('div.alert').not('.alert-danger').delay(5000).fadeOut(350);
          $('.select2').select2();
        })
    </script>
@stop
