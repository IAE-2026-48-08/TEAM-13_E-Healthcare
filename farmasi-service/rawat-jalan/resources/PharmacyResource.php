<?php
// PharmacyResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'patient_id'     => $this->patient_id,
            'appointment_id' => $this->appointment_id,
            'medicine_name'  => $this->medicine_name,
            'dosage'         => $this->dosage,
            'frequency'      => $this->frequency,
            'quantity'       => $this->quantity,
            'instructions'   => $this->instructions,
            'status'         => $this->status,
            'patient'        => new PatientResource($this->whenLoaded('patient')),
            'appointment'    => new AppointmentResource($this->whenLoaded('appointment')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
