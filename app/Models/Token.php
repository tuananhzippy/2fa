<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    const EXPIRATION_TIME = 15; // minutes

    protected $fillable = [
        'code',
        'user_id',
        'used'
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode();
        }

        parent::__construct($attributes);
    }

    /**
     * Generate a six digits code
     *
     * @param int $codeLength
     * @return string
     */
    public function generateCode($codeLength = 6)
    {
        $min = pow(10, $codeLength);
        $max = $min * 10 - 1;
        $code = mt_rand($min, $max);

        return $code;
    }

    /**
     * User tokens relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True if the token is not used nor expired
     *
     * @return bool
     */
    public function isValid()
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }

    /**
     * Is the current token used
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * Is the current token expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->created_at->diffInMinutes(date('Y-m-d H:i:s')) > static::EXPIRATION_TIME;
    }

    public function sendCode($type = 'sms')
    {

        if (! $this->user) {
            throw new \Exception("No user attached to this token.");
        }

        if (! $this->code) {
            $this->code = $this->generateCode();
        }

        try {
            if($type === "sms") {
                app('twilio')->messages->create($this->user->getPhoneNumber(),
                ['from' => env('TWILIO_NUMBER'), 'body' => "Code is {$this->code}"]);
            } else if($type === "voice") {
                app('twilio')->calls->create($this->user->getPhoneNumber(), env('TWILIO_NUMBER'),
                ['url' => "https://demo.twilio.com/welcome/voice/"]);
            }
        } catch (\Exception $ex) {
            return false; //enable to send SMS
        }

        return true;
    }
}
