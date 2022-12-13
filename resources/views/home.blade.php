@extends('layouts.app')



@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if (Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                        data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <div class="card p-3 mb-2 bg-secondary text-white" id="availability_checking">
        <h5 class="card-header">Availability Checking</h5>
        <div class="card-body">
            <div class="mb-3">

                {!! Form::open(['url' => '/check-availability', 'method' => 'post', 'id' => 'check_form']) !!}

                <div class="form-group">
                    {{ Form::label('property', 'Please Select a Property ') }}
                    {{ Form::select('property', ['Holiday Resort' => 'Holiday Resort', 'NEST' => 'NEST', 'Agri Farm Kabana' => 'Agri Farm Kabana', 'Agri Farm Dining Room' => 'Agri Farm Dining Room', 'Audio Visual Unit' => 'Audio Visual Unit'], null, ['class' => 'form-control', 'v-model' => 'property_type']) }}

                </div>

                <div class="form-group " v-if="property_type === `NEST`">
                    {!! Form::label('Room Type') !!}
                    {!! Form::select('NestId', $nestfill, null, ['class' => 'form-control', 'v-model' => 'room_type_nest']) !!}
                </div>




                <div class="form-group"
                    v-if="property_type === `Agri Farm Dining Room` || property_type === `Audio Visual Unit`">
                    {{ Form::label('CheckInDate', 'Check In Date') }}
                    {{ Form::date('CheckInDate', new \DateTime(), ['class' => 'form-control']) }}

                </div>

                {{-- <div class="form-group" v-if="property_type === `Holiday Resort` || property_type === `NEST` || property_type === `Agri Farm Kabana`"> --}}
                {{-- {{Form::label('CheckOutDate', 'Check Out Date') }} --}}
                {{-- {{ Form::date('CheckOutDate', new \DateTime(), ['class' => 'form-control']) }} --}}

                {{-- </div> --}}

                {{-- =============== --}}


                {{-- <div class="form-group" v-if="property_type === `Holiday Resort`">
                    {!! Form::label('Room Type') !!}
                    {!! Form::select('HolodayResortId', $hrfill, null, ['class' => 'form-control', 'v-model' => 'room_type_hr']) !!}

                </div> --}}

                <div class="form-group" v-if="property_type === `NEST` || property_type === `Agri Farm Kabana`">
                    {{ Form::label('CheckInDate', 'Check In Date Time') }}
                    {{ Form::datetimeLocal('CheckInDate', null, ['class' => 'form-control']) }}
                </div>

                <div class="form-group" v-if="property_type === `NEST` || property_type === `Agri Farm Kabana`">
                    {{ Form::label('CheckOutDate', 'Check Out Date Time') }}
                    {{ Form::datetimeLocal('CheckOutDate', null, ['class' => 'form-control']) }}
                </div>
                {{-- Edit --}}

                <div class="form-group"
                    v-if="property_type === `Agri Farm Dining Room` || property_type === `Audio Visual Unit`">
                    {{ Form::label('StartTime', 'Start Time') }}
                    {{ Form::time('StartTime', \Carbon\Carbon::now(), ['class' => 'form-control']) }}

                </div>
                <div hidden class="form-group"
                    v-if="property_type === `Agri Farm Dining Room` || property_type === `Audio Visual Unit`">
                    {{ Form::label('CurrentTime', 'Current Time') }}
                    {{ Form::time('CurrentTime', \Carbon\Carbon::now(), ['class' => 'form-control']) }}
                    {{-- Edit --}}
                </div>
                <div class="form-group"
                    v-if="property_type === `Agri Farm Dining Room` || property_type === `Audio Visual Unit`">
                    {{ Form::label('EndTime', 'End Time') }}
                    {{ Form::time('EndTime', \Carbon\Carbon::now(), ['class' => 'form-control']) }}
                </div>

                <div class="form-group" v-if="property_type === `NEST` || property_type === `Agri Farm Kabana`">
                    {{ Form::label('NoOfUnits', 'Number Of Units') }}
                    {{ Form::text('NoOfUnits', '', ['class' => 'form-control', 'placeholder' => 'Number Of Units', 'v-model' => 'no_of_units', 'v-on:change' => 'checkUnitsCount']) }}
                </div>

                <div class="form-group" v-if="property_type === `NEST` || property_type === `Agri Farm Kabana`">
                    {{ Form::label('NoOfAdults', 'Number Of Adults') }}
                    {{ Form::text('NoOfAdults', '', ['class' => 'form-control', 'placeholder' => 'Number Of Adults', 'v-model' => 'no_of_adults']) }}
                </div>
                <div class="form-group" v-if="property_type === `NEST` || property_type === `Agri Farm Kabana`">
                    {{ Form::label('NoOfChildren', 'Number Of Children') }}
                    {{ Form::text('NoOfChildren', '', ['class' => 'form-control', 'placeholder' => 'Number Of Children', 'v-model' => 'no_of_children']) }}
                </div>






                </br>
                <div v-if="property_type != `Holiday Resort`">
                    {{ Form::button('Check', ['class' => 'btn btn-primary', 'v-on:click' => 'formSubmit']) }}
                </div>
            </div>
            {!! Form::close() !!}

            <form v-if="property_type === `Holiday Resort`" action="/proceed-booking" method="post" id="hr_Proceed_booking">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Booking Type</label>
                            <select class="form-control" name="booking_type" id="booking_type" required>
                                <option value="">Please Select Booking Type</option>
                                <option value="1">Master Room</option>
                                <option value="2">Single Bed with Shared Room</option>
                                <option value="3">Whole Resort</option>
                                <option value="4">Single Room with Shared Bathroom</option>
                                <option value="5">Single Room with Attached Bathroom</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Check In Date Time</label>
                            <input type="datetime-local" class="form-control" name="check_in_date" id="check_in_date"
                                required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Check Out Date Time</label>
                            <input type="datetime-local" class="form-control" name="check_out_date" id="check_out_date"
                                required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label id="lable_availability">Availability</label>
                            <input type="text" class="form-control" id="availability_count" disabled>
                        </div>
                    </div>
                    {{-- <form action="/proceed-booking" method="post" id="hr_Proceed_booking"> --}}
                        <div class="col-md-6" id="btn_proceedToBooking" style="display: none">
                            {{-- <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> --}}
                            <div class="form-group">
                                <label for="exampleInputEmail1" style="color: transparent;">.</label>
                                <button type="submit" class="btn btn-primary btn-block" onclick="formSubmitHr()">Proceed to
                                    booking</button>
                            </div>
                        </div>
                    {{-- </form> --}}

                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <label for="exampleInputEmail1" style="color: transparent;">.</label>
                        <button type="button" class="btn btn-primary btn-block"
                            onclick="checkAvailability()">Check</button>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </form>

        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.js"></script>

    <script>
        const holiday_resort = new Vue({
            el: '#availability_checking',
            data() {
                return {
                    property_type: null,
                    room_type_hr: null,
                    room_type_nest: null,
                    no_of_units: 0,
                    no_of_adults: 0,
                    no_of_children: 0
                }
            },

            methods: {

                //'Holiday Resort' => 'Holiday Resort', 'NEST' => 'NEST','Agri Farm Kabana' => 'Agri Farm Kabana', 'Agri Farm Dining Room' => 'Agri Farm Dining Room', 'Audio Visual Unit' => 'Audio Visual Unit'
                //
                //


                checkUnitsCount() {

                    if (this.room_type_hr == 1 && this.no_of_units > 7) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 7 units.')
                    }
                    if (this.room_type_hr == 2 && this.no_of_units > 28) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 28 units.')
                    }
                    if (this.room_type_hr == 3 && this.no_of_units > 7) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 28 units.')
                    }

                    if (this.room_type_nest == 1 && this.no_of_units > 1) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 1 units.')
                    }
                    if (this.room_type_nest == 2 && this.no_of_units > 4) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 4 units.')
                    }

                    if (this.property_type == `Agri Farm Kabana` && this.no_of_units > 3) {
                        this.no_of_units = 0;
                        alert('Sorry, You can not book more than 3 units.')
                    }

                },

                formSubmit() {

                    if (this.room_type_hr == 1) {
                        if (this.no_of_adults > 2 * this.no_of_units || this.no_of_children > 1 * this
                            .no_of_units) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.room_type_hr == 2) {
                        if (this.no_of_adults > 1 * this.no_of_units || this.no_of_children > 0) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.room_type_hr == 3) {
                        if (this.no_of_adults > 6 * this.no_of_units || this.no_of_children > 1 * this
                            .no_of_units) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.room_type_nest == 1) {
                        if (this.no_of_adults > 2 * this.no_of_units || this.no_of_children > 2 * this
                            .no_of_units) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.room_type_nest == 2) {
                        if (this.no_of_adults > 1 * this.no_of_units || this.no_of_children > 0) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.property_type == `Agri Farm Kabana`) {
                        if (this.no_of_adults > 2 * this.no_of_units || this.no_of_children > 2 * this
                            .no_of_units) {
                            alert(
                                "Sorry, the maximum number of people that can be accommodated has been exceeded."
                            );
                        } else {
                            $("#check_form").submit();
                        }
                    } else if (this.property_type == `Agri Farm Dining Room`) {

                        $("#check_form").submit();

                    } else if (this.property_type == `Audio Visual Unit`) {

                        $("#check_form").submit();

                    }


                }




            }
        });

        function formSubmitHr() {

            $("#hr_Proceed_booking").submit();

        }

        function checkAvailability() {

            const bookingTypes = ["", "Master Room", "Single Bed with Shared Room", "Whole Resort", "Single Room with Shared Bathroom" , "Single Room with Attached Bathroom"];

            let booking_type = jQuery('#booking_type').val();
            let check_in_date = jQuery('#check_in_date').val();
            let check_out_date = jQuery('#check_out_date').val();

            if (booking_type === "" || check_in_date === "" || check_out_date === "") {
                alert("Please enter valid data");
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            jQuery.ajax({
                url: "{{ url('/check-availability-hr') }}",
                method: 'post',
                data: {
                    booking_type: booking_type,
                    CheckInDate: check_in_date,
                    CheckOutDate: check_out_date
                },
                success: function(result) {

                    if (result.is_available) {
                        $("#btn_proceedToBooking").css("display", "block");
                        if (result.available_resort_count > 0) {
                            $("#lable_availability").html(bookingTypes[booking_type] + "s Available");
                            $("#availability_count").val(result.available_resort_count);
                        }
                        if (result.available_rooms_count > 0) {
                            $("#lable_availability").html(bookingTypes[booking_type] + "s Available");
                            $("#availability_count").val(result.available_rooms_count);
                        }
                        if (result.available_beds_count > 0) {
                            $("#lable_availability").html(bookingTypes[booking_type] + "s Available");
                            $("#availability_count").val(result.available_beds_count);
                        }

                        alert('Your Search Is available, Please click Proceed to booking button!');
                    } else {
                        $("#btn_proceedToBooking").css("display", "none");
                        $("#availability_count").val("Not Available");
                        $("#lable_availability").html("Availability");
                        alert('Sorry! We dont have any available ' + bookingTypes[booking_type] + ' for you');
                    }

                }
            });
        }
    </script>
@endsection

@section('sidebar')
    @parent
    <p>This is appended to the sidebar</p>
@endsection
