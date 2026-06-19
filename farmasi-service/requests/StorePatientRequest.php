<?php
// StorePatientRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'nik'            => 'required|string|size:16|unique:patients,nik,' . $this->route('patient'),
            'email'          => 'required|email|unique:patients,email,' . $this->route('patient'),
            'phone'          => 'nullable|string|max:20',
            'date_of_birth'  => 'required|date',
            'gender'         => 'required|in:MALE,FEMALE',
            'address'        => 'nullable|string',
            'medical_history'=> 'nullable|string',
        ];
    }
}
