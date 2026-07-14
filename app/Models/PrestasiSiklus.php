<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiSiklus extends Model
{
    use HasFactory;

    protected $table = 'prestasi_siklus';

    protected $fillable = [
        'madrasah_id',
        'periode',
        'status',

        'submitted_at',
        'locked_at',
        'assessment_started_at',
        'finished_at',

        'submitted_by',
        'locked_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'locked_at' => 'datetime',
        'assessment_started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const OPEN = 'OPEN';

    public const SUBMITTED = 'SUBMITTED';

    public const LOCKED = 'LOCKED';

    public const ASSESSMENT = 'ASSESSMENT';

    public const FINISHED = 'FINISHED';

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER STATUS
    |--------------------------------------------------------------------------
    */

    public function isOpen(): bool
    {
        return $this->status === self::OPEN;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::SUBMITTED;
    }

    public function isLocked(): bool
    {
        return $this->status === self::LOCKED;
    }

    public function isAssessment(): bool
    {
        return $this->status === self::ASSESSMENT;
    }

    public function isFinished(): bool
    {
        return $this->status === self::FINISHED;
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION
    |--------------------------------------------------------------------------
    */

    public function canInput(): bool
    {
        return $this->status === self::OPEN;
    }

    public function canSubmit(): bool
    {
        return $this->status === self::OPEN;
    }

    public function canAssignAsesor(): bool
    {
        return $this->status === self::SUBMITTED;
    }

    public function canAssessment(): bool
    {
        return in_array($this->status, [
            self::LOCKED,
            self::ASSESSMENT,
        ]);
    }

    public function canFinish(): bool
    {
        return $this->status === self::ASSESSMENT;
    }
}