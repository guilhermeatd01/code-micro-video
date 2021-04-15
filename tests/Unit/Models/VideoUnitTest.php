<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use PHPUnit\Framework\TestCase;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoUnitTest extends TestCase
{
    private $video;

    public function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function testIfUseTraits()
    {
        $traits = [
            HasFactory::class, SoftDeletes::class, Uuid::class
        ];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration'
        ];
        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testCastsAttribute()
    {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer',
            'deleted_at' => 'datetime'
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->video->incrementing);
    }
}
