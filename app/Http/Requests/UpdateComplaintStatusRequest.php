<?php

namespace App\Http\Requests;

use App\Enums\ComplaintStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ComplaintStatus::class)],
        ];
    }

    /**
     * Convert the validated string into the enum instance before the controller sees it.
     * Roughly the FastAPI equivalent of Pydantic coercing a str into an Enum field.
     */
    public function status(): ComplaintStatus
    {
        return ComplaintStatus::from($this->validated('status'));
    }
}
