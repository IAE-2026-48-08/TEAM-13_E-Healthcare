<?php
// AppointmentResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'patient_id'            => $this->patient_id,
            'doctor_name'           => $this->doctor_name,
            'doctor_specialization' => $this->doctor_specialization,
            'appointment_date'      => $this->appointment_date,
            'status'                => $this->status,
            'complaint'             => $this->complaint,
            'diagnosis'             => $this->diagnosis,
            'notes'                 => $this->notes,
            'patient'               => new PatientResource($this->whenLoaded('patient')),
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
