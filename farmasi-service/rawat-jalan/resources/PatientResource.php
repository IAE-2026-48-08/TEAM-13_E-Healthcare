<?php
// PatientResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'nik'            => $this->nik,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'date_of_birth'  => $this->date_of_birth?->format('Y-m-d'),
            'gender'         => $this->gender,
            'address'        => $this->address,
            'medical_history'=> $this->medical_history,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
