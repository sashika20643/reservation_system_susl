@extends('layouts.app')


@section('content')
<div class="card  ">
<!-- View Agri Farm booking details from dean/hod side -->
<h5 class="card-header bg-secondary text-white">Agri Farm Kabana Booking Details</h5>
<div class="card-body ">


   <div class="mb-3">

    {!! Form::open(['url' => 'viewdeanhodagrisbooking',  'method' => 'GET',  'id' => 'booking_form']) !!}


    <div class="form-group">
    {{Form::label('CheckInDate', 'Check In Date') }}
        <input type="date" class="form-control" name="CheckInDate" value="{{ date('Y-m-d', strtotime( request()->query('CheckInDate') ) ) != null ?  date('Y-m-d', strtotime( request()->query('CheckInDate') ) ) : date('yyyy/mm/dd')}}">

    </div>


    </br>
    {{Form::submit('Submit', ['class'=>'btn btn-primary', 'v-on:click'=>'formSubmit'])}}
    </div>
    {!! Form::close() !!}

    </div>

<div class="table-responsive">
    <table  class="table table-striped">
    <tr>
        <td>Booking Id </td>
        <td>Create Date</td>
        <td>Guest Name </td>
        <td>Check In Date</td>
        <td>Check Out Date</td>
        {{-- <td>Number Of Adults</td>
        <td>Number Of Children</td> --}}
        <td>Number Of Units</td>
        <td>Guest Type</td>
        {{-- <td>Description</td> --}}
        {{-- <td>Request VC Approval</td> --}}
        <td>Status</td>
        <td>Option</td>





    </tr>
    @foreach ($agrsbookings as $agrsbooking)
    <tr>
        <td>{{ $agrsbooking->BookingId  }}</td>
        <td>{{ $agrsbooking->created_at  }}</td>
        <td>{{ $agrsbooking->GuestName  }}</td>
        <td>{{ $agrsbooking->CheckInDate }}</td>
        <td>{{ $agrsbooking->CheckOutDate }}</td>
        {{-- <td>{{ $agrsbooking->NoOfAdults }}</td>
        <td>{{ $agrsbooking->NoOfChildren  }}</td> --}}
        <td>{{ $agrsbooking->NoOfUnits }}</td>
        <td>{{ $agrsbooking->BookingType }}</td>
        {{-- <td>{{ $agrsbooking->Description }}</td> --}}
        {{-- @if($agrsbooking->VCApproval == 0)
        <td>Not Request</a></td>
        @else
        <td>Requested</a></td>
        @endif
         --}}
        <td>{{ $agrsbooking->Status }}</td>

        <td>
        <a class="nav-link btn btn-outline-primary" href = 'showafdean/{{ $agrsbooking->BookingId }}'>View</a></br>
        @if($agrsbooking->Status == 'Send to Recommendation')
        <a class="nav-link btn btn-outline-primary" href = 'afrecommend/{{ $agrsbooking->BookingId }}'>Recommend</a> </br>
        <a class="nav-link btn btn-outline-primary" href = 'afnotrecommend/{{ $agrsbooking->BookingId }}'>Reject</a>
        @else

        @endif

        </td>

    </tr>
    @endforeach
    </table>

{{ $agrsbookings->links() }}
</div>
 </div>
</div>


@endsection
