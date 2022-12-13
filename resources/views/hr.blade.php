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
    <!-- Holiday Resort booking page -->
    <div class=" card border-primary ">
        <h1>Holiday Resort</h1>
        </br>
        <p style="margin-left: 10px;">
            Holiday Resort is the main circuit bungalows of the Sabaragamuwa University of Sri Lanka. The university
            provides accommodation
            facility to the guest of the university by using these holiday resort bungalows. 
        </p>
        <p style="margin-left: 10px;">Payments as follows</p>

        <table class="table">
            <thead class="table-primary">
                <tr>
                    <td>Customer Type</td>

                    <td>Master Room</td>
                    <td>Single Room(Attached bathroom)</td>
                    <td>Single Room(shared bathroom)</td>
                    <td>Single Bed</td>
                    <td>Whole Resort</td>
                </tr>



            </thead>
            <tbody>
                <tr>
                    <td>Sabaragamuwa University Staff</td>
                    <td>800.00 </td>
                    <td>800.00 </td>
                    <td>800.00 </td>
                    <td> - </td>
                    <td>2400.00 </td>

                </tr>

                <tr>
                    <td>Other University Staff </td>
                    <td>1100.00</td>
                    <td>1100.00</td>
                    <td>1100.00</td>
                    <td> - </td>
                    <td>3400.00</td>

                </tr>
                <tr>
                    <td>Resource Persons</td>
                    <td> - </td>
                    <td> - </td>
                    <td> - </td>
                    <td>200.00</td>
                    <td> - </td>

                </tr>

                <tr>
                    <td>Local Visitors</td>
                    <td>2000.00</td>
                    <td>2000.00</td>
                    <td>2000.00</td>
                    <td> - </td>
                    <td>10000.00</td>

                </tr>

            </tbody>
        </table>
