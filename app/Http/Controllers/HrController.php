<?php

namespace App\Http\Controllers;

use App\Models\HoliydayResortPayment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\holidayresort;
use App\Models\hrbooking;
use Auth;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\hremail;
use DB;
use Session;
use Illuminate\Support\Facades\Input;

//to handle holiday resort details
class HrController extends Controller
{
    public function gethr()
    {

        $sessionData = [];

        // Check the availability session exist or not
        if (Session::has('CheckAvailabilityRequest')) {
            $sessionData = (object)Session::get('CheckAvailabilityRequest');
            //dd(Session::all());

            if ($sessionData->property !== "Holiday Resort") {
                Session::forget('CheckAvailabilityRequest');
                $sessionData = NULL;
            }
        }

        $hr = holidayresort::all();
        $hrdetail = DB::select('select * from holidayresorts');
        $hrfill = [];
        foreach ($hrdetail as $n) {
            $hrfill[$n->HolodayResortId] = $n->Type;
        }
        $Users = User::where('roleNo', '>=', 11)->get();
        $select = [];
        foreach ($Users as $User) {
            $select[$User->id] = $User->name;
        }

        return view('hr', compact('select', 'hrfill', 'hr', 'sessionData'));
    }

    public function submit(Request $request)
    {

        $Department = Auth::user()->Department;
        $hod = User::select('id')
            ->where('Department', '=', [$Department])
            ->where('Designation', '=', 'Head of The Department')
            ->get();
        $hodEmail = User::select('email')
            ->where('Department', '=', [$Department])
            ->where('Designation', '=', 'Head of The Department')
            ->get();

        $this->validate($request, [


            'BookingType' => 'required',
            'CheckInDate' => 'required|date|after:-1 days',
            'CheckOutDate' => 'required|date|after:CheckInDate',
            'NoOfAdults' => 'required|numeric|min:1',
            'NoOfChildren' => 'required|numeric|min:0',
            'NoOfUnits' => 'required|numeric|min:1',
            'Description' => 'required',
            // 'Recommendation_from'=>"required_if:BookingType,==,Resource Person,SUSL Staff",
            //            'HolodayResortId'=>'required',
        ]);

        $t1 = Carbon::parse($request->CheckInDate);
        $t2 = Carbon::parse($request->CheckOutDate);
        $diff = $t1->diff($t2);

        $hoursPerDay = 0;
        $diff->h > 0 ? $hoursPerDay++ : '';

        $totalDays = $diff->d + $hoursPerDay;
        $holidayPayment = HoliydayResortPayment::where('booking_type', $request->input('BookingType'))->first();


        $totalPayments = 0;

        $data = array(
            "CheckInDate" => $request->input('CheckInDate'),
            "CheckOutDate" => $request->input('CheckOutDate'),
            "booking_type" => $request->input('HolodayResortId')
        );

        $availability = (new ViewHrBookingController())->checkAvailability(new Request(), true, $data);

        // dd($availability);

        if (!$availability['is_available']) {
            return back()->with('error', 'Sorry Allready Booked!');
        } else {
            if ($request->input('HolodayResortId') == 3 && $availability['available_resort_count'] < $request->input('NoOfUnits')) {
                return back()->with('error', 'Sorry Only ' . $availability['available_resort_count'] . ' Resorts Available');
            }
            if ($request->input('HolodayResortId') == 1 && $availability['available_rooms_count'] < $request->input('NoOfUnits')) {
                return back()->with('error', 'Sorry Only ' . $availability['available_rooms_count'] . ' Rooms Available');
            }
            if ($request->input('HolodayResortId') == 2 && $availability['available_beds_count'] < $request->input('NoOfUnits')) {
                return back()->with('error', 'Sorry Only ' . $availability['available_beds_count'] . ' Rooms Available');
            }
        }

        if ($request->input('HolodayResortId') == 3) {
            //Whole Resort

            if ($holidayPayment->full_resort == 0) {
                return back()->with('error', 'Sorry, You Can`t Book This Kind Of Accommodation');
            }

            if ($request->input('NoOfUnits') > 1) {
                return back()->with('error', 'Sorry You Can Book Only Single Resort At a Time');
            }

            $totalPayments = $holidayPayment->full_resort * $totalDays * (+$request->input('NoOfUnits'));


            $hrbooking = new hrbooking;
            $hrbooking->BookingType = $request->input('BookingType');
            $hrbooking->CheckInDate = $request->input('CheckInDate');
            $hrbooking->CheckOutDate = $request->input('CheckOutDate');

            $hrbooking->NoOfAdults = $request->input('NoOfAdults');
            $hrbooking->NoOfChildren = $request->input('NoOfChildren');
            $hrbooking->NoOfUnits = $request->input('NoOfUnits');
            $hrbooking->pending_units = $request->input('NoOfUnits');

            $hrbooking->NormalOrFree = $request->input('NormalOrFree');
            $hrbooking->booking_type = "Whole Resort";

            $hrbooking->Description = $request->input('Description');

            if ($request->input('NormalOrFree') == 'Normal') {
                if (($request->input('BookingType') == 'Local Visitor') || ($request->input('BookingType') == 'Other University Staff')) {
                    $hrbooking->Status = 'Request for Booking';

                    $email = DB::select('select email from users where id = 12');
                }
                if (($request->input('BookingType') == 'Resource Person') || ($request->input('BookingType') == 'SUSL Staff')) {
                    $hrbooking->Status = 'Send to Recommendation';
                    $department = Auth::user()->Department;
                    $email = $hodEmail;
                }
                if ($request->input('BookingType') == "Resource Person" || $request->input('BookingType') == "SUSL Staff") {
                    $hrbooking->Recommendation_from = count($hod)>0 ? $hod[0]->id : 12;
                    if ($hrbooking->Recommendation_from == 12) {
                        $hrbooking->Status = 'Recommended';
                        }
                } else {
                    $hrbooking->Recommendation_from = 13;
                }
            } else {
                $hrbooking->Status = 'Request for Booking';
                $hrbooking->Recommendation_from = 13;
                $email = DB::select('select email from users where id = 12');
            }


            $hrbooking->payment_total = $totalPayments;

            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  $request->input('HolodayResortId');
            $hrbooking->save();


            $data = array(
                'id'      =>  Auth::user()->id,
                'name'      =>  Auth::user()->name,
                'CheckInDate' => $request->input('CheckInDate'),
                'CheckOutDate' => $request->input('CheckOutDate'),
                'NoOfUnits' => $request->input('NoOfUnits'),
                'Description' => $request->input('Description')
            );

            Mail::to($email)->send(new hremail($data));
            return back()->with('success', 'Request Sent Successfuly!');
        }
        if ($request->input('HolodayResortId') == 1) {
            //Master bed room

            if ($holidayPayment->master_room == 0) {
                return back()->with('error', 'Sorry, You Can`t Book This Kind Of Accommodation');
            }

            $totalPayments = $holidayPayment->master_room * $totalDays * (+$request->input('NoOfUnits'));


            $hrbooking = new hrbooking;
            $hrbooking->BookingType = $request->input('BookingType');
            $hrbooking->CheckInDate = $request->input('CheckInDate');
            $hrbooking->CheckOutDate = $request->input('CheckOutDate');

            $hrbooking->NoOfAdults = $request->input('NoOfAdults');
            $hrbooking->NoOfChildren = $request->input('NoOfChildren');
            $hrbooking->NoOfUnits = $request->input('NoOfUnits');
            $hrbooking->rooms_count = $request->input('NoOfUnits');
            $hrbooking->pending_units = $request->input('NoOfUnits');

            $hrbooking->NormalOrFree = $request->input('NormalOrFree');
            $hrbooking->booking_type = "Master bed room";

            $hrbooking->Description = $request->input('Description');

            if ($request->input('NormalOrFree') == 'Normal') {
                if (($request->input('BookingType') == 'Local Visitor') || ($request->input('BookingType') == 'Other University Staff')) {
                    $hrbooking->Status = 'Request for Booking';

                    $email = DB::select('select email from users where id = 12');
                }
                if (($request->input('BookingType') == 'Resource Person') || ($request->input('BookingType') == 'SUSL Staff')) {
                    $hrbooking->Status = 'Send to Recommendation';
                    $department = Auth::user()->Department;
                    $email = $hodEmail;
                }
                if ($request->input('BookingType') == "Resource Person" || $request->input('BookingType') == "SUSL Staff") {
                    $hrbooking->Recommendation_from = count($hod)>0 ? $hod[0]->id : 12;
                    if ($hrbooking->Recommendation_from == 12) {
                        $hrbooking->Status = 'Recommended';
                        }
                } else {
                    $hrbooking->Recommendation_from = 13;
                }
            } else {
                $hrbooking->Status = 'Request for Booking';
                $hrbooking->Recommendation_from = 13;
                $email = DB::select('select email from users where id = 12');
            }



            $hrbooking->payment_total = $totalPayments;



            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  $request->input('HolodayResortId');
            $hrbooking->save();

            //data array which pass details to hrmail
            $data = array(
                'id'      =>  Auth::user()->id,
                'name'      =>  Auth::user()->name,
                'CheckInDate' => $request->input('CheckInDate'),
                'CheckOutDate' => $request->input('CheckOutDate'),
                'NoOfUnits' => $request->input('NoOfUnits'),
                'Description' => $request->input('Description')
            );

            //send mail to hr coordinator
            Mail::to($email)->send(new hremail($data));

            return back()->with('success', 'Request Sent Successfuly!');
        }

        if ($request->input('HolodayResortId') == 4) {
            //Single Room with Shared Bathroom

            if ($holidayPayment->single_room_shared_bathroom == 0) {
                return back()->with('error', 'Sorry, You Can`t Book This Kind Of Accommodation');
            }

            $totalPayments = $holidayPayment->single_room_shared_bathroom * $totalDays * (+$request->input('NoOfUnits'));


            $hrbooking = new hrbooking;
            $hrbooking->BookingType = $request->input('BookingType');
            $hrbooking->CheckInDate = $request->input('CheckInDate');
            $hrbooking->CheckOutDate = $request->input('CheckOutDate');

            $hrbooking->NoOfAdults = $request->input('NoOfAdults');
            $hrbooking->NoOfChildren = $request->input('NoOfChildren');
            $hrbooking->NoOfUnits = $request->input('NoOfUnits');
            $hrbooking->rooms_count = $request->input('NoOfUnits');
            $hrbooking->pending_units = $request->input('NoOfUnits');

            $hrbooking->NormalOrFree = $request->input('NormalOrFree');
            $hrbooking->booking_type = "Single Room with Shared Bathroom";

            $hrbooking->Description = $request->input('Description');

            if ($request->input('NormalOrFree') == 'Normal') {
                if (($request->input('BookingType') == 'Local Visitor') || ($request->input('BookingType') == 'Other University Staff')) {
                    $hrbooking->Status = 'Request for Booking';

                    $email = DB::select('select email from users where id = 12');
                }
                if (($request->input('BookingType') == 'Resource Person') || ($request->input('BookingType') == 'SUSL Staff')) {
                    $hrbooking->Status = 'Send to Recommendation';
                    $department = Auth::user()->Department;
                    $email = $hodEmail;
                }
                if ($request->input('BookingType') == "Resource Person" || $request->input('BookingType') == "SUSL Staff") {
                    $hrbooking->Recommendation_from = count($hod)>0 ? $hod[0]->id : 12;
                  
                    if ($hrbooking->Recommendation_from == 12) {
                        $hrbooking->Status = 'Recommended';
                        }
                } else {
                    $hrbooking->Recommendation_from = 13;
                }
            } else {
                $hrbooking->Status = 'Request for Booking';
                $hrbooking->Recommendation_from = 13;
                $email = DB::select('select email from users where id = 12');
            }



            $hrbooking->payment_total = $totalPayments;



            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  $request->input('HolodayResortId');
            $hrbooking->save();

            //data array which pass details to hrmail
            $data = array(
                'id'      =>  Auth::user()->id,
                'name'      =>  Auth::user()->name,
                'CheckInDate' => $request->input('CheckInDate'),
                'CheckOutDate' => $request->input('CheckOutDate'),
                'NoOfUnits' => $request->input('NoOfUnits'),
                'Description' => $request->input('Description')
            );

            //send mail to hr coordinator
            Mail::to($email)->send(new hremail($data));

            return back()->with('success', 'Request Sent Successfuly!');
        }

        if ($request->input('HolodayResortId') == 5) {
            //Single Room with Attached Bathroom

            if ($holidayPayment->single_room_attached_bathroom == 0) {
                return back()->with('error', 'Sorry, You Can`t Book This Kind Of Accommodation');
            }

            $totalPayments = $holidayPayment->single_room_attached_bathroom * $totalDays * (+$request->input('NoOfUnits'));


            $hrbooking = new hrbooking;
            $hrbooking->BookingType = $request->input('BookingType');
            $hrbooking->CheckInDate = $request->input('CheckInDate');
            $hrbooking->CheckOutDate = $request->input('CheckOutDate');

            $hrbooking->NoOfAdults = $request->input('NoOfAdults');
            $hrbooking->NoOfChildren = $request->input('NoOfChildren');
            $hrbooking->NoOfUnits = $request->input('NoOfUnits');
            $hrbooking->rooms_count = $request->input('NoOfUnits');
            $hrbooking->pending_units = $request->input('NoOfUnits');

            $hrbooking->NormalOrFree = $request->input('NormalOrFree');
            $hrbooking->booking_type = "Single Room with Attached Bathroom";

            $hrbooking->Description = $request->input('Description');

            if ($request->input('NormalOrFree') == 'Normal') {
                if (($request->input('BookingType') == 'Local Visitor') || ($request->input('BookingType') == 'Other University Staff')) {
                    $hrbooking->Status = 'Request for Booking';

                    $email = DB::select('select email from users where id = 12');
                }
                if (($request->input('BookingType') == 'Resource Person') || ($request->input('BookingType') == 'SUSL Staff')) {
                    $hrbooking->Status = 'Send to Recommendation';
                    $department = Auth::user()->Department;
                    $email = $hodEmail;
                }
                if ($request->input('BookingType') == "Resource Person" || $request->input('BookingType') == "SUSL Staff") {
                    $hrbooking->Recommendation_from = count($hod)>0 ? $hod[0]->id : 12;
                    if ($hrbooking->Recommendation_from == 12) {
                        $hrbooking->Status = 'Recommended';
                        }
                } else {
                    $hrbooking->Recommendation_from = 13;
                }
            } else {
                $hrbooking->Status = 'Request for Booking';
                $hrbooking->Recommendation_from = 13;
                $email = DB::select('select email from users where id = 12');
            }



            $hrbooking->payment_total = $totalPayments;



            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  $request->input('HolodayResortId');
            $hrbooking->save();

            //data array which pass details to hrmail
            $data = array(
                'id'      =>  Auth::user()->id,
                'name'      =>  Auth::user()->name,
                'CheckInDate' => $request->input('CheckInDate'),
                'CheckOutDate' => $request->input('CheckOutDate'),
                'NoOfUnits' => $request->input('NoOfUnits'),
                'Description' => $request->input('Description')
            );

            //send mail to hr coordinator
            Mail::to($email)->send(new hremail($data));

            return back()->with('success', 'Request Sent Successfuly!');
        }

        if ($request->input('HolodayResortId') == 2) {
            //Single Bed with Shared Room

            if ($holidayPayment->bed == 0) {
                return back()->with('error', 'Sorry, You Can`t Book This Kind Of Accommodation');
            }

            $totalPayments = $holidayPayment->bed * $totalDays * (+$request->input('NoOfUnits'));


            $hrbooking = new hrbooking;
            $hrbooking->BookingType = $request->input('BookingType');
            $hrbooking->CheckInDate = $request->input('CheckInDate');
            $hrbooking->CheckOutDate = $request->input('CheckOutDate');

            $hrbooking->NoOfAdults = $request->input('NoOfAdults');
            $hrbooking->NoOfChildren = $request->input('NoOfChildren');
            $hrbooking->NoOfUnits = $request->input('NoOfUnits');
            $hrbooking->pending_units = $request->input('NoOfUnits');
            $hrbooking->beds_count = $request->input('NoOfUnits');

            $hrbooking->NormalOrFree = $request->input('NormalOrFree');
            $hrbooking->booking_type = "Single Bed";

            $hrbooking->Description = $request->input('Description');

            if ($request->input('NormalOrFree') == 'Normal') {
                if (($request->input('BookingType') == 'Local Visitor') || ($request->input('BookingType') == 'Other University Staff')) {
                    $hrbooking->Status = 'Request for Booking';

                    $email = DB::select('select email from users where id = 12');
                }
                if (($request->input('BookingType') == 'Resource Person') || ($request->input('BookingType') == 'SUSL Staff')) {
                    $hrbooking->Status = 'Send to Recommendation';
                    $department = Auth::user()->Department;
                    $email = $hodEmail;
                }
                if ($request->input('BookingType') == "Resource Person" || $request->input('BookingType') == "SUSL Staff") {
                    $hrbooking->Recommendation_from = count($hod)>0 ? $hod[0]->id : 12;
                    if ($hrbooking->Recommendation_from == 12) {
                        $hrbooking->Status = 'Recommended';
                        }
                } else {
                    $hrbooking->Recommendation_from = 13;
                }
            } else {
                $hrbooking->Status = 'Request for Booking';
                $hrbooking->Recommendation_from = 13;
                $email = DB::select('select email from users where id = 12');
            }

            $hrbooking->payment_total = $totalPayments;

            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  $request->input('HolodayResortId');
            $hrbooking->save();

            //data array which pass details to hrmail
            $data = array(
                'id'      =>  Auth::user()->id,
                'name'      =>  Auth::user()->name,
                'CheckInDate' => $request->input('CheckInDate'),
                'CheckOutDate' => $request->input('CheckOutDate'),
                'NoOfUnits' => $request->input('NoOfUnits'),
                'Description' => $request->input('Description')
            );
            //send mail to hr coordinator
            Mail::to($email)->send(new hremail($data));

            return back()->with('success', 'Request Sent Successfuly!');
        }
        return redirect('/')->with('danger', 'Sorry Allready Booked!');
    }



    // function send(Request $request)
    // {
    //  $this->validate($request, [

    //  ]);

    //  $data = array(
    //     'id'      =>  Auth::user()->id,
    //     'name'      =>  Auth::user()->name,

    // );

    //         Mail::to('ashansawijeratne@gmail.com')->send(new SendMail($data));
    //         return back()->with('success', 'Successfuly sent!');

    // }
}
