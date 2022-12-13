@extends('layouts.app')


@section('content')
    {{-- <ul class="nav nav-tabs" id="myTab" role="tablist"> --}}
    <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-block" id="manageResortsRooms-tab" data-toggle="pill"
                data-target="#manageResortsRooms" type="button" role="tab" aria-controls="manageResortsRooms"
                aria-selected="true">Manage Resorts &
                Rooms</button>

        <li class="nav-item" role="presentation">
            <button class="nav-link btn-block" id="addResortsRooms-tab" data-toggle="pill" data-target="#addResortsRooms"
                type="button" role="tab" aria-controls="addResortsRooms" aria-selected="false">Add Resorts &
                Rooms</button>
        </li>

        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="addResortsRooms" role="tabpanel" aria-labelledby="addResortsRooms-tab">

            <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
                <center>
                    <h5 class="card-header">Add Resorts</h5>
                </center>
                <div class="card-body">

                    <form action="{{ route('create-resort') }}" method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Resort Code</label>
                                    <input type="text" class="form-control" name="resort_code" id="resort_code" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="exampleInputEmail1" style="color: transparent;">.</label>
                                <button type="submit" class="btn btn-primary btn-block">Add Resort</button>
                            </div>
                        </div>
                    </form>



                </div>
            </div>

            <div class="card p-3 mb-2 mt-5 bg-secondary text-white">
                <center>
                    <h5 class="card-header">Add Resort Rooms</h5>
                </center>
                <div class="card-body">

                    <form action="{{ route('create-room') }}" method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Resort</label>
                                    <select class="form-control" name="resort" required>
                                        <option value="">Please Select Resort</option>
                                        @foreach (\App\Models\Resorts::where('is_deleted', 0)->get() as $resort)
                                            <option value="{{ $resort->id }}">{{ $resort->resort_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Room Code</label>
                                    <input type="text" class="form-control" name="room_code" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Room Type</label>
                                    <select class="form-control" name="room_type" id="room_type" required
                                        onchange="setRoomPreferance()">
                                        <option value="">Please Select Type</option>
                                        <option value="Master">Master</option>
                                        <option value="Single">Single</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Beds</label>
                                    <input type="text" class="form-control" name="beds" id="beds" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Is Available For Resource Persons</label>
                                    <select class="form-control" name="is_available_for_resource_person" id="is_available_for_resource_person" required>
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Room Bathroom</label>
                                    <select class="form-control" name="is_shared_bathroom" id="is_shared_bathroom" required>
                                        <option value="">Please Select Bathroom</option>
                                        <option value="1">Shared Bathroom</option>
                                        <option value="0">Attached Bathroom</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <label for="exampleInputEmail1" style="color: transparent;">.</label>
                                <button type="submit" class="btn btn-primary btn-block">Add Room</button>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
        <div class="tab-pane fade show active" id="manageResortsRooms" role="tabpanel"
            aria-labelledby="manageResortsRooms-tab">

            <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
                <center>
                    <h5 class="card-header">Manage Resorts</h5>
                </center>
                <div class="card-body">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Resort</label>
                                <select class="form-control" name="resort_id" id="resort_id">
                                    <option value="">Please Select Resort</option>
                                    @foreach (\App\Models\Resorts::where('is_deleted', 0)->get() as $resort)
                                        <option value="{{ $resort->id }}">{{ $resort->resort_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-primary btn-block" id="searchResort">Search</button>
                        </div>

                        <div class="col-md-2">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-warning btn-block" id="deleteResort">Delete</button>
                        </div>

                        <div class="col-md-2" id="div-btn-unblock" style="display: none;">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-success btn-block"
                                onclick="resortStatusChange(1)">Unblock</button>
                        </div>

                        <div class="col-md-2" id="div-btn-block" style="display: none;">
                            <label for="exampleInputEmail1" style="color: transparent;">.</label>
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                                data-target="#blockResonModal">Block</button>
                        </div>

                        <div class="col-md-12">
                            <div class="card p-3 mb-2 bg-primery text-white">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Room Code</th>
                                            <th scope="col">Room Type</th>
                                            <th scope="col">Bathroom</th>
                                            <th scope="col">Beds</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Enable RP</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="roomTable">

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="blockResonModal" tabindex="-1" aria-labelledby="blockResonModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reson for blocking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Reason</label>
                        <textarea class="form-control" id="blockingReason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-block" onclick="resortStatusChange(0)"
                        data-dismiss="modal">Block</button>
                </div>
            </div>
        </div>
    </div>

    {{-- blocking Reason Room Modal --}}
    <div class="modal fade" id="blockingReasonRoomModal" tabindex="-1" aria-labelledby="blockingReasonRoomModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reson for blocking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Reason</label>
                        <textarea class="form-control" id="blockingReasonRoom" rows="3"></textarea>
                        <input type="hidden" id="room_id" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-block" onclick="roomStatusChange('null',0)"
                        data-dismiss="modal">Block</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script>
        function setRoomPreferance() {
            let room_type = $("#room_type").val();
            if (room_type === "Master") {
                document.getElementById('is_shared_bathroom').value = 0;
                $("#is_shared_bathroom").attr('disabled', 'disabled');

                document.getElementById('beds').value = 1;
                $("#beds").attr('disabled', 'disabled');
            } else {
                $("#is_shared_bathroom").removeAttr('disabled')

                document.getElementById('beds').value = "";
                $("#beds").removeAttr('disabled')
            }
        }

        jQuery(document).ready(function() {
            jQuery('#searchResort').click(function(e) {

                let id = jQuery('#resort_id').val();
                if (id === "") {
                    alert("Please select resort first");
                    return;
                }

                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                jQuery.ajax({
                    url: "{{ url('/resort-details') }}/" + id,
                    method: 'get',
                    data: {

                    },
                    success: function(result) {
                        console.log(result);

                        if (result.resort.status == 1) {
                            $("#div-btn-unblock").css("display", "none");
                            $("#div-btn-block").css("display", "block");
                        } else {
                            $("#div-btn-unblock").css("display", "block");
                            $("#div-btn-block").css("display", "none");
                        }

                        if (result.rooms.length == 0) {
                            $('#roomTable').children('tr').remove();
                        } else {
                            $('#roomTable').children('tr').remove();

                            let i = 1;
                            result.rooms.forEach(function(room) {
                                console.log(room);

                                let bathroom = "Attached";
                                if (room.is_shared_bathroom == 1) {
                                    bathroom = "Shared"
                                }

                                let deleteButton =
                                    '<button type="button" class="btn btn-warning" onclick="roomDelete(' +
                                    room.id + ')"><i class="bi bi-trash"></i></button>';

                                let status = "Blocked";
                                let color = "badge-danger";
                                let color_enable_rp = "badge-danger";
                                let status_enable_rp = "No";

                                let actionButton =
                                    '<button type="button" class="btn btn-success mr-2" onclick="roomStatusChange(' +
                                    room.id + ',1)">Unblock</button>' + deleteButton;

                                if (room.status == 1) {
                                    status = "Available";
                                    color = "badge-success";
                                    actionButton =
                                        '<button type="button" class="btn btn-danger mr-2" onclick="openRoomStatusChangeModal(' +
                                        room.id + ')">Block</button>' + deleteButton;
                                }

                                if(room.is_available_for_resource_person == 1){
                                    color_enable_rp = "badge-success";
                                    status_enable_rp = "Yes";
                                }

                                $("#roomTable").append("<tr>" +
                                    "<td>" + i + "</td>" +
                                    "<td>" + room.room_code + "</td>" +
                                    "<td>" + room.room_type + "</td>" +
                                    "<td>" + bathroom + "</td>" +
                                    "<td>" + room.beds_count + "</td>" +
                                    '<td><span class="badge badge-pill ' + color +
                                    '" data-toggle="tooltip" data-placement="top" title="' +
                                    room.block_reason +
                                    '" style="cursor: pointer;">' + status +
                                    '</span></td>' +
                                    '<td><span class="badge badge-pill ' + color_enable_rp +
                                    '" style="cursor: pointer;">' + status_enable_rp +
                                    '</span></td>' +
                                    "<td>" + actionButton + "</td>" +
                                    "</tr>");

                                i++;
                            });


                        }

                    }
                });
            });
        });


        jQuery(document).ready(function() {
            jQuery('#deleteResort').click(function(e) {

                let id = jQuery('#resort_id').val();
                if (id === "") {
                    alert("Please select resort first");
                    return;
                }

                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                jQuery.ajax({
                    url: "{{ url('/resort-delete') }}/" + id,
                    method: 'post',
                    data: {

                    },
                    success: function(result) {
                        console.log(result);

                        location.reload();

                    }
                });
            });
        });

        function roomStatusChange(id, status) {

            if (id === "null") {
                id = jQuery('#room_id').val();
            }
            let reason = jQuery('#blockingReasonRoom').val();

            if (status == 0 && reason == "") {
                alert("Please add valid reason");
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/room-status') }}/" + id + "/" + status,
                method: 'post',
                data: {
                    reason: reason
                },
                success: function(result) {
                    console.log(result);
                    jQuery('#blockingReasonRoom').val("");
                    jQuery('#searchResort').click();
                }
            });
        }

        function resortStatusChange(status) {

            let id = jQuery('#resort_id').val();
            let reason = jQuery('#blockingReason').val();

            if (status == 0 && reason == "") {
                alert("Please add valid reason");
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/resort-status') }}/" + id + "/" + status,
                method: 'post',
                data: {
                    reason: reason
                },
                success: function(result) {
                    console.log(result);
                    jQuery('#blockingReason').val(" ");
                    jQuery('#searchResort').click();
                }
            });
        }

        function roomDelete(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/room-delete') }}/" + id,
                method: 'post',
                data: {},
                success: function(result) {
                    console.log(result);
                    jQuery('#searchResort').click();
                }
            });
        }

        function openRoomStatusChangeModal(id) {

            jQuery('#room_id').val(id);
            $('#blockingReasonRoomModal').modal('show');

        }
    </script>
@endsection
