<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StorePharmacyRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'patient_id'     => 'nullable|string',
            'appointment_id' => 'nullable|string',
            'medicine_name'  => 'required|string|max:255',
            'dosage'         => 'required|string|max:100',
            'frequency'      => 'required|string|max:100',
            'quantity'       => 'required|integer|min:1',
            'instructions'   => 'nullable|string',
            'status'         => 'nullable|in:PENDING,PREPARING,READY_TO_PICKUP,DISPENSED',
        ];
    }
}