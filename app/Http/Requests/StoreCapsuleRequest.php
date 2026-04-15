<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCapsuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
            'unlock_date' => 'nullable|date|after_or_equal:today',
            'pin_code' => 'nullable|digits:4',
            'category' => 'nullable|in:memory,gift,mystery,game,anniversary,treasure',
            'is_anniversary' => 'nullable|boolean',
            'hint' => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $category = $this->input('category', 'memory');

            if ($category === 'anniversary' && !$this->filled('unlock_date')) {
                $validator->errors()->add('unlock_date', 'Yıldönümü kategorisinde açılış tarihi zorunludur.');
            }

            if ($category === 'mystery' && !$this->filled('pin_code')) {
                $validator->errors()->add('pin_code', 'Gizem kategorisinde PIN kodu zorunludur.');
            }

            if ($category === 'game') {
                if (!$this->filled('pin_code')) {
                    $validator->errors()->add('pin_code', 'Oyun kategorisinde PIN kodu zorunludur.');
                }

                if (!$this->filled('hint')) {
                    $validator->errors()->add('hint', 'Oyun kategorisinde görev/ipucu metni zorunludur.');
                }
            }

            if ($category === 'treasure') {
                if (!$this->filled('hint')) {
                    $validator->errors()->add('hint', 'Hazine kategorisinde ipucu alanı zorunludur.');
                }

                if (!$this->filled('unlock_date')) {
                    $validator->errors()->add('unlock_date', 'Hazine kategorisinde açılış tarihi zorunludur.');
                }
            }

            if ($category === 'gift' && !$this->filled('unlock_date') && !$this->filled('pin_code')) {
                $validator->errors()->add('unlock_date', 'Hediye kategorisinde tarih kilidi veya PIN zorunludur.');
                $validator->errors()->add('pin_code', 'Hediye kategorisinde tarih kilidi veya PIN zorunludur.');
            }
        });
    }
}
