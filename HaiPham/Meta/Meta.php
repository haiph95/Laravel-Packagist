<?php

namespace App\HaiPham\Meta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;

class Meta extends Model
{
    protected $fillable = [
        'metable_id', 'metable_type', 'key', 'value',
    ];

    public $timestamps = false;

    /**
     * Error message bag
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'metable_id'   => 'required|integer',
        'metable_type' => 'required',
        'key'          => 'required|max:100',
        'value'        => 'required',
    ];

    /**
     * Listen for save event
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function($model)
        {
            return $model->validate();
        });
    }

    /**
     * Validates current attributes against rules
     *
     * @return bool
     */
    public function validate()
    {
        $validator = Validator::make($this->attributes, static::$rules);
        if ($validator->passes()) {
            return true;
        }
        $this->setErrors($validator->messages());
        return false;
    }

    /**
     * Set error message bag
     *
     * @var MessageBag
     * @return void
     */
    protected function setErrors(MessageBag $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Retrieve error message bag
     *
     * @return MessageBag
     */
    public function getErrors()
    {
        return $this->hasErrors() ? $this->errors : new MessageBag;
    }

    /**
     * Check if a model has been saved
     *
     * @return boolean
     */
    public function isSaved()
    {
        return $this->hasErrors() ? false : true;
    }

    /**
     * Check if there are errors
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errors instanceof MessageBag;
    }

    /**
     * Connect the models
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function metable()
    {
        return $this->morphTo();
    }

    /**
     * Maybe decode a meta value
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return Helpers::maybeDecode($value);
    }

    /**
     * Maybe encode a value for saving
     *
     * @return null
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = Helpers::maybeEncode($value);
    }
}
