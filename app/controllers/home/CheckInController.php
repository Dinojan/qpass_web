<?php 
namespace App\Controllers\Home;
use App\Core\Lib\Request;
class CheckInController {
    public $disable;

    public function __construct() {}

    /**
     * Show home page
     */
    public function index()
    {
        session()->forget('visitor');
        session()->forget('is_returned');
        return view('frontend.check-in.home-page');
    }

     public function scanQr()
    {
        return view('frontend.check-in.cameraPreview');
    }

      public function createStepOne(Request $request)
    {
        $employees = Employee::where( ['status'=> Status::ACTIVE]);
         $visitor = (object) $request->session()->get('visitor');


        $employee_id = "";
        $purpose = "";
        $company_name = "";
        $disable =  false;
       if (!empty($visitor) && isset($visitor->id)) {
            $role = authHelper()->user() ? authHelper()->user()->myrole : 0;
            if (session()->has('pre-register')) {
                $visitingDetails = PreRegister::where(['visitor_id', $visitor->id])->latest()->first();
                if (!empty($visitingDetails)) {
                    $employee_id = $visitingDetails->employee_id;
                }
            } else {
                $visitingDetails = VisitingDetails::where(['visitor_id', $visitor->id])->latest()->first();
                if (!empty($visitingDetails)) {
                    $company_name = $visitingDetails->company_name;
                    $employee_id = $visitingDetails->employee_id;
                    $purpose = $visitingDetails->purpose;
                }
            }
        }
        if (!empty($visitor) && isset($visitor->disable)) {
            $disable =  $visitor->disable;
        }

        return view('frontend.check-in.step-one', compact('employees', 'visitor', 'company_name', 'disable', 'employee_id', 'purpose'));
    }


    public function postCreateStepOne(Request $request)
    {
        if ($request->session()->get('is_returned') == false || empty($request->session()->get('is_returned'))) {
            $emailValidation = '';

            if (!empty($request->get('email'))) {
                $emailValidation = $request->validate([
                    'email' => 'unique:visitors,email',
                ]);
            }

            if (setting('terms_visibility_status')) {
                $validatedData = $request->validate([
                    'first_name'                 => 'required',
                    'last_name'                  => 'required',
                    'phone'                      => 'required|unique:visitors,phone',
                    'country_code'               => '',
                    'country_code_name'          => '',
                    'purpose'                    => 'required',
                    'employee_id'                => 'required|numeric',
                    'gender'                     => 'required|numeric',
                    'company_name'               => '',
                    'company_employee_id'        => '',
                    'national_identification_no' => 'required|unique:visitors,national_identification_no',
                    'is_group_enabled'           => '',
                    'address'                    => '',
                    'oldVisitor'                 => '',
                    'accept_tc'                  => 'accepted',
                ]);
            } else {
                $validatedData = $request->validate([
                    'first_name'                 => 'required',
                    'last_name'                  => 'required',
                    'phone'                      => 'required|unique:visitors,phone',
                    'country_code'               => '',
                    'country_code_name'          => '',
                    'purpose'                    => 'required',
                    'employee_id'                => 'required|numeric',
                    'gender'                     => 'required|numeric',
                    'company_name'               => '',
                    'company_employee_id'        => '',
                    'national_identification_no' => 'required|unique:visitors,national_identification_no',
                    'is_group_enabled'           => '',
                    'address'                    => '',
                    'oldVisitor'                 => '',
                ]);
            }


            if (!blank($emailValidation)) {
                $validatedData = array_merge($validatedData, $emailValidation);
            }
        } else {
            $visitor = Visitor::where('email', $request->get('email') == null ? '' : $request->get('email'))
                ->orWhere('phone', $request->get('phone') == null ? '' : $request->get('phone'))
                ->orWhere('national_identification_no', $request->get('national_identification_no') == null ? '' : $request->get('national_identification_no'))
                ->first();
            $national_identification_no = "";
            if ($visitor) {
                $email = blank($request->get('email')) ? '' : ['email', 'string', 'unique:visitors,email,' . $visitor->id];
                $phone = ['required', 'string', Rule::unique("visitors", "phone")->ignore($visitor)];
                $national_identification_no = ['required',  'string', 'unique:visitors,national_identification_no,' . $visitor->id];
            } else {
                $email                      = blank($request->get('email')) ? '' : ['email', 'string', 'unique:visitors,email'];
                $phone                      = [];
                $national_identification_no = ['required',  'string', 'unique:visitors,national_identification_no'];
            }

            if (setting('terms_visibility_status')) {
                $validatedData = $request->validate([
                    'first_name'                 => 'required',
                    'last_name'                  => 'required',
                    'email'                      => $email,
                    'phone'                      => $phone,
                    'country_code'               => '',
                    'country_code_name'          => '',
                    'purpose'                    => 'required',
                    'employee_id'                => 'required|numeric',
                    'gender'                     => 'required|numeric',
                    'company_name'               => '',
                    'company_employee_id'        => '',
                    'national_identification_no' => $national_identification_no,
                    'is_group_enabled'           => '',
                    'address'                    => '',
                    'oldVisitor'                 => '',
                    'accept_tc'                  => 'accepted',

                ]);
            } else {
                $validatedData = $request->validate([
                    'first_name'                 => 'required',
                    'last_name'                  => 'required',
                    'email'                      => $email,
                    'phone'                      => $phone,
                    'country_code'               => '',
                    'country_code_name'          => '',
                    'purpose'                    => 'required',
                    'employee_id'                => 'required|numeric',
                    'gender'                     => 'required|numeric',
                    'company_name'               => '',
                    'company_employee_id'        => '',
                    'national_identification_no' => $national_identification_no,
                    'is_group_enabled'           => '',
                    'address'                    => '',
                    'oldVisitor'                 => '',

                ]);
            }
        }
        $request->session()->put('visitor', $validatedData);
        return redirect()->route('check-in.step-two');
    }


