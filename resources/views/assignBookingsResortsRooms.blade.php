@extends('layouts.app')


@section('content')
    {{-- <ul class="nav nav-tabs" id="myTab" role="tablist"> --}}
    <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-block" id="manageResortsRooms-tab" data-toggle="pill"
                data-target="#manageResortsRooms" type="button" role="tab" aria-controls="manageResortsRooms"
                aria-selected="true">Manage Booking</button>

        <li class="nav-item" role="presentation">
            <button class="nav-link btn-block" id="addResortsRooms-tab" data-toggle="pill" data-target="#addResortsRooms"
                type="button" role="tab" aria-controls="addResortsRooms" aria-selected="false">View Completed
                Bookings</button>
        </li>

        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="addResortsRooms" role="tabpanel" aria-labelledby="addResortsRooms-tab">

            <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
                <center>
                    <h5 class="card-header">View Bookings</h5>
                </center>
                <div class="card-body">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Check In Date</label>
                                <input type="date" class="form-control" id="checkInDateViewBooking"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Check Out Date</label>
                                <input type="date" class="form-control" id="checkOutDateViewBooking"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-primary btn-block"
                                onclick="loadBookings(false,false);">Search</button>
                        </div>

                        <div class="col-md-12">
                            <div class="card p-3 mb-2 bg-primery text-white">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Booking ID</th>
                                            <th scope="col">Guest Name</th>
                                            <th scope="col">Booking Date</th>
                                            <th scope="col">Check In Date</th>
                                            <th scope="col">Check Out Date</th>
                                            <th scope="col">No Of Units</th>
                                            <th scope="col">Pending Units</th>
                                            <th scope="col">Booking Type</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bookingTableView">

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-pane fade show active" id="manageResortsRooms" role="tabpanel"
            aria-labelledby="manageResortsRooms-tab">

            <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
                <center>
                    <h5 class="card-header">Manage Booking</h5>
                </center>
                <div class="card-body">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Check In Date</label>
                                <input type="date" class="form-control" id="checkInDateManageBooking"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Check Out Date</label>
                                <input type="date" class="form-control" id="checkOutDateManageBooking"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-primary btn-block"
                                onclick="loadBookings(true,false);">Search</button>
                        </div>

                        <div class="col-md-12">
                            <div class="card p-3 mb-2 bg-primery text-white">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Booking ID</th>
                                            <th scope="col">Guest Name</th>
                                            <th scope="col">Booking Date</th>
                                            <th scope="col">Check In Date</th>
                                            <th scope="col">Check Out Date</th>
                                            <th scope="col">No Of Units</th>
                                            <th scope="col">Pending Units</th>
                                            <th scope="col">Booking Type</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bookingTableManage">

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- blocking Reason Room Modal --}}
    <div class="modal fade" id="roomAssignModal" tabindex="-1" aria-labelledby="roomAssignModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assing Resort | Rooms | Beds</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Resort</label>
                                <select class="form-control" name="resort_id" id="resort_id"
                                    onchange="loadAvailableRooms()">
                                    <option value="">Please Select Resort</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="room_div">
                                <label>Room</label>
                                <select class="form-control" name="room_id" id="room_id">
                                    <option value="">Please Select Room</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="color: transparent;">.</label>
                                <button type="button" class="btn btn-primary btn-block"
                                    onclick="addToList()">Add</button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <table class="table table-hover" id="selected_room_table">
                                <thead>
                                    <tr>
                                        <th scope="col">Resort Code</th>
                                        <th scope="col">Room Code</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <input type="hidden" id="booking_id" value="" />
                    <input type="hidden" id="check_in" value="" />
                    <input type="hidden" id="check_out" value="" />
                    <input type="hidden" id="type" value="" />
                    <input type="hidden" id="units" value="" />

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-block" onclick="saveAssignRooms()"
                        data-dismiss="modal">Assign</button>
                </div>
            </div>
        </div>
    </div>

    {{-- blocking Reason Room Modal --}}
    <div class="modal fade" id="roomViewModal" tabindex="-1" aria-labelledby="roomViewModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assinged Resort & Rooms</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <table class="table table-hover" id="room_view_table">
                                <thead>
                                    <tr>
                                        <th scope="col">Resort Code</th>
                                        <th scope="col">Room Code</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script>
        let resortRoomAddCount = 0;

        function loadBookings(is_manage, is_all = true) {

            let checkInDate = $('#checkInDateViewBooking').val();
            let checkOutDate = $('#checkOutDateViewBooking').val();
            let dataTableID = "bookingTableView";

            if (is_manage) {
                checkInDate = $('#checkInDateManageBooking').val();
                checkOutDate = $('#checkOutDateManageBooking').val();;
                dataTableID = "bookingTableManage";
            }

            if (!is_all && (checkInDate === "" || checkOutDate === "")) {
                alert("Please Choose Check In and Check Out Dates");
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/booking-details') }}",
                method: 'get',
                data: {
                    is_all: is_all,
                    checkInDate: checkInDate,
                    checkOutDate: checkOutDate,
                    is_manage: is_manage
                },
                success: function(result) {
                    console.log(result);

                    if (result.bookings.length == 0) {
                        $('#' + dataTableID).children('tr').remove();
                    } else {
                        $('#' + dataTableID).children('tr').remove();

                        let i = 1;
                        result.bookings.forEach(function(booking) {
                            console.log(booking);

                            let status = "Canceled";
                            let color = "badge-danger";

                            let args = "'" + booking.BookingId + "','" + booking.booking_type + "','" +
                                booking
                                .pending_units + "','" + formatDate(booking.CheckInDate) + "','" +
                                formatDate(
                                    booking.CheckOutDate) + "'";

                            console.log(args);
                            if (booking.is_cancelled === 0) {
                                status = "Confirmed"
                                color = "badge-success"
                            }

                            if (dataTableID === "bookingTableView") {
                                actionButton =
                                    '<button type="button" class="btn btn-success" onclick="roomView(' +
                                    booking.BookingId + ')"><i class="bi bi-eye"></i></button>';

                                $("#bookingTableView").append("<tr>" +
                                    "<td>" + i + "</td>" +
                                    "<td>" + booking.BookingId + "</td>" +
                                    "<td>" + booking.GuestName + "</td>" +
                                    "<td>" + formatDate(booking.created_at) + "</td>" +
                                    "<td>" + formatDate(booking.CheckInDate) + "</td>" +
                                    "<td>" + formatDate(booking.CheckOutDate) + "</td>" +
                                    "<td>" + booking.NoOfUnits + "</td>" +
                                    "<td>" + booking.pending_units + "</td>" +
                                    "<td>" + booking.booking_type + "</td>" +
                                    '<td><span class="badge badge-pill ' + color +
                                    '" style="cursor: pointer;">' + status +
                                    '</span></td>' +
                                    "<td>" + actionButton + "</td>" +
                                    "</tr>");
                            } else {
                                let actionButton =
                                    '<button type="button" class="btn btn-success mr-2" onclick="roomAssign(' +
                                    args + ')">Asign</button>';

                                $("#bookingTableManage").append("<tr>" +
                                    "<td>" + i + "</td>" +
                                    "<td>" + booking.BookingId + "</td>" +
                                    "<td>" + booking.GuestName + "</td>" +
                                    "<td>" + formatDate(booking.created_at) + "</td>" +
                                    "<td>" + formatDate(booking.CheckInDate) + "</td>" +
                                    "<td>" + formatDate(booking.CheckOutDate) + "</td>" +
                                    "<td>" + booking.NoOfUnits + "</td>" +
                                    "<td>" + booking.pending_units + "</td>" +
                                    "<td>" + booking.booking_type + "</td>" +
                                    "<td>" + actionButton + "</td>" +
                                    "</tr>");
                            }


                            i++;
                        });


                    }

                }
            });

        }

        function formatDate(date) {
            var date = new Date(date);
            var dateString = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
                .toISOString()
                .split("T")[0];

            return dateString;
        }

        loadBookings(true);
        loadBookings(false);



        function roomAssign(id, type, units, check_in, check_out) {

            resortRoomAddCount = 0;

            localStorage.setItem("selectedRooms", "");

            $('#roomAssignModal').modal('show');
            $('#check_in').val(check_in);
            $('#check_out').val(check_out);
            $('#units').val(units);
            $('#type').val(type);
            $('#booking_id').val(id);

            $("#room_div").css("display", "none");
            if (type != "Whole Resort") {
                $("#room_div").css("display", "block");
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/get-resorts-for-assign') }}",
                method: 'get',
                data: {
                    units: units,
                    check_in: check_in,
                    check_out: check_out,
                    type: type
                },
                success: function(result) {
                    console.log(result);

                    $('#resort_id').empty().append($('<option>', {
                        value: "",
                        text: "Please Select Resort"
                    }));

                    result.forEach(function(resorts) {
                        $('#resort_id').append($('<option>', {
                            value: resorts.id + "#" + resorts.resort_code,
                            text: resorts.resort_code
                        }));

                    });

                }
            });
        }

        function loadAvailableRooms() {

            let resortId = $('#resort_id').val().split("#")[0];
            let type = $('#type').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/get-rooms-for-assign') }}",
                method: 'get',
                data: {
                    resort_id: resortId,
                    type: type,
                    units: $('#units').val(),
                    check_in_date: $('#check_in').val(),
                    check_out_date: $('#check_out').val()
                },
                success: function(result) {
                    console.log(result);

                    $('#room_id').empty().append($('<option>', {
                        value: "",
                        text: "Please Select Room"
                    }));

                    result.forEach(function(room) {

                        if (type == "Single Bed") {

                            if (room.room_type == "Single") {
                                $('#room_id').append($('<option>', {
                                    value: room.id + "#" + room.room_code,
                                    text: room.room_code
                                }));
                            }

                        } else {

                            if (type == "Master bed room" && room.room_type == "Master") {
                                $('#room_id').append($('<option>', {
                                    value: room.id + "#" + room.room_code,
                                    text: room.room_code
                                }));
                            } else {
                                if (room.room_type == "Single") {
                                    $('#room_id').append($('<option>', {
                                        value: room.id + "#" + room.room_code,
                                        text: room.room_code
                                    }));
                                }
                            }

                        }



                    });

                }
            });

        }

        function addToList() {

            let type = $('#type').val();
            let units = $('#units').val();
            let room = $('#room_id').val();
            let resort = $('#resort_id').val();

            let resort_id = resort.split('#')[0];
            let resort_code = resort.split('#')[1];

            let room_id = room.split('#')[0];
            let room_code = room.split('#')[1];

            if (type == "Whole Resort") {
                room_code = "N/A";
                if (resortRoomAddCount == 1) {
                    alert('Sorry, You can only add one resort');
                    return;
                }
            } else {
                if (units == resortRoomAddCount) {
                    alert('Sorry, You can`t add more');
                    return;
                }
            }

            let selectedRooms = localStorage.getItem("selectedRooms");

            if (selectedRooms == "") {
                localStorage.setItem("selectedRooms", resort_code + "#" + resort_id + "#" + room_code + "#" + room_id);
            } else {
                localStorage.setItem("selectedRooms", selectedRooms + "$" + resort_code + "#" + resort_id + "#" +
                    room_code + "#" + room_id);
            }

            $("#selected_room_table tbody tr").remove();

            let selectedRoomsForTable = localStorage.getItem("selectedRooms").split("$");

            selectedRoomsForTable.forEach(function(data) {

                let dataAr = data.split("#");

                let onClick = "removeSelectedItem('" + dataAr[1] + "')";

                let trData = '<tr>' +
                    '<td>' + dataAr[0] + '</td>' +
                    '<td>' + dataAr[2] + '</td>' +
                    '<td><button type="button" class="btn btn-danger" onclick="' + onClick +
                    '"><i class="bi bi-trash"></i></button></td>' +
                    '</tr>';

                $('#selected_room_table > tbody:first').append(trData);

            });

            resortRoomAddCount++;

        }

        function removeSelectedItem(id) {

            let selectedRooms = localStorage.getItem("selectedRooms");

            if (selectedRooms.includes("$")) {

                let selectedRoomsAll = localStorage.getItem("selectedRooms").split("$");

                let setData = "";

                selectedRoomsAll.forEach(function(data) {

                    let dataAr = data.split("#");

                    if (dataAr[1] != id) {

                        if (setData == "") {
                            setData = dataAr[0] + "#" + dataAr[1] + "#" + dataAr[2] + "#" + dataAr[3];
                        } else {
                            setData = setData + "$" + dataAr[0] + "#" + dataAr[1] + "#" + dataAr[2] + "#" + dataAr[
                                3];
                        }

                    }


                });

                localStorage.setItem("selectedRooms", setData);

            } else {
                localStorage.setItem("selectedRooms", "");
            }

            $("#selected_room_table tbody tr").remove();

            if (localStorage.getItem("selectedRooms") != "") {

                let selectedRoomsForTable = localStorage.getItem("selectedRooms").split("$");

                selectedRoomsForTable.forEach(function(data) {

                    let dataAr = data.split("#");

                    let onClick = "removeSelectedItem('" + dataAr[1] + "')";

                    let trData = '<tr>' +
                        '<td>' + dataAr[0] + '</td>' +
                        '<td>' + dataAr[2] + '</td>' +
                        '<td><button type="button" class="btn btn-danger" onclick="' + onClick +
                        '"><i class="bi bi-trash"></i></button></td>' +
                        '</tr>';

                    $('#selected_room_table > tbody:first').append(trData);

                });

            }


            resortRoomAddCount--;

        }

        function saveAssignRooms() {

            let selectedRooms = localStorage.getItem("selectedRooms");

            if (selectedRooms == "") {
                alert("Please add selected rooms");
                return;
            }

            let type = $('#type').val();
            let booking_id = $('#booking_id').val();
            let check_in = $('#check_in').val();
            let check_out = $('#check_out').val();
            let units = $('#units').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/assing-rooms-for-booking') }}",
                method: 'post',
                data: {
                    booking_id: booking_id,
                    type: type,
                    check_in: check_in,
                    check_out: check_out,
                    selectedRooms: selectedRooms,
                    units: units
                },
                success: function(result) {
                    console.log(result);

                    alert('Successfully Assigned');

                    location.reload();

                }
            });

        }

        function roomView(id) {
            $('#roomViewModal').modal('show');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/assing-rooms-view') }}",
                method: 'get',
                data: {
                    booking_id: id,
                },
                success: function(result) {
                    console.log(result);

                    $("#room_view_table tbody tr").remove();

                    result.forEach(function(data) {

                        let onClick = "removeAsingRoom('" + data.id + "')";

                        let room_code = data.room_code == null ? "N/A" : data.room_code;

                        let trData = '<tr>' +
                            '<td>' + data.resort_code + '</td>' +
                            '<td>' + room_code + '</td>' +
                            '<td><button type="button" class="btn btn-danger" onclick="' + onClick +
                            '"><i class="bi bi-trash"></i></button></td>' +
                            '</tr>';

                        $('#room_view_table > tbody:first').append(trData);

                    });

                }
            });
        }

        function removeAsingRoom(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/assing-rooms-remove') }}",
                method: 'post',
                data: {
                    booking_id: id,
                },
                success: function(result) {
                    console.log(result);

                    alert('Successfully Removed');

                    location.reload();

                }
            });
        }
    </script>
@endsection
