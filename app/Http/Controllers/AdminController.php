<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Doctor;

use App\Models\Appointment;

use Notification;

use App\Notifications\SendEmailNotification;

class AdminController extends Controller
{
    public function addview()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                return view('admin.add_doctor');
            }
            else
            {
                return redirect()->back();
            }
        }
        else{
            return redirect('login');
        }

       
    }

    public function upload(Request $request)
    {
        $doctor=new doctor;

        $image=$request->file;

        $imagename=time().'.'.$image->getClientoriginalExtension();

        $request->file->move('doctorimage',$imagename);

        $doctor->image=$imagename;

        $doctor->name=$request->name;

        $doctor->phone=$request->number;

        $doctor->room=$request->room;

        $doctor->specialty=$request->specialty;

        $doctor->save();

        return redirect()->back()->with('message','Doctor added successfully ');
    }

    public function showappointment()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {  
                $data=appointment::all();

                return view('admin.showappointment', compact('data'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }


    }

    public function approved($id)
    {

        $data=appointment::find($id);

        $data->status='approved';

        $data->save();

        return redirect()->back();
    }

    public function canceled($id)
    {

        $data=appointment::find($id);

        $data->status='canceled';

        $data->save();

        return redirect()->back();
    }

    public function showdoctor()
    {

        $data=doctor::all();

        return view('admin.showdoctor', compact('data'));
    }

    public function deletedoctor($id)
    {

        $data=doctor::find($id);

        $data->delete();

        return redirect()->back();
    }
    public function updatedoctor($id)
    {

        $data=doctor::find($id);

        return view('admin.update_doctor', compact('data'));
    }

    public function editdoctor(Request $request, $id)
    {

        $doctor=doctor::find($id);

        $doctor->name=$request->name;

        $doctor->phone=$request->number;

        $doctor->specialty=$request->specialty;

        $doctor->room=$request->room;

        // Check if a new file is uploaded
        $image=$request->file;

        if($image){
        
            // If a new file is uploaded, process it
            $imagename=time().'.'.$image->getClientOriginalExtension();

            // If a new file is uploaded, update the image field
            $request->file->move('doctorimage',$imagename);
            
            // If no new file is uploaded, keep the old image
            $doctor->image=$imagename;

        }

        $doctor->save();

        return redirect()->back()->with('message','Doctor details updated successfully ');
    }

    public function emailview($id)
    {
        $data=appointment::find($id);

        return view('admin.email_view',compact('data'));
    }

    public function sendemail(Request $request,$id)
    {
        $data = appointment::find($id);
        $details=[
            'greeting' => $request->greeting,
            'body' => $request->body,
            'actiontext' => $request->actiontext,
            'actionurl' => $request->actionurl,
            'endpart' => $request->endpart
        ];

        Notification::send($data,new SendEmailNotification($details));

        return redirect()->back()->with('message','Email sent successfully.');

    }
}