    public function createStepTwo(Request $request)
    {
        // $visitingDetails = $request->session()->get('visitor');
        // $employee = Employee::find($visitingDetails['employee_id']);


        // $visitor = Visitor::where('phone', $visitingDetails['phone'])->first();
        // if ($visitor) {
        //     $visitorDetail = VisitingDetails::where('visitor_id', $visitor->id)->first();
        //     if ($visitorDetail) {
        //         $image = $visitorDetail->images;
        //     } elseif (!setting('photo_capture_enable')) {
        //         $image = '';
        //     } else {
        //         $image = 'default/user.png';
        //     }
        //     return view('frontend.check-in.step-two', compact('employee', 'visitingDetails', 'image'));
        // } else {
        //     if (setting('photo_capture_enable')) {
        //         $image = 'default/user.png';
        //     } else {
        //         $image = '';
        //     }
        //     return view('frontend.check-in.step-two', compact('employee', 'visitingDetails', 'image'));
        // }
    }


    public function store(Request $request)
    {
        // $getVisitor = $request->session()->get('visitor');
        // if ($getVisitor) {
        //     $imageName = null;
        //     if ($request->has('photo')) {
        //         if (setting('photo_capture_enable')) {
        //             $request->validate([
        //                 'photo' => 'required',
        //             ]);
        //         }

        //         $encoded_data = $request['photo'];
        //         $image = str_replace('data:image/png;base64,', '', $encoded_data);
        //         $image = str_replace(' ', '+', $image);
        //         $imageName = Str::random(10) . '.' . 'png';
        //         file_put_contents($imageName, base64_decode($image));
        //         $url = public_path($imageName);
        //         $optimizerChain = OptimizerChainFactory::create();
        //         $optimizerChain->optimize($url);
        //     }
        // } else {
        //     redirect()->route('check-in.step-one')->with('error', 'visitor information not found, fill again!');
        // }

        // $visitorID = DB::table('visiting_details')->max('id');
        // $visitorReg = VisitingDetails::find($visitorID);
        // $date = date('y-m-d');
        // $data = substr($date, 0, 2);
        // $data1 = substr($date, 3, 2);
        // $data2 = substr($date, 6, 8);
        // $today = $data2 . $data1 . $data;

        // if (!blank($visitorReg)) {
        //     $lastentrydmy = substr($visitorReg->reg_no, 0, 6);
        //     if ($lastentrydmy == $today) {
        //         $value = substr($visitorReg->reg_no, 6);
        //         $value += 1;
        //         $reg_no = $data2 . $data1 . $data . $value;
        //     } else {
        //         $reg_no = $data2 . $data1 . $data . '1';
        //     }
        // } else {
        //     $reg_no = $data2 . $data1 . $data . '1';
        // }
        // if ($request->session()->get('is_returned') == false || empty($request->session()->get('is_returned'))) {
        //     $input['first_name']                 = $getVisitor['first_name'];
        //     $input['last_name']                  = $getVisitor['last_name'];
        //     $input['email']                      = isset($getVisitor['email']) ? $getVisitor['email'] : "";
        //     $input['phone']                      = preg_replace("/[^0-9]/", "", $getVisitor['phone']);
        //     $input['country_code']               = $getVisitor['country_code'];
        //     $input['country_code_name']          = $getVisitor['country_code_name'];
        //     $input['gender']                     = $getVisitor['gender'];
        //     $input['address']                    = $getVisitor['address'];
        //     $input['national_identification_no'] = $getVisitor['national_identification_no'] ? $getVisitor['national_identification_no'] : "";
        //     $input['is_pre_register']            = false;
        //     $input['status']                     = Status::ACTIVE;
        //     $input['creator_id']                 = 1;
        //     $input['creator_type']               = 'App\Models\User';
        //     $input['editor_type']                = 'App\Models\User';
        //     $input['editor_id']                  = 1;
        //     //Qrcode Genarate
        //     $file_name = 'qrcode-' . preg_replace("/[^0-9]/", "", $getVisitor['phone']) . '.png';
        //     $input['barcode']   = $file_name;
        //     $file = public_path('qrcode/' . $file_name);
        //     // QRCode::size(300)->format('png')->generate(route('checkin.visitor-details', preg_replace("/[^0-9]/", "", $getVisitor['phone'])), $file);
        //     $visitor = Visitor::create($input);
        // } else {
        //     $visitor                             = Visitor::where('phone', $getVisitor['phone'])->first();
        //     $visitor->first_name                 = $getVisitor['first_name'];
        //     $visitor->last_name                  = $getVisitor['last_name'];
        //     $visitor->email                      = $getVisitor['email'];
        //     $visitor->national_identification_no = $getVisitor['national_identification_no'];
        //     $visitor->gender                     = $getVisitor['gender'];
        //     $visitor->address                    = $getVisitor['address'];
        //     $visitor->is_pre_register            = false;

        //     $file_name = 'qrcode-' . preg_replace("/[^0-9]/", "", $getVisitor['phone']) . '.png';
        //     $visitor->barcode = $file_name;
        //     $file = public_path('qrcode/' . $file_name);
        //     // QRCode::size(300)->format('png')->generate(route('checkin.visitor-details', preg_replace("/[^0-9]/", "", $getVisitor['phone'])), $file);
        //     $visitor->save();
        // }


        // if ($visitor) {
        //     $visiting['reg_no']       = $reg_no;
        //     $visiting['purpose']      = $getVisitor['purpose'];
        //     $visiting['company_name'] = $getVisitor['company_name'];
        //     $visiting['employee_id']  = $getVisitor['employee_id'];
        //     $visiting['visitor_id']   = $visitor->id;
        //     $visiting['status']       = VisitorStatus::PENDDING;
        //     $visiting['user_id']      = $getVisitor['employee_id'];
        //     $visiting['creator_id']   = 1;
        //     $visiting['creator_type'] = 'App\Models\User';
        //     $visiting['editor_type']  = 'App\Models\User';
        //     $visiting['editor_id']    = 1;
        //     $visitingDetails          = VisitingDetails::create($visiting);
        //     if ($imageName) {
        //         $visitingDetails->addMedia($imageName)->toMediaCollection('visitor');
        //         File::delete($imageName);
        //     }

        //     try {
        //         $token = app(JwtTokenService::class)->jwtToken($visitingDetails);
        //         $visitingDetails->employee->user->notify(new EmployeConfirmation($visitingDetails, $token));
        //     } catch (\Exception $e) {
        //         Log::info($e->getMessage());
        //     }

        //     try {
        //         app(PushNotificationService::class)->sendWebNotification($visitingDetails);
        //     } catch (\Exception $exception) {
        //         Log::info($exception->getMessage());
        //     }
        // }
        // return redirect()->route('check-in.show', $visitingDetails->id);
    }

