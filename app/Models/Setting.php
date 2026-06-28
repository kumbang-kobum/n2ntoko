<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasActivityLog;
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    // Override all() mengembalikan Collection key=>value bukan Eloquent Collection.
    // Dipakai di SettingController, sales/show.blade.php, purchases/show.blade.php.
    // Gunakan allAsArray() jika ingin key-value array, atau parent::all() untuk Eloquent standar.
    public static function all($columns = ['*'])
    {
        return parent::all($columns)->pluck('value', 'key');
    }

    public static function allAsArray(): array
    {
        return parent::all(['key', 'value'])->pluck('value', 'key')->all();
    }
}
