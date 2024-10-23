<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // Fetch list of appointments
    public function fetchlist()
    {
        $appointments = Appointment::all();
        return response()->json($appointments);
    }

    // Store a newly created appointment in storage
    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:appointments,email',
            'reason' => 'required|string|max:500',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i', // Assuming time format is HH:mm
            'status' => 'nullable|string|in:Pending,Accepted,Suspended',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new appointment
        $appointment = Appointment::create($request->all());

        return response()->json($appointment, 201);
    }

    // Update the specified appointment in storage
    public function update(Request $request)
    {
        $appointment = Appointment::findOrFail($request->id); // Assuming you're sending the ID in the request body

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:appointments,email,' . $appointment->id,
            'reason' => 'required|string|max:500',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i', // Assuming time format is HH:mm
            'status' => 'nullable|string|in:Pending,Accepted,Suspended',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the appointment
        $appointment->update($request->all());

        return response()->json($appointment);
    }

    // Remove the specified appointment from storage
    public function remove(Request $request)
    {
        $appointment = Appointment::findOrFail($request->id); // Assuming you're sending the ID in the request body
        $appointment->delete();

        return response()->json(null, 204);
    }

    // Update the status of the appointment
    public function statusUpdate(Request $request)
    {
        $appointment = Appointment::findOrFail($request->id); // Assuming you're sending the ID in the request body

        // Validate the new status
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Pending,Accepted,Suspended',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the status
        $appointment->status = $request->status;
        $appointment->save();

        return response()->json($appointment);
    }
}