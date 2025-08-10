<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePassengerDetailRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama'                          => 'required',
            'email'                         => 'required',
            'nomor'                         => 'required',
            'passengers'                    => 'required|array|min:1',
            'passengers.*.nama'             => 'required',
            'passengers.*.date_of_birth'    => 'required',
            'passengers.*.kewarganegaraan'  => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'passengers.*.nama'             => 'Passenger nama',
            'passengers.*.date_of_birth'    => 'Passenger date of birth',
            'passengers.*.kewarganeraan'    => 'Passenger kewarganegaraan',
        ];
    }

    public function messages(){
        return [
            'passengers.*.nama.required'             => 'atribute field is required',
            'passengers.*.date_of_birth.required'    => 'atribute field is required',
            'passengers.*.kewarganeraan.required'    => 'atribute field is required',
        ];
    }
}
