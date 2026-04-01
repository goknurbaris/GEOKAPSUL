<?php

namespace App\Http\Requests;

use App\Models\Capsule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCapsuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
            'unlock_date' => 'nullable|date',
            'pin_code' => 'nullable|digits:4',
            'category' => 'nullable|in:memory,gift,mystery,game,anniversary,treasure',
            'is_anniversary' => 'nullable|boolean',
            'hint' => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var Capsule|null $capsule */
            $capsule = $this->route('capsule');
            $category = $this->input('category', $capsule?->category ?? 'memory');

            $hasUnlockDate = $this->has('unlock_date')
                ? $this->filled('unlock_date')
                : !empty($capsule?->unlock_date);

            $hasPin = $this->has('pin_code')
                ? $this->filled('pin_code')
                : !empty($capsule?->pin_code);

            $hasHint = $this->has('hint')
                ? $this->filled('hint')
                : !empty($capsule?->hint);

            if ($category === 'anniversary' && !$hasUnlockDate) {
                $validator->errors()->add('unlock_date', 'Yıldönümü kategorisinde açılış tarihi zorunludur.');
            }

            if ($category === 'treasure' && !$hasHint) {
                $validator->errors()->add('hint', 'Hazine kategorisinde ipucu alanı zorunludur.');
            }

            if ($category === 'gift' && !$hasUnlockDate && !$hasPin) {
                $validator->errors()->add('unlock_date', 'Hediye kategorisinde tarih kilidi veya PIN zorunludur.');
                $validator->errors()->add('pin_code', 'Hediye kategorisinde tarih kilidi veya PIN zorunludur.');
            }
        });
    }
}