If you have any issue please contact General Admin Office through 045 2280009
    </div>
    </br>

    <div class="card p-3 mb-2 bg-secondary text-white" id="holiday_resort_booking">
        <h5 class="card-header">Booking</h5>
        <div class="card-body">
            <div class="mb-3">


                {!! Form::model($sessionData, ['route' => ['hr_submit'], 'method' => 'POST', 'id' => 'booking_form']) !!}

                <div class="form-group">
                    {{ Form::label('BookingType', 'Booking for ') }}

                    @if (isset($sessionData->booking_type) && $sessionData->booking_type != 2)
                    {{ Form::select('BookingType', ['SUSL Staff' => 'SUSL Staff', 'Local Visitor' => 'Local Visitor', 'Other University Staff' => 'Other University Staff'], null, ['class' => 'form-control', 'v-model' => 'booking_type']) }}
                    @else
                    {{ Form::select('BookingType', ['Resource Person' => 'Resource Person'], null, ['class' => 'form-control', 'v-model' => 'booking_type']) }}
                    @endif

                </div>

                <div class="form-group ">
                    {!! Form::label('Room Type') !!}
                    {!! Form::select('HolodayResortId', $hrfill, null, ['class' => 'form-control', 'v-model' => 'room_type']) !!}

                </div>

                <div class="form-group">
                    {{ Form::label('NoOfUnits', 'Number Of Units') }}
                    {{ Form::text('NoOfUnits', '', ['class' => 'form-control', 'placeholder' => 'Number Of Units', 'v-model' => 'no_of_units', 'v-on:change' => 'checkUnitsCount']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('NoOfAdults', 'Number Of Adults') }}
                    {{ Form::text('NoOfAdults', '', ['class' => 'form-control', 'placeholder' => 'Number Of Adults', 'v-model' => 'no_of_adults']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('NoOfChildren', 'Number Of Children') }}
                    {{ Form::text('NoOfChildren', '', ['class' => 'form-control', 'placeholder' => 'Number Of Children', 'v-model' => 'no_of_children']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('CheckInDate', 'Check in Date Time') }}
                    {{ Form::datetimeLocal('CheckInDate', !empty($sessionData) ? Input::old('CheckInDate') : null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('CheckOutDate', 'Check out Date Time') }}
                    {{ Form::datetimeLocal('CheckOutDate', !empty($sessionData) ? Input::old('CheckInDate') : null, ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('NormalOrFree', 'Normal or Free') }}
                    {{ Form::select('NormalOrFree', ['Normal' => 'Normal', 'Free' => 'Free'], null, ['placeholder' => 'Please select ...', 'class' => 'form-control']) }}

                </div>

                <div class="form-group">
                    {{ Form::label('Description', 'Description') }}
                    {{ Form::textarea('Description', '', ['class' => 'form-control', 'placeholder' => 'Description']) }}
                </div>

                </br>
                {{ Form::button('Submit', ['class' => 'btn btn-primary', 'v-on:click' => 'formSubmit']) }}
            </div>
            {!! Form::close() !!}

        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.js"></script>

    <script>
        const holiday_resort = new Vue({
            el: '#holiday_resort_booking',
            data() {
                return {
                    booking_type: null,
                    room_type: {{ !empty($sessionData) ? $sessionData->booking_type : 'null' }},
                    no_of_units: {{ !empty($sessionData) ? $sessionData->NoOfUnits : 0 }},
                    no_of_adults: {{ !empty($sessionData) ? $sessionData->NoOfAdults : 0 }},
                    no_of_children: {{ !empty($sessionData) ? $sessionData->NoOfChildren : 0 }}


                }
            },

            methods: {

                checkUnitsCount() {

                    // if (this.room_type == 1 && this.no_of_units > 7) {
                    //     this.no_of_units = 0;
                    //     alert('Sorry, You can not book more than 7 units.')
                    // }
                    // if (this.room_type == 2 && this.no_of_units > 28) {
                    //     this.no_of_units = 0;
                    //     alert('Sorry, You can not book more than 28 units.')
                    // }
                    // if (this.room_type == 3 && this.no_of_units > 7) {
                    //     this.no_of_units = 0;
                    //     alert('Sorry, You can not book more than 7 units.')
                    // }

                },

                formSubmit() {
                    //     if (this.room_type == 1) {
                    //         if (this.no_of_adults > 2 * this.no_of_units || this.no_of_children > 1 * this
                    //             .no_of_units) {
                    //             alert(
                    //                 "Sorry, the maximum number of people that can be accommodated has been exceeded. Please check the number of units and number of guests");
                    //         } else {
                    //             $("#booking_form").submit();
                    //         }
                    //     } else if (this.room_type == 2) {
                    //         if (this.no_of_adults > 1 * this.no_of_units || this.no_of_children > 0) {
                    //             alert(
                    //                 "Sorry, the maximum number of people that can be accommodated has been exceeded. Please check the number of units and number of guests");
                    //         } else {
                    //             $("#booking_form").submit();
                    //         }
                    //     } else if (this.room_type == 3) {
                    //         if (this.no_of_adults > 6 * this.no_of_units || this.no_of_children > 1 * this
                    //             .no_of_units) {
                    //             alert(
                    //                 "Sorry, the maximum number of people that can be accommodated has been exceeded. Please check the number of units and number of guests");
                    //         } else {
                    //             $("#booking_form").submit();
                    //         }
                    //     }

                    //     // else if(this.room_type == 3){
                    //     //     if(this.no_of_adults > 6*this.no_of_units || this.no_of_children > 1*this.no_of_units)){
                    //     //         alert("Sorry, the maximum number of people that can be accommodated has been exceeded. Please check the number of units and number of guests");
                    //     //     }else{
                    //     //         $("#booking_form").submit();
                    //     //     }
                    $("#booking_form").submit();
                }


            }
            // }
        });
    </script>
@endsection

<!-- @section('sidebar')
    @parent
                    <p>This is appended to the sidebar</p>
@endsection -->
