<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('user.edit');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150', "unique:users,email,{$userId}"],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama',
            'email'    => 'email',
            'password' => 'kata sandi',
            'role'     => 'role',
        ];
    }
}
