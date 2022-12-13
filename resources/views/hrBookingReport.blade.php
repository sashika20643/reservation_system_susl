@extends('layouts.app')


@section('content')
    <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
        <center>
            <h5 class="card-header">View Bookings</h5>
        </center>
        <div class="card-body">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Check In Date</label>
                        <input type="date" class="form-control" id="checkInDateViewBooking" aria-describedby="emailHelp">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Check Out Date</label>
                        <input type="date" class="form-control" id="checkOutDateViewBooking"
                            aria-describedby="emailHelp">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="exampleInputEmail1" style="color: transparent;">.</label>
                    <button type="button" class="btn btn-primary btn-block"
                        onclick="loadBookings(false);">Search</button>
                </div>
                <div class="col-md-1">
                    <label for="exampleInputEmail1" style="color: transparent;">.</label>
                    <button type="button" class="btn btn-success btn-block" onclick="downloadPdf();"><i
                            class="bi bi-file-pdf"></i></button>
                </div>

                <div class="col-md-12">
                    <div class="card p-3 mb-2 bg-primery text-white" id="reportTable">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Booking Id</th>
                                    <th scope="col">Guest Name</th>
                                    <th scope="col">Booking Date</th>
                                    <th scope="col">Check In Date</th>
                                    <th scope="col">Check Out Date</th>
                                    <th scope="col">Booking Type</th>
                                    <th scope="col">No Of Units</th>
                                    <th scope="col">Resort</th>
                                    <th scope="col">Rooms</th>

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






    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script>
        let resortRoomAddCount = 0;

        function loadBookings(is_all = true) {

            let checkInDate = $('#checkInDateViewBooking').val();
            let checkOutDate = $('#checkOutDateViewBooking').val();
            let dataTableID = "bookingTableView";

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
                url: "{{ url('/get-hr-booking-report') }}",
                method: 'get',
                data: {
                    is_all: is_all,
                    checkInDate: checkInDate,
                    checkOutDate: checkOutDate
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
                            if (booking.rooms == "All") {
                                booking.resort = booking.resort_code;
                            }
                           

                            $("#bookingTableView").append("<tr>" +
                                "<td>" + i + "</td>" +
                                "<td>" + booking.BookingId + "</td>" +
                                "<td>" + booking.GuestName + "</td>" +
                                "<td>" + formatDate(booking.created_at) + "</td>" +
                                // "<td>" + formatDate(booking.CheckInDate) + "</td>" +
                                // "<td>" + formatDate(booking.CheckOutDate) + "</td>" +
                                "<td>" + booking.CheckInDate + "</td>" +
                                "<td>" + booking.CheckOutDate + "</td>" +
                                "<td>" + booking.booking_type + "</td>" +
                                "<td>" + booking.NoOfUnits + "</td>" +
                                "<td>" + booking.resort + "</td>" +
                                "<td>" + booking.rooms + "</td>" +
                                "</tr>");

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

        loadBookings();

        function downloadPdf() {
            var sTable = document.getElementById('reportTable').innerHTML;

            var style = "<style>";
            style = style + "table {width: 100%;font: 17px Calibri;}";
            style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
            style = style + "padding: 2px 3px;text-align: center;}";
            style = style + "</style>";

            // CREATE A WINDOW OBJECT.
            var win = window.open('', '', 'height=700,width=700');

            win.document.write('<html><head>');
            win.document.write('<title>Profile</title>'); // <title> FOR PDF HEADER.
            win.document.write(style); // ADD STYLE INSIDE THE HEAD TAG.
            win.document.write('</head>');
            win.document.write('<body>');
            win.document.write(sTable); // THE TABLE CONTENTS INSIDE THE BODY TAG.
            win.document.write('</body></html>');

            win.document.close(); // CLOSE THE CURRENT WINDOW.

            win.print(); // PRINT THE CONTENTS.
        }
    </script>
@endsection
