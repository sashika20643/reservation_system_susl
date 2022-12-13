<?php

namespace App\Http\Controllers;

use App\Models\hrbooking;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\RejectMail;
use App\Mail\ConfirmMail;
use Auth;
use App\Mail\SendMail;
use App\Mail\RegistarMail;
use App\Mail\RequestRecommendMail;
use App\Models\BookingAssignedRooms;
use App\Models\Resorts;
use App\Models\Rooms;
use PDF;
use Carbon\Carbon;


class ViewHrBookingController extends Controller
{
    public function viewhrbooking(Request $request)
    {

        //$hrbookings = DB::select('select * from hrbookings');

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        } else {

            //            $hrbookings = DB::select( DB::raw("SELECT *
            //FROM hrbookings
            //INNER JOIN holidayresorts ON holidayresorts.HolodayResortId=hrbookings.HolodayResortId") );

            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                //                ->when('hrbookings.BookingType','==','SUSL Staff')
                //                ->where('hrbookings.BookingType','SUSL Staff','Local Visitor')
                //                ->where('hrbookings.BookingType','Local Visitor')
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        }

        return view('viewhrbooking', ['hrbookings' => $hrbookings]);
    }


    public function viewreporthrbooking(Request $request)
    {

        //$hrbookings = DB::select('select * from hrbookings');

        if ($request->input('CheckInDate') != null  && $request->input('CheckOutDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                //->where('CheckInDate', $request->input('CheckInDate'))
                ->whereDate('CheckInDate', '>=', $request->input('CheckInDate'))
                ->whereDate('CheckOutDate', '<=', $request->input('CheckOutDate'))
                ->where('is_temp', '=', 0)
                ->paginate(10);
        } else if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                //->where('CheckInDate', $request->input('CheckInDate'))
                ->where('CheckInDate',  $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->paginate(10);
        } else if ($request->input('CheckOutDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckOutDate',  $request->input('CheckOutDate'))
                ->where('is_temp', '=', 0)
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('is_temp', '=', 0)
                ->paginate(10);
        }

        return view('viewreporthrbooking', ['hrbookings' => $hrbookings]);
    }
    public function downloadpdf(Request $request)
    {
        //dd($request->input('CheckOutDate'));
        //$hrbookings = DB::select('select * from hrbookings');
        if ($request->input('CheckInDate') != null && $request->input('CheckOutDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                //->where('CheckInDate', $request->input('CheckInDate'))
                ->whereDate('CheckInDate', '>=', $request->input('CheckInDate'))
                ->whereDate('CheckOutDate', '<=', $request->input('CheckOutDate'))
                ->where('is_temp', '=', 0)
                ->get();
        } else if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->get();
        } else if ($request->input('CheckOutDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckOutDate', $request->input('CheckOutDate'))
                ->where('is_temp', '=', 0)
                ->get();
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('is_temp', '=', 0)
                ->get();
        }

        view()->share('hrbookings', $hrbookings);
        $pdf = PDF::loadView('viewhrbooking_pdf', compact($hrbookings));

        return $pdf->download('details.pdf');
    }

    public function downloadmonthpdf(Request $request)
    {


        $hrbookings = DB::table('hrbookings')
            ->select('hrbookings.*', 'holidayresorts.Type')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->whereMonth('CheckInDate', Carbon::now()->month)
            ->where('is_temp', '=', 0)
            ->get();

        view()->share('hrbookings', $hrbookings);
        $pdf = PDF::loadView('viewhrbooking_pdf', compact($hrbookings));

        return $pdf->download('details.pdf');
    }


    public function downloadyearpdf(Request $request)
    {


        $hrbookings = DB::table('hrbookings')
            ->select('hrbookings.*', 'holidayresorts.Type')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->whereYear('CheckInDate', Carbon::now()->year)
            ->where('is_temp', '=', 0)
            ->get();

        view()->share('hrbookings', $hrbookings);
        $pdf = PDF::loadView('viewhrbooking_pdf', compact($hrbookings));

        return $pdf->download('details.pdf');
    }


    public function viewreghrbooking(Request $request)
    {

        //$hrbookings = DB::select('select * from hrbookings');

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        }

        return view('viewreghrbooking', ['hrbookings' => $hrbookings]);
    }

    public function viewcthrbooking(Request $request)
    {

        //$hrbookings = DB::select('select * from hrbookings');

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        }

        return view('viewcthrbooking', ['hrbookings' => $hrbookings]);
    }

    public function viewvchrbooking(Request $request)
    {

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('is_temp', '=', 0)
                ->orderBy('BookingId', 'DESC')
                ->paginate(10);
        }

        return view('viewvchrbooking', ['hrbookings' => $hrbookings]);
    }

    public function viewdeanhodhrbooking(Request $request)
    {


        $Recommendation_From = Auth::id();

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where(['hrbookings.Recommendation_From' => $Recommendation_From])
                ->where('is_temp', '=', 0)
                ->orderBy('hrbookings.BookingId', 'DESC')
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where(['hrbookings.Recommendation_From' => $Recommendation_From])
                ->where('is_temp', '=', 0)
                ->orderBy('hrbookings.BookingId', 'DESC')
                ->paginate(10);
        }


        //$hrbookings = DB::select('select * from hrbookings where Recommendation_From = ?', [$Recommendation_From]);


        return view('viewdeanhodhrbooking', ['hrbookings' => $hrbookings]);
    }


    public function viewguesthrbooking(Request $request)
    {


        $GuestId = Auth::id();

        if ($request->input('CheckInDate') != null) {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where('CheckInDate', $request->input('CheckInDate'))
                ->where(['hrbookings.GuestId' => $GuestId])
                ->where('is_temp', '=', 0)
                ->orderBy('hrbookings.BookingId', 'DESC')
                ->paginate(10);
        } else {
            $hrbookings = DB::table('hrbookings')
                ->select('hrbookings.*', 'holidayresorts.Type')
                ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
                ->where(['hrbookings.GuestId' => $GuestId])
                ->where('is_temp', '=', 0)
                ->orderBy('hrbookings.BookingId', 'DESC')
                ->paginate(10);
        }


        return view('viewguesthrbooking', ['hrbookings' => $hrbookings]);
    }

    public function confirm(Request $request, $BookingId)
    {

        $data = $BookingId;

        //$GuestId = DB::select('select GuestId from avubookings where BookingId = ?', [$data]);
        $GuestId = DB::table('hrbookings')->where('BookingId', [$BookingId])->value('GuestId');
        $email = DB::table('users')->where('id', [$GuestId])->value('email');
        //$email = DB::select('select email from users where id = ?', [$GuestId]);

        $Status = 'Confirmed';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.";
        echo 'Click Here to go back.';

        Mail::to($email)->send(new ConfirmMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }



    public function reject(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Rejected';
        $GuestId = DB::table('hrbookings')->where('BookingId', [$BookingId])->value('GuestId');
        $email = DB::table('users')->where('id', [$GuestId])->value('email');

        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                ";
        echo 'Click Here to go back.';

        Mail::to($email)->send(new RejectMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function regconfirm(Request $request, $BookingId)
    {

        $data = $BookingId;

        //$GuestId = DB::select('select GuestId from avubookings where BookingId = ?', [$data]);
        $GuestId = DB::table('hrbookings')->where('BookingId', [$BookingId])->value('GuestId');
        $email = DB::table('users')->where('id', [$GuestId])->value('email');
        //$email = DB::select('select email from users where id = ?', [$GuestId]);

        $Status = 'Approved By General Admin';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.";
        echo 'Click Here to go back.';

        //Mail::to($email)->send(new ConfirmMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }



    public function regreject(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = ' Registrar Not Approved ';
        $GuestId = DB::table('hrbookings')->where('BookingId', [$BookingId])->value('GuestId');
        $email = DB::table('users')->where('id', [$GuestId])->value('email');

        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                        ";
        echo 'Click Here to go back.';

        //Mail::to($email)->send(new RejectMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function recommend(Request $request, $BookingId)
    {

        $data = $BookingId;

        $Status = 'Recommended';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.";
        echo 'Click Here to go back.';

        $email = DB::select('select email from users where roleNo = 4');
        Mail::to($email)->send(new RequestRecommendMail($data));

        return back()->with('success', 'Updated Successfuly!');
    }


    public function notrecommend(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Not Recommended';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                    ";
        echo 'Click Here to go back.';

        $email = DB::select('select email from users where roleNo = 12');
        Mail::to($email)->send(new RejectMail($data));

        return back()->with('success', 'Updated Successfuly!');
    }


    public function hrapprove(Request $request, $BookingId)
    {

        $data = $BookingId;

        $Status = 'Approved By Vice Chancellor';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.";
        echo 'Click Here to go back.';


        return back()->with('success', 'Updated Successfuly!');
    }



    public function hrnotapprove(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Not Approved';
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                        ";
        echo 'Click Here to go back.';


        return back()->with('success', 'Updated Successfuly!');
    }

    public function showhrvc($id)
    {

        $users = DB::table('hrbookings')
            ->select('hrbookings.*', 'users.*', 'holidayresorts.Type')
            ->join('users', 'users.id', '=', 'hrbookings.GuestId')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->where(['hrbookings.BookingId' => $id])
            ->get();

        //$users = DB::select('select * from hrbookings where BookingId = ?',[$id]);
        return view('hrvc_view', ['users' => $users]);
    }

    public function showhrdean($id)
    {

        $users = DB::table('hrbookings')
            ->select('hrbookings.*', 'users.*', 'holidayresorts.Type')
            ->join('users', 'users.id', '=', 'hrbookings.GuestId')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->where(['hrbookings.BookingId' => $id])
            ->get();

        //$users = DB::select('select * from hrbookings where BookingId = ?',[$id]);
        return view('hrdean_view', ['users' => $users]);
    }

    public function showhr($id)
    {

        $users = DB::table('hrbookings')
            ->select('hrbookings.*', 'users.*', 'holidayresorts.Type')
            ->join('users', 'users.id', '=', 'hrbookings.GuestId')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->where(['hrbookings.BookingId' => $id])
            ->where('is_temp', '=', 0)
            ->get();
        //$users = DB::select('select * from hrbookings where BookingId = ?',[$id]);
        return view('hr_view', ['users' => $users]);
    }

    public function showreghr($id)
    {

        $users = DB::table('hrbookings')
            ->select('hrbookings.*', 'users.*', 'holidayresorts.Type')
            ->join('users', 'users.id', '=', 'hrbookings.GuestId')
            ->join('holidayresorts', 'holidayresorts.HolodayResortId', '=', 'hrbookings.HolodayResortId')
            ->where(['hrbookings.BookingId' => $id])
            ->where('is_temp', '=', 0)
            ->get();
        //$users = DB::select('select * from hrbookings where BookingId = ?',[$id]);
        return view('hrreg_view', ['users' => $users]);
    }

    public function update(Request $request, $BookingId)
    {

        $NoOfUnits = $request->input('NoOfUnits');
        $NoOfChildren = $request->input('NoOfChildren');
        $NoOfAdults = $request->input('NoOfAdults');
        $Checked = $request->input('Checked');
        DB::update('update hrbookings set NoOfAdults=?,NoOfChildren=?,NoOfUnits=?,Checked=? where BookingId = ?', [$NoOfAdults, $NoOfChildren, $NoOfUnits, $Checked, $BookingId]);
        echo "Record updated successfully.
                                ";
        echo 'Click Here to go back.';

        return back()->with('success', 'Updated Successfuly!');
    }

    public function vcapprove(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Request Vice Chancellor Approval';

        // $users =DB::table('hrbookings')
        // ->select('hrbookings.*','users.name','holidayresort.Type')
        // ->join('users','users.id','=','hrbookings.Recommendation_From')
        // ->join('holidayresort','holidayresort.HolodayResortId','=','hrbookings.HolodayResortId')
        // ->where(['hrbookings.BookingId' => $BookingId])
        // ->get();
        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                            ";
        echo 'Click Here to go back.';

        $email = DB::select('select email from users where roleNo = 2');

        Mail::to($email)->send(new SendMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function regapprove(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Request Head of the General Administration Division Approval';

        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                                ";
        echo 'Click Here to go back.';

        $email = DB::select('select email from users where roleNo = 7');

        Mail::to($email)->send(new RegistarMail($data));
        return back()->with('success', 'Message Sent Successfuly!');
    }
    public function addheadcomment(Request $request, $BookingId)
    {

        $HODComment = $request->input('HODComment');
        DB::update('update hrbookings set HODComment=? where BookingId = ?', [$HODComment, $BookingId]);
        echo "Record updated successfully.
                                ";
        echo 'Click Here to go back.';

        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function addvccomment(Request $request, $BookingId)
    {

        $VCComment = $request->input('VCComment');
        DB::update('update hrbookings set VCComment=? where BookingId = ?', [$VCComment, $BookingId]);
        echo "Record updated successfully.
                                    ";
        echo 'Click Here to go back.';

        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function addregcomment(Request $request, $BookingId)
    {

        $RegComment = $request->input('RegComment');
        DB::update('update hrbookings set RegComment=? where BookingId = ?', [$RegComment, $BookingId]);
        echo "Record updated successfully.
                                        ";
        echo 'Click Here to go back.';

        return back()->with('success', 'Message Sent Successfuly!');
    }

    public function getRecommendation(Request $request, $BookingId)
    {
        $data = $BookingId;
        $Status = 'Send to Recommendation';


        DB::update('update hrbookings set Status = ? where BookingId = ?', [$Status, $BookingId]);
        echo "Record updated successfully.
                                    ";
        echo 'Click Here to go back.';

        $email = DB::table('hrbookings')
            ->select('users.email')
            ->join('users', 'users.id', '=', 'hrbookings.Recommendation_From')
            ->where(['hrbookings.BookingId' => $BookingId])
            ->where('is_temp', '=', 0)
            ->get();
        Mail::to($email)->send(new RequestRecommendMail($data));

        //$Recommendation_From = DB::select('select Recommendation_From from nestbookings where BookingId =  ?',[$BookingId]);

        //$email = DB::select('select email from users where id = ?', [$Recommendation_From]);
        //dd($Recommendation_From,$email);

        return back()->with('success', 'Message Sent Successfuly!');
    }


    //    public function edit(Request $request,$BookingId) {
    //     $VCApproval = 1;
    //     DB::update('update hrbookings set VCApproval = ? where BookingId = ?',[$VCApproval,$BookingId]);
    //     echo "Record updated successfully.
    //     ";
    //     echo 'Click Here to go back.';
    //     }


    public function requestPayment($id)
    {
        $status = 'Payment Requested';
        $state = DB::update('update hrbookings set Status = ? where BookingId = ?', [$status, $id]);
        if ($state != 1) return redirect()->back()->with('success', 'Somthing went wrong');
        return redirect()->back()->with('success', 'Payment requested invitation send successfully!');
    }

    public function cancelBooking($id)
    {
        $status = 'Cancelled';
        $state = DB::update('update hrbookings set Status = ? where BookingId = ?', [$status, $id]);
        if ($state != 1) return redirect()->back()->with('success', 'Somthing went wrong');
        return redirect()->back()->with('success', 'Reservation cancelled!');
    }

    public function createResortsRooms()
    {
        return view('createResortsRooms');
    }

    public function createResort(Request $request)
    {
        $isAdded = Resorts::where('resort_code', $request->resort_code)->get();

        if (sizeof($isAdded) > 0) {
            return back()->with('error', 'Resort name not available!');
        }

        $resort = new Resorts();
        $resort->resort_code = $request->resort_code;
        $resort->save();

        return back()->with('success', 'Resort Added Successfuly!');
    }

    public function createRoom(Request $request)
    {
        $isAdded = Rooms::where('room_code', $request->room_code)->get();

        if (sizeof($isAdded) > 0) {
            return back()->with('error', 'Room name not available!');
        }

        if ($request->room_type == "Master") {
            $request->beds = 1;
            $request->is_shared_bathroom = 0;
            $request->is_available_for_resource_person = 0;
        }
        $room = new Rooms();
        $room->resorts_id = $request->resort;
        $room->room_code = $request->room_code;
        $room->room_type = $request->room_type;
        $room->beds_count = $request->beds;
        $room->is_shared_bathroom = $request->is_shared_bathroom;
        $room->is_available_for_resource_person = $request->is_available_for_resource_person;
        $room->save();

        $resort = Resorts::find($request->resort);

        if ($request->room_type == "Master") {
            $resort->rooms_master_count = $resort->rooms_master_count + 1;
        } else {

            if ($request->is_shared_bathroom == 1) {
                $resort->rooms_single_count = $resort->rooms_single_count + 1;
            } else {
                $resort->rooms_single_with_attached_bathroom_count = $resort->rooms_single_with_attached_bathroom_count + 1;
            }

            if (isset($request->is_available_for_resource_person) && $request->is_available_for_resource_person == 1) {
                $resort->beds_count = $resort->beds_count + $request->beds;
            }
        }
        $resort->save();

        return back()->with('success', 'Room Added Successfuly!');
    }

    public function resortDetails($resortId)
    {

        $resort = Resorts::where('id', $resortId)->first();
        $rooms = Rooms::where('resorts_id', $resortId)
            ->where('is_deleted', 0)
            ->get();

        $data = array(
            'resort' => $resort,
            'rooms' => $rooms,
        );

        return response()->json($data, 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function resortStatusChange(Request $request, $resortId, $status)
    {
        $resort = Resorts::find($resortId);
        $resort->status = $status;
        $resort->block_reason = $request->reason;
        $resort->save();

        foreach (Rooms::where('resorts_id', $resortId)
            ->get() as $room) {

            $this->roomStatusChange($request, $room->id, $status, true);
        }


        return response()->json(["success" => true], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function roomStatusChange(Request $request, $roomId, $status, $is_internal = false)
    {
        $room = Rooms::find($roomId);
        $room->status = $status;
        $room->block_reason = $request->reason;
        $room->save();

        $resort = Resorts::find($room->resorts_id);
        if ($status == 1) {
            if ($room->room_type == "Master") {
                $resort->rooms_master_count = $resort->rooms_master_count + 1;
            } else {

                if ($room->is_shared_bathroom == 1) {
                    $resort->rooms_single_count = $resort->rooms_single_count + 1;
                } else {
                    $resort->rooms_single_with_attached_bathroom_count = $resort->rooms_single_with_attached_bathroom_count + 1;
                }

                if ($room->is_available_for_resource_person == 1) {
                    $resort->beds_count = $resort->beds_count + $room->beds_count;
                }
            }
            $resort->status = 1;
        } else {
            if ($room->room_type == "Master") {
                $resort->rooms_master_count = $resort->rooms_master_count - 1;
            } else {

                if ($room->is_shared_bathroom == 1) {
                    $resort->rooms_single_count = $resort->rooms_single_count - 1;
                } else {
                    $resort->rooms_single_with_attached_bathroom_count = $resort->rooms_single_with_attached_bathroom_count - 1;
                }

                if ($room->is_available_for_resource_person == 1) {
                    $resort->beds_count = $resort->beds_count - $room->beds_count;
                }
            }
        }
        $resort->save();
        if (!$is_internal) {
            return response()->json(["success" => true], 200)
                ->header('Access-Control-Allow-Origin', '*');
        } else {
            return 1;
        }
    }

    public function resortDelete($resortId)
    {
        $resort = Resorts::find($resortId);
        $resort->is_deleted = 1;
        $resort->save();

        return response()->json(["success" => true], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function roomDelete($roomId)
    {
        $room = Rooms::find($roomId);
        $room->is_deleted = 1;
        $room->save();

        $resort = Resorts::find($room->resorts_id);

        if ($room->room_type == "Master") {
            $resort->rooms_master_count = $resort->rooms_master_count - 1;
        } else {

            if ($room->is_shared_bathroom == 1) {
                $resort->rooms_single_count = $resort->rooms_single_count - 1;
            } else {
                $resort->rooms_single_with_attached_bathroom_count = $resort->rooms_single_with_attached_bathroom_count - 1;
            }

            if ($room->is_available_for_resource_person == 1) {
                $resort->beds_count = $resort->beds_count - $room->beds_count;
            }
        }

        $resort->save();

        return response()->json(["success" => true], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function checkAvailability(Request $request, $is_local = false, $local_data = null)
    {

        //  Master Room => 1
        //  Single Bed with Shared Room => 2
        //  Whole Resort => 3
        //  Single Room with Shared Bathroom => 4
        //  Single Room with Attached Bathroom => 5

        $is_available = false;
        $booking_completed_status = 'Confirmed';

        $availableResortNames = array();
        $available_rooms_count = 0;
        $available_beds_count = 0;

        $check_in_date = Carbon::parse($request->CheckInDate)->format('Y-m-d') . " 00:00:00";;
        $check_out_date = Carbon::parse($request->CheckOutDate)->format('Y-m-d') . " 23:59:59";;

        if ($is_local) {
            $check_in_date = Carbon::parse($local_data['CheckInDate'])->format('Y-m-d') . " 00:00:00";
            $check_out_date = Carbon::parse($local_data['CheckOutDate'])->format('Y-m-d') . " 23:59:59";
            $request->booking_type = $local_data['booking_type'];
        }

        $resorts = Resorts::where('is_deleted', 0)
            ->where('status', '=', 1)
            ->get();

        // Whole Resort Availability Check
        if ($request->booking_type == 3) {

            $bookings = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('resorts_id', '!=', '')
              //  ->where('booking_type', '=', 'Whole Resort')
                ->where('is_temp', '=', 0)
                ->select(['resorts_id'])
                ->get();

            if (count($bookings) == 0) {
                $availableResortNames = $resorts;
            } else {
                $resort_ids = array();
                foreach ($bookings as $booking) {
                    $resort_ids[] = $booking->resorts_id;
                }

                $availableResortNames = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();
            }
        }

        // Master Room Availability Check
        if ($request->booking_type == 1) {
            $bookings = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Master bed room')
                // ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '!=', 0)
                ->get();

            $bookings_whole_resort = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Whole Resort')
                ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '=', 0)
                ->get();

            if (count($bookings) == 0 && count($bookings_whole_resort) == 0) {
                foreach ($resorts as $resort) {
                    $available_rooms_count = $available_rooms_count + $resort->rooms_master_count;
                }
            } else {
                $all_rooms_count = 0;
                $booked_rooms_count = 0;

                $resort_ids = array();
                foreach ($bookings_whole_resort as $booking) {
                    $resort_ids[] = $booking->resorts_id;
                }

                $availableResorts = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();

                foreach ($availableResorts as $resort) {
                    $all_rooms_count += $resort->rooms_master_count;
                }

                foreach ($bookings as $booking) {
                    $booked_rooms_count += $booking->rooms_count;
                }

                $available_rooms_count = $all_rooms_count - $booked_rooms_count;
            }
        }

        // Single Room with Shared Bathroom
        if ($request->booking_type == 4) {
            $bookings = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Single Room with Shared Bathroom')
                // ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '!=', 0)
                ->get();

            $bookings_whole_resort = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Whole Resort')
                ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '=', 0)
                ->get();

            if (count($bookings) == 0 && count($bookings_whole_resort) == 0) {
                foreach ($resorts as $resort) {
                    $available_rooms_count = $available_rooms_count + $resort->rooms_single_count;
                }
            } else {
                $all_rooms_count = 0;
                $booked_rooms_count = 0;

                $resort_ids = array();
                foreach ($bookings_whole_resort as $booking) {
                    $resort_ids[] = $booking->resorts_id;
                }

                $availableResorts = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();

                foreach ($availableResorts as $resort) {
                    $all_rooms_count += $resort->rooms_single_count;
                }

                foreach ($bookings as $booking) {
                    $booked_rooms_count += $booking->rooms_count;
                }

                $available_rooms_count = $all_rooms_count - $booked_rooms_count;
            }
        }

        // Single Room with Attached Bathroom
        if ($request->booking_type == 5) {
            $bookings = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Single Room with Attached Bathroom')
                // ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '!=', 0)
                ->get();

            $bookings_whole_resort = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Whole Resort')
                ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('rooms_count', '=', 0)
                ->get();

            if (count($bookings) == 0 && count($bookings_whole_resort) == 0) {
                foreach ($resorts as $resort) {
                    $available_rooms_count = $available_rooms_count + $resort->rooms_single_with_attached_bathroom_count;
                }
            } else {
                $all_rooms_count = 0;
                $booked_rooms_count = 0;

                $resort_ids = array();
                foreach ($bookings_whole_resort as $booking) {
                    $resort_ids[] = $booking->resorts_id;
                }

                $availableResorts = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();

                foreach ($availableResorts as $resort) {
                    $all_rooms_count += $resort->rooms_single_with_attached_bathroom_count;
                }

                foreach ($bookings as $booking) {
                    $booked_rooms_count += $booking->rooms_count;
                }

                $available_rooms_count = $all_rooms_count - $booked_rooms_count;
            }
        }

        // Single Beds Availability Check
        if ($request->booking_type == 2) {
            $bookings = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Single Bed')
                // ->where('resorts_id', '!=', '')
                // ->where('is_temp', '=', 0)
                ->where('beds_count', '!=', 0)
                ->get();

            $bookings_whole_resort = hrbooking::where('CheckInDate', '>=', $check_in_date)
                ->where('CheckOutDate', '<=', $check_out_date)
                ->where('Status', '=', $booking_completed_status)
                ->where('booking_type', '=', 'Whole Resort')
                ->where('resorts_id', '!=', '')
                ->where('is_temp', '=', 0)
                ->where('beds_count', '=', 0)
                ->get();

            if (count($bookings) == 0 && count($bookings_whole_resort) == 0) {
                foreach ($resorts as $resort) {
                    $available_beds_count = $available_beds_count + $resort->beds_count;
                }
            } else {
                $all_beds_count = 0;
                $booked_beds_count = 0;

                $resort_ids = array();
                foreach ($bookings_whole_resort as $booking) {
                    $resort_ids[] = $booking->resorts_id;
                }

                $availableResorts = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();

                foreach ($availableResorts as $resort) {
                    $all_beds_count += $resort->beds_count;
                }

                foreach ($bookings as $booking) {
                    $booked_beds_count += $booking->beds_count;
                }

                $available_beds_count = $all_beds_count - $booked_beds_count;
            }
        }

        if (count($availableResortNames) > 0 || $available_rooms_count > 0 || $available_beds_count > 0) {
            $is_available = true;
        }

        if (!$is_local) {

            $sessionData = $request->input();
            $sessionData['property'] = "Holiday Resort";
            $sessionData['HolodayResortId'] = null;
            $sessionData['NoOfUnits'] = 0;
            $sessionData['NoOfAdults'] = 0;
            $sessionData['NoOfChildren'] = 0;

            Session::put('CheckAvailabilityRequest', $sessionData);

            return response()->json([
                "is_available" => $is_available,
                "available_resort_count" => count($availableResortNames),
                "available_rooms_count" => $available_rooms_count,
                "available_beds_count" => $available_beds_count,
            ], 200)
                ->header('Access-Control-Allow-Origin', '*');
        } else {
            return [
                "is_available" => $is_available,
                "available_resort_count" => count($availableResortNames),
                "available_resorts" => $availableResortNames,
                "available_rooms_count" => $available_rooms_count,
                "available_beds_count" => $available_beds_count,
            ];
        }
    }

    public function proceedToBooking(Request $request)
    {
        if (Auth::check()) {
            return redirect('/hr')->with(session()->flash('alert-success', 'Available'));
        }
        return redirect('/login')->with(session()->flash('alert-success', 'Available.Please loggin to the system for booking.'));
    }

    public function assignBookingsResortsRooms()
    {
        return view('assignBookingsResortsRooms');
    }

    public function bookingDetails(Request $request)
    {

        $is_all = $request->is_all;
        $is_manage = $request->is_manage;

        $check_in_date = "";
        $check_out_date = "";
        if ($is_all == "false") {
            $check_in_date = Carbon::parse($request->checkInDate)->format('Y-m-d') . " 00:00:00";
            $check_out_date = Carbon::parse($request->checkOutDate)->format('Y-m-d') . " 23:59:59";
        }

        $bookings = hrbooking::where('Status', '=', 'Confirmed')
            ->when($is_manage == "true", function ($query) {
                $query->Where('pending_units', '!=', '0');
            }, function ($query) {
                $query->where('is_assigned', '=', '1');
            })
            ->when($is_all == "false", function ($query) use ($check_in_date, $check_out_date) {
                $query->where('hrbookings.CheckInDate', '>=', $check_in_date)
                    ->where('hrbookings.CheckOutDate', '<=', $check_out_date);
            })
            ->where('is_temp', '=', 0)
            ->get();

        return response()->json([
            "bookings" => $bookings
        ], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function getResortsForAssign(Request $request)
    {

        $check_in_date = Carbon::parse($request->check_in)->format('Y-m-d');
        $check_out_date = Carbon::parse($request->check_out)->format('Y-m-d');

        $bookings = BookingAssignedRooms::where('check_in_date', '>=', $check_in_date)
            ->where('check_out_date', '<=', $check_out_date)
            ->get();

        $availableResorts = Resorts::where('is_deleted', 0)
            ->where('status', '=', 1)
            ->get();

        if ($bookings) {

            if ($request->type === "Whole Resort") {
                $resort_ids = array();
                foreach ($bookings as $booking) {
                    $resort_ids[] = $booking->resort_id;
                }

                $availableResorts = Resorts::where('is_deleted', 0)
                    ->whereNotIn('id', $resort_ids)
                    ->where('status', '=', 1)
                    ->get();
            }
        }

        return $availableResorts;
    }

    public function getRoomsForAssign(Request $request)
    {

        $check_in_date = Carbon::parse($request->check_in_date)->format('Y-m-d');
        $check_out_date = Carbon::parse($request->check_out_date)->format('Y-m-d');

        $bookings = BookingAssignedRooms::where('check_in_date', '>=', $check_in_date)
            ->where('check_out_date', '<=', $check_out_date)
            ->where('resort_id', '=', $request->resort_id)
            ->where('type', '!=', 'Whole Resort')
            ->get();

        if ($request->type == 'Master bed room') {
            $availableRooms = Rooms::where('is_deleted', 0)
                ->where('status', '=', 1)
                ->where('resorts_id', '=', $request->resort_id)
                ->where('room_type', '=', 'Master')
                ->get();
        }
        if ($request->type == 'Single Room with Shared Bathroom') {
            $availableRooms = Rooms::where('is_deleted', 0)
                ->where('status', '=', 1)
                ->where('resorts_id', '=', $request->resort_id)
                ->where('room_type', '=', 'Single')
                ->where('is_shared_bathroom', '=', '1')
                ->get();
        }
        if ($request->type == 'Single Room with Attached Bathroom') {
            $availableRooms = Rooms::where('is_deleted', 0)
                ->where('status', '=', 1)
                ->where('resorts_id', '=', $request->resort_id)
                ->where('room_type', '=', 'Single')
                ->where('is_shared_bathroom', '=', '0')
                ->get();
        }
        if ($request->type == 'Single Bed') {
            $availableRooms = Rooms::where('is_deleted', 0)
                ->where('status', '=', 1)
                ->where('resorts_id', '=', $request->resort_id)
                ->where('is_shared_bathroom', '=', 1)
                ->where('room_type', '=', 'Single')
                ->where('is_available_for_resource_person', '=', 1)
                ->get();
        }

        if ($bookings) {

            $room_ids = array();
            foreach ($bookings as $booking) {
                $room_ids[] = $booking->room_id;
            }
            if ($request->type == "Single Bed") {

                $availableRooms = Rooms::where('is_deleted', 0)
                    ->whereNotIn('id', $room_ids)
                    ->where('status', '=', 1)
                    ->where('resorts_id', '=', $request->resort_id)
                    ->where('is_available_for_resource_person', '=', 1)
                    ->where('room_type', '=', 'Single')
                    ->where('is_available_for_resource_person', '=', 1)
                    ->get();
            } else {

                // $availableRooms = Rooms::where('is_deleted', 0)
                //     ->whereNotIn('id', $room_ids)
                //     ->where('status', '=', 1)
                //     ->where('resorts_id', '=', $request->resort_id)
                //     ->get();

                if ($request->type == 'Master bed room') {
                    $availableRooms = Rooms::where('is_deleted', 0)
                        ->whereNotIn('id', $room_ids)
                        ->where('status', '=', 1)
                        ->where('resorts_id', '=', $request->resort_id)
                        ->where('room_type', '=', 'Master')
                        ->get();
                }
                if ($request->type == 'Single Room with Shared Bathroom') {
                    $availableRooms = Rooms::where('is_deleted', 0)
                        ->whereNotIn('id', $room_ids)
                        ->where('status', '=', 1)
                        ->where('resorts_id', '=', $request->resort_id)
                        ->where('room_type', '=', 'Single')
                        ->where('is_shared_bathroom', '=', '1')
                        ->get();
                }
                if ($request->type == 'Single Room with Attached Bathroom') {
                    $availableRooms = Rooms::where('is_deleted', 0)
                        ->whereNotIn('id', $room_ids)
                        ->where('status', '=', 1)
                        ->where('resorts_id', '=', $request->resort_id)
                        ->where('room_type', '=', 'Single')
                        ->where('is_shared_bathroom', '=', '0')
                        ->get();
                }
            }
        }

        return $availableRooms;
    }

    public function assingRoomsForBooking(Request $request)
    {
        $check_in_date = Carbon::parse($request->check_in)->format('Y-m-d');
        $check_out_date = Carbon::parse($request->check_out)->format('Y-m-d');
        $booking_id = $request->booking_id;
        $type = $request->type;
        $selectedRooms = $request->selectedRooms;
        $units = $request->units;

        $booking_count = 0;
        if (str_contains($selectedRooms, '$')) {

            $dataAll = explode('$', $selectedRooms);

            foreach ($dataAll as $booking) {

                $booking_count++;

                $data = explode('#', $booking);

                $room_id = $data[2] == "N/A" ? 0 : $data[3];

                $bookingAssigne = new BookingAssignedRooms();

                $bookingAssigne->hr_booking_id = $booking_id;
                $bookingAssigne->resort_id = $data[1];
                $bookingAssigne->room_id = $room_id;
                $bookingAssigne->type = $type;
                $bookingAssigne->bed_count = 1;
                $bookingAssigne->check_in_date = $check_in_date;
                $bookingAssigne->check_out_date = $check_out_date;

                $bookingAssigne->save();

                if ($room_id != "N/A") {
                    $this->addTempBookingsForBed($room_id, $check_in_date, $check_out_date);
                }
            }
        } else {
            $booking_count++;

            $data = explode('#', $selectedRooms);

            $room_id = $data[2] == "N/A" ? 0 : $data[3];

            $bookingAssigne = new BookingAssignedRooms();

            $bookingAssigne->hr_booking_id = $booking_id;
            $bookingAssigne->resort_id = $data[1];
            $bookingAssigne->room_id = $room_id;
            $bookingAssigne->type = $type;
            $bookingAssigne->bed_count = 1;
            $bookingAssigne->check_in_date = $check_in_date;
            $bookingAssigne->check_out_date = $check_out_date;

            $bookingAssigne->save();

            if ($room_id != "N/A") {
                $this->addTempBookingsForBed($room_id, $check_in_date, $check_out_date);
            }
        }

        hrbooking::where("BookingId", '=', $booking_id)
            ->update(
                [
                    "is_assigned" => 1,
                    "pending_units" => $units - $booking_count,
                    "resorts_id" => $data[1],
                    "beds_count" => $type == "Single Bed" ? $units : 0,
                    "rooms_count" => $type != "Single Bed" ? $units : 0,
                ]
            );

        return response()->json([
            "sucess" => true
        ], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function assingRoomsView(Request $request)
    {

        $bookingAssigne = BookingAssignedRooms::where('booking_assigned_rooms.hr_booking_id', '=', $request->booking_id)
            ->join('resorts', 'resorts.id', '=', 'booking_assigned_rooms.resort_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'booking_assigned_rooms.room_id')
            ->select(['booking_assigned_rooms.id', 'resorts.resort_code', 'rooms.room_code'])
            ->get();

        return $bookingAssigne;
    }

    public function assingRoomsRemove(Request $request)
    {
        $bookingAssigne = BookingAssignedRooms::find($request->booking_id);

        $hrBooking = hrbooking::where("BookingId", '=', $bookingAssigne->hr_booking_id)
            ->first();

        hrbooking::where("BookingId", '=', $bookingAssigne->hr_booking_id)
            ->update(
                [
                    "pending_units" => $hrBooking->pending_units + 1
                ]
            );

        $bookingAssigne->delete();

        return true;
    }

    public function openHrBookingReport()
    {
        return view('hrBookingReport');
    }

    public function loadHrBookingReport(Request $request)
    {
        $is_all = $request->is_all;

        $check_in_date = "";
        $check_out_date = "";
        if ($is_all == "false") {
            $check_in_date = Carbon::parse($request->checkInDate)->format('Y-m-d') . " 00:00:00";
            $check_out_date = Carbon::parse($request->checkOutDate)->format('Y-m-d') . " 23:59:59";
        }

        $bookings = hrbooking::where('hrbookings.Status', '=', 'Confirmed')
            ->join('resorts', 'resorts.id', '=', 'hrbookings.resorts_id')
            ->where('hrbookings.is_assigned', '=', '1')
            ->where('hrbookings.pending_units', '=', '0')
            ->when($is_all == "false", function ($query) use ($check_in_date, $check_out_date) {
                $query->where('hrbookings.CheckInDate', '>=', $check_in_date)
                    ->where('hrbookings.CheckOutDate', '<=', $check_out_date);
            })
            ->select(['hrbookings.BookingId', 'hrbookings.GuestName', 'hrbookings.created_at', 'hrbookings.CheckInDate', 'hrbookings.CheckOutDate', 'hrbookings.booking_type', 'hrbookings.NoOfUnits', 'resorts.resort_code'])
            ->get();

        $booking_data = array();

        foreach ($bookings as $booking) {

            $bookedRooms = array();
            $bookedResorts = array();

            $bookingAssigne = BookingAssignedRooms::where('booking_assigned_rooms.hr_booking_id', '=', $booking->BookingId)
                ->join('rooms', 'rooms.id', '=', 'booking_assigned_rooms.room_id')
                ->join('resorts', 'resorts.id', '=', 'booking_assigned_rooms.resort_id')
                ->select(['booking_assigned_rooms.id', 'rooms.room_code', 'resorts.resort_code'])
                ->get();

                $resort_code = "";
            foreach ($bookingAssigne as $room) {
                $bookedRooms[] = $room->room_code;
                $bookedResorts[] = $room->resort_code;
                $resort_code = $room->resort_code;
            }
            $rooms = "All";
            if (count($bookedRooms) > 0) {
                $rooms = implode(", ", $bookedRooms);
            }
            $resort = "";
            if (count($bookedRooms) > 0) {
                $resort = implode(", ", $bookedResorts);
            }

            if ($rooms == "All") {
                $resort = $resort_code;
            }

            $booking->rooms = $rooms;
            $booking->resort = $resort;
            $booking_data[] = $booking;
        }

        return response()->json([
            "bookings" => $booking_data
        ], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function addTempBookingsForBed($roomId, $check_in_date, $check_out_date)
    {
        $room = Rooms::find($roomId);

        if ($room->is_available_for_resource_person == "1") {

            $hrbooking = new hrbooking;
            $hrbooking->BookingType = "Temporary";
            $hrbooking->CheckInDate = $check_in_date;
            $hrbooking->CheckOutDate = $check_out_date;

            $hrbooking->NoOfAdults = $room->beds_count;
            $hrbooking->NoOfChildren = 0;
            $hrbooking->NoOfUnits = $room->beds_count;
            $hrbooking->pending_units = 0;
            $hrbooking->beds_count = $room->beds_count;

            $hrbooking->NormalOrFree = "Free";
            $hrbooking->booking_type = "Single Bed";

            $hrbooking->Description = "Tem Bookings";


            $hrbooking->Status = 'Confirmed';
            $hrbooking->Recommendation_from = 13;


            $hrbooking->payment_total = 0;

            $hrbooking->GuestId = Auth::user()->id;
            $hrbooking->GuestName = Auth::user()->name;
            $hrbooking->HolodayResortId =  2;
            $hrbooking->save();
        }
    }
}
