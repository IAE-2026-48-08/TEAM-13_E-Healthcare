<?php
// StoreAppointmentRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'            => 'required|uuid|exists:patients,id',
            'doctor_name'           => 'required|string|max:255',
            'doctor_specialization' => 'nullable|string|max:255',
            'appointment_date'      => 'required|date',
            'status'                => 'nullable|in:SCHEDULED,IN_PROGRESS,COMPLETED,CANCELLED',
            'complaint'             => 'nullable|string',
            'diagnosis'             => 'nullable|string',
            'notes'                 => 'nullable|string',
        ];
    }
}