     public function show(Request $request, $id)
    {
        // $visitingDetails = VisitingDetails::find($id);
        // $visitorDetail = VisitingDetails::where('visitor_id', $visitingDetails->visitor_id)->first();
        // $visitingDetails['photo'] = $visitorDetail->images;

        // if ($visitingDetails) {
        //     return view('frontend.check-in.show', compact('visitingDetails'));
        // } else {
        //     session()->forget('visitor');
        //     session()->forget('is_returned');
        //     return redirect('/check-in');
        // }
    }


    /**
     * Show pre-registered visitor form
     */
    public function pre_registered()
    {
        return view('frontend.check-in.pre-registered');
    }

    /**
     * Handle pre-registered visitor form submission
     */
    public function find_pre_visitor(Request $request)
    {
       

        // ✅ Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );

        if (!$validator->validate()) {
            set_errors($validator->errors());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $email = $request->input('email');

        // Simulate database lookup
        $visitor = []; // Replace with actual lookup (e.g. findVisitorByEmailOrPhone($email))

        if (!$visitor) {
            set_errors(['email' => __('all.visitor_not_found')]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // If visitor found
        return view('frontend.check-in.pre-registered-details', ['visitor' => $visitor]);
    }

    /**
     * Handle POST request with extra logic
     */
    public function find_visitor(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );

         if (!$validator->validate()) {
            set_errors($validator->errors());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $email = $request->input('email');

        // Simulate database lookup
        $visitor = []; // Replace with actual lookup (e.g. findVisitorByEmailOrPhone($email))

        if (!$visitor) {
            set_errors(['email' => __('all.visitor_not_found')]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }


        return header('Location: ' . Route::routeUrl('check-in.return'));
    }

    /**
     * Show return visitor page
     */
    public function visitor_return()
    {
        return view('frontend.check-in.return');
    }

    /**
     * Simulate visitor checking logic
     */
    private function checkVisitor($email)
    {
        // Replace this with actual DB lookup logic
        return false;
    }

    
    public function visitorDetails($visitorPhone)
    {
        // $visitor = Visitor::where('phone', $visitorPhone)->first();

        // if ($visitor === null) {
        //     $employee = Employee::where('phone', $visitorPhone)->first();

        //     if ($employee) {
        //         $checkout = Attendance::where(['user_id' => $employee->id, 'date' => date('Y-m-d')])->first();

        //         if ($checkout === null) {
        //             $checkout               = new Attendance;
        //             $checkout->title        = 'Office';
        //             $checkout->checkin_time = date('g:i A');
        //             $checkout->date         = date('Y-m-d');
        //             $checkout->user_id      = $employee->id;
        //             $checkout->save();

        //             return redirect()->route('home')->withSuccess("Check-in Successful!");
        //         } else {
        //             $checkout->checkout_time     = date('g:i A');
        //             $checkout->save();

        //             return redirect()->route('home')->withSuccess("Check-out Successful!");
        //         }
        //     } else {
        //         return redirect()->route('home')->withWarning('No record found!');
        //     }
        // } else {
        //     $visitor_Detail = VisitingDetails::select('visitor_id')->where('visitor_id', $visitor->id)->where('disable', true)->orderBy('created_at', 'desc')->first();

        //     //Check User block or not
        //     if ($visitor_Detail) {
        //         return redirect()->route('home')->with('error', 'This visitor has been Blocked!');
        //     } else {
        //         $visitorDetail = VisitingDetails::where('visitor_id', $visitor->id)->first();
        //         if (!empty($visitor)) {

        //             if ($visitorDetail) {

        //                 $visitor->image = $visitorDetail->images;
        //             }
        //             session()->put('visitor', $visitor);
        //             if (@Auth::user()->id == 1) {
        //                 $visitor->disable = false;
        //             } else {
        //                 $visitor->disable = true;
        //             }
        //             session()->put('is_returned', true);
        //             return redirect()->route('check-in.step-one');
        //         }
        //     }
        // }
    }

    public function preVisitorDetails($visitorPhone)
    {

        // $visitor = Visitor::where('phone', $visitorPhone)->first();

        // $visitor_Detail = VisitingDetails::select('visitor_id')->where('visitor_id', $visitor->id)->where('disable', true)->orderBy('created_at', 'desc')->first();

        // //Check User block or not
        // if ($visitor_Detail) {
        //     return redirect()->route('home')->with('error', 'This visitor has been Blocked!');
        // } else {

        //     $today = Carbon::now()->toDateString();
        //     if ($visitor) {
        //         $visitDetails = PreRegister::select('expected_date')->where('visitor_id', $visitor->id)->where('expected_date', '<=', $today)->first();

        //         if (!$visitDetails) {
        //             return redirect()->back()->with('error', 'Sorry, Your Appoinment date has not arrived yet !');
        //         }
        //     }
        //     if (!empty($visitor)) {
        //         $visitorDetail = VisitingDetails::where('visitor_id', $visitor->id)->first();
        //         if ($visitorDetail) {
        //             $visitor->image = $visitorDetail->images;
        //         }
        //         $preData = PreRegister::where('visitor_id', $visitor->id)->first();
        //         $visitor->employee_id = $preData->employee_id;

        //         if (@Auth::user()->id == 1) {
        //             $visitor->disable = false;
        //         } else {
        //             $visitor->disable = true;
        //         }

        //         session()->put('visitor', $visitor);
        //         session()->put('is_returned', true);
        //         return redirect()->route('check-in.step-one');
        //     }
        // }
    }
}
